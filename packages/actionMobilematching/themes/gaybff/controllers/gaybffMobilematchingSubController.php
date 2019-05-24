<?php

class gaybffMobilematchingSubController extends MobilematchingController {

    public $mymatchtitleimage = 'my-matches-text.png';

    /* get the users, put the layout code in place */
    public function showMatches($skipfirst=false){
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $users = $this->mobilematchingobj->getUsersNearby($search_dist, 'exclude', false, false, false, $this->units);

        if($this->mobilematchingobj->debug){
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        if(empty($users)){
            $this->notFound();
            return false;
        } else {
            $swipestack = $this->buildSwipeStack($users,$skipfirst);
        }

        if(empty($swipestack)){
            $this->notFound();
        } else {
            $this->data->scroll[] = $this->swipe($swipestack);
            $this->setFooter();
        }
    }

    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '120';
        $params['margin'] = '100 0 0 0';
        $params['priority'] = '9';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');

        $row[] = $this->getText('{#sorry_no_one_new#}',array('style' => 'register-text-step-2'));
        $row[] = $this->getSpacer(20);
        /*
        $row[] = $this->getTextbutton('{#invite_friends#}',array('id' => '1234',
            'action' => 'open-action',
            'style' => 'general_button_style_red',
            'open_popup' => true,
            'config' => $this->getConfigParam('invite_action')));
        */

        $row[] = $this->getTextbutton('{#invite_friends#}', array(
                'id' => '1234',
                'action' => 'share',
                'style' => 'general_button_style_red',
        ));

        $menu = $this->getPushPermissionMenu();
        $menu2 = new StdClass();
        $menu2->action = 'submit-form-content';
        $menu2->id = 12344;

        if($menu){
            $row[] = $this->getText('{#scan_again#}',array('style' => 'general_button_style_red','onclick' => $menu));
        } else {
            $row[] = $this->getText('{#scan_again#}',array('style' => 'general_button_style_red','onclick' => $menu2));
        }


        $this->setFooter();

        /* $img = $this->getImageFileName('match-bg.jpg');*/

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
/*            'background-image' => $img,
            'background-size'=>'cover'*/
        ));

    }

    public function itsAMatch($id=false){
        $this->configureBackground('actionimage2');

        $cachepointer = $this->playid .'-matchid';
        Appcaching::setGlobalCache($cachepointer,$id);

        if(!$id){
            $this->initMobileMatching();
            $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';
        $params['priority'] = '9';

        $match_img = 'its-a-match2.png';

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
        }

        $row[] = $this->getImage($match_img, $params);

        $textstyle['font-ios'] = 'Roboto-Regular';
        $textstyle['margin'] = '0 0 0 0';
        $textstyle['color'] = '#000000';
        $textstyle['text-align'] = 'center';

        $row[] = $this->getText('{#you_and#} ' .$this->getFirstName($vars) .' are a match!',$textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '106';
        $params['text-align'] = 'center';
        $params['border-width'] = '3';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '53';
        $params['priority'] = '9';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic,$params);
        $pics[] = $this->getImage($this->getVariable('profilepic'),$params);

        $row[] = $this->getRow($pics,array('margin' => '80 30 0 30','text-align' => 'center'));

        $row[] = $this->getSpacer('50');

        $row[] = $this->getTextbutton('{#send_a_message#}',array('id' => $this->getTwoWayChatId($id),'style' => 'general_button_style_red',
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));


        $row[] = $this->getSpacer(15);
        $row[] = $this->getTextbutton('{#keep_searching#}',array('id' => 'keep-playing','style' => 'general_button_style_red'));

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'height' => '100%'
        ));

        $this->setFooter();
    }

    public function getBtns($id,$detail_view,$i){

        $btn_no_img = 'btn2-no2.png';
        if ( $this->getConfigParam( 'actionimage5' ) ) {
            $btn_no_img = $this->getConfigParam( 'actionimage5' );
        }

        $btn_yes_img = 'btn3-yes2.png';
        if ( $this->getConfigParam( 'actionimage6' ) ) {
            $btn_yes_img = $this->getConfigParam( 'actionimage6' );
        }
        
        $column[] = $this->getImagebutton('btn1-refresh2.png', 'refresh', false, array('width' => '25%', 'margin' => '0 0 0 0'));
        
        $column[] = $this->getImagebutton($btn_no_img, 'no_' . $id, false, array('width' => '25%', 'margin' => '0 0 0 0','action' => 'swipe-left'));
        
        $column[] = $this->getImagebutton($btn_yes_img, 'yes_' . $id, false, array('width' => '25%','action' => 'swipe-right'));

        $args = array(
            'width' => '25%',
            'sync_open' => '1',
            'action' => 'open-action',
            'config' => $detail_view,
        );

        if ( !$i ) {
            //$args['context'] = 'profile-' . $id;
        }

        $column[] = $this->getImagebutton('btn4-info2.png', $id, false, $args);
        return $this->getRow($column, array('align' => 'center', 'noanimate' => true));
    }

    public function filterBySex($one,$opposites=false){
        $my_gender_pref = $this->getFormattedArray( 'gender_preferences' );
        $my_sexual_orientation_pref = $this->getFormattedArray( 'sexual_preferences' );
        $my_transgender_pref = $this->getSavedVariable( 'transgender_preferences' );

        if ( !isset($one['gender_preferences']) OR !isset($one['sexual_preferences']) ) {
            return true;
        }

        $person_gender = ( isset($one['gender']) ? strtolower($one['gender']) : false );
        $person_sexual_orientation = ( isset($one['sexual_orientaion']) ? strtolower($one['sexual_orientaion']) : false );
        $person_is_trasgender = ( isset($one['is_transgender']) ? $one['is_transgender'] : 'Yes' );

        $has_gender_match = false;
        $has_sexual_match = false;
        $has_transgender_match = true;

        if ( in_array($person_gender, $my_gender_pref) ) {
            $has_gender_match = true;
        }

        if ( in_array($person_sexual_orientation, $my_sexual_orientation_pref) ) {
            $has_sexual_match = true;
        }

        if ( $my_transgender_pref != 'No preference' ) {
            if ( $my_transgender_pref != $person_is_trasgender ) {
                $has_transgender_match = false;
            }
        }

        if ( !$has_gender_match OR !$has_sexual_match OR !$has_transgender_match ) {
            return false; // Means the person was filtered
        }

        return true;
    }

    public function getFormattedArray( $variable ) {
        $data = @json_decode( $this->getSavedVariable( $variable ), true );

        if ( empty($data) ) {
            return false;
        }

        $data = array_change_key_case($data, CASE_LOWER);
        $data = array_map('strtolower', $data);

        return $data;
    }

    public function getMatchText( $name ) {
        return '{#you_have_a_new_match_with#} ' . $name . '!';
    }
    
}