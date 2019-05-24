<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');


class golfMobilematchingSubController extends MobilematchingController {


    public $mymatchtitleimage = 'my-matches-text.png';


    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '180';
        $params['margin'] = '100 0 0 0';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('30');

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

        $menu2 = new StdClass();
        $menu2->action = 'submit-form-content';
        $menu2->id = 12344;
        $row[] = $this->getText('{#scan_again#}',array('style' => 'general_button_style_red','onclick' => $menu2));



        $this->setFooter();

/*        $img = $this->getImageFileName('match-bg.jpg');*/

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
/*            'background-image' => $img,
            'background-size'=>'cover'*/
        ));

    }

    public function collectPushPermission() {
        $this->askPushPermission();
    }

    public function activateLocationTracking(){
        $this->askLocation();
        $this->askMonitorRegion($this->getConfigParam('monitor_region'));
    }

    public function otherAppInstalled(){

        $cache = Appcaching::getGlobalCache('golfizzschemecheck-'.$this->playid.$this->userid);

        /* check if the other app is installed */
        if(!$this->getSavedVariable('otherapp_installed') AND !$cache){
            $checker = new stdClass();
            $checker->action = 'check-scheme';
            $checker->variable = 'otherapp_installed';
            $checker->action_config = 'golfizz://';
            $this->data->onload[] = $checker;
            Appcaching::setGlobalCache('golfizzschemecheck-'.$this->playid.$this->userid,true,120);
        }

    }

    public function configureTop($id){
        $this->configureBackground('actionimage2');

        $cachepointer = $this->current_playid .'-matchid';
        Appcaching::setGlobalCache($cachepointer,$id);

        if(!$id){
            $this->initMobileMatching();
            $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $params['margin'] = '30 0 0 0';
        $params['priority'] = '9';

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
            $row[] = $this->getImage($match_img, $params);
        } else {
            $textstyle['margin'] = '30 0 0 0';
            $textstyle['color'] = '#000000';
            $textstyle['text-align'] = 'center';
            $textstyle['font-ios'] = 'PoiretOne-Regular';
            $textstyle['font-android'] = 'PoiretOne-Regular';
            $textstyle['font-size'] = '38';

            $row[] = $this->getSpacer(20);
            $row[] = $this->getText('{#its_a_match#}!',$textstyle);
        }

        $textstyle['margin'] = '0 0 0 0';
        $textstyle['color'] = '#000000';
        $textstyle['text-align'] = 'center';

        //$row[] = $this->getText('{#you_and#} ' .$this->getFirstName($vars) .' {#like_each_other#}',$textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '106';
        $params['text-align'] = 'center';
        $params['border-width'] = '3';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '53';
        $params['priority'] = '9';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        if(isset($vars['home_club']) AND $vars['home_club']){
            $obj = MobileplacesModel::model()->findByPk($vars['home_club']);
            if(is_object($obj) AND isset($obj->logo)){
                $logo = $obj->logo;
            }
        }

        $pic1[] = $this->getImage($profilepic,$params);

        if(isset($logo)){
            $pic1[] = $this->getImage($logo,array('width' => '80','align' => 'center','margin' => '8 0 0 0'));
        } else {
            $pic1[] = $this->getText('{#no_club#}',array('font-size' => '10','width' => '80','text-align' => 'center', 'margin' => '8 0 0 0','border-width' => '1',
                'border-color' => '#000000','border-radius' => '4','padding' => '3 3 3 3'));
        }

        unset($logo);

        if($this->getSavedVariable('home_club')){
            $obj = MobileplacesModel::model()->findByPk($this->getSavedVariable('home_club'));
            if(is_object($obj) AND isset($obj->logo)){
                $logo = $obj->logo;
            }
        }

        $pic2[] = $this->getImage($this->getVariable('profilepic'),$params);

        if(isset($logo)){
            $pic2[] = $this->getImage($logo,array('width' => '80','align' => 'center', 'margin' => '8 0 0 0'));
        } else {
            $pic2[] = $this->getText('{#no_club#}',array('font-size' => '10','width' => '80','text-align' => 'center', 'margin' => '8 0 0 0','border-width' => '1',
                'border-color' => '#000000','border-radius' => '4','padding' => '3 3 3 3'));
        }


        $pics[] = $this->getColumn($pic1,array('text-align' => 'center','width' => '120'));
        $pics[] = $this->getColumn($pic2,array('text-align' => 'center','width' => '120'));

        $row[] = $this->getRow($pics,array('margin' => '30 30 0 30','text-align' => 'center'));

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'height' => '100%'
        ));

    }


    public function itsAMatch($id=false){

        $this->otherAppInstalled();
        $this->configureTop($id);
        $chatid = $this->requireConfigParam('chat');
        $this->askOtherAppInstalled('golfizz://','otherapp_installed');

        if($this->getSavedVariable('otherapp_installed')){
            $row[] = $this->getSpacer('50');

            $row[] = $this->getTextbutton('{#send_a_message#}',array('id' => $this->getTwoWayChatId($id,$this->current_playid),'style' => 'general_button_style_red',
                'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid));
            $url = 'golfizz://open?action_id=' .$this->getConfigParam('action_id_golfizzmain') .'&menuid=' .'newround-otherapp';

            $onclick2 = new stdClass();
            $onclick2->id = 'link';
            $onclick2->action = 'open-url';
            $onclick2->sync_open = 1;
            $onclick2->action_config = $url;

            $row[] = $this->getTextbutton('{#setup_a_round#}',array('id' => 'some','onclick' => $onclick2,'style' => 'general_button_style_red',
                'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid));

            $row[] = $this->getTextbutton('{#find_more_matches#}',array('id' => 'keep-playing','style' => 'general_button_style_red'));

            $this->data->scroll[] = $this->getColumn($row,array(
                //'background-color' => '#e33124',
                //'height' => '100%'
            ));
        } else {

            if($this->getSavedVariable('system_source') == 'client_iphone') {
                $openurl = 'itms://itunes.apple.com/us/app/apple-store/id1153433528?mt=8';
            } else {
                $openurl = 'market://details?id=com.appzio.golfizz';
            }

            $btn[] = $this->getImage('golfizz-logo.png',array('margin' => '20 80 10 80','text-align' => 'center','height' => '45'));
            $btn[] = $this->getText('{#download_golfizz#}',array('color' => '#ffffff','text-align' => 'center','margin' => '0 0 10 0'));
            $this->data->scroll[] = $this->getText('{#explanation_why_you_should_install_another_app#}',array('margin' => '20 40 10 40','text-align' => 'center','font-size' => 14));

            $this->data->scroll[] = $this->getColumn($btn,array('border-radius' => '4','background-color' => '#459a35',
                'margin' => '10 40 10 40',
                'onclick' => $this->getOnclick('url', false,$openurl)));


            $row[] = $this->getSpacer(15);
            $this->data->scroll[] = $this->getTextbutton('{#not_yet#}, {#find_more_matches#}',array('id' => 'keep-playing','style' => 'general_button_style_red'));

            $checker = new stdClass();
            $checker->action = 'check-scheme';
            $checker->variable = 'otherapp_installed';
            $checker->action_config = 'golfizz://';
            $row[] = $this->getSpacer(15);
            $this->data->scroll[] = $this->getTextbutton('{#i_have_it_installed#}',array('id' => 'keep-playing','onclick' => $checker, 'style' => 'general_button_style_red'));
        }

        $this->setFooter();
    }

    public function swipe($swipestack){
        $nope = 'nope.png';
        $like = 'like2.png';

        if ( $this->getConfigParam( 'actionimage3' ) ) {
            $nope = $this->getConfigParam( 'actionimage3' );
        }

        if ( $this->getConfigParam( 'actionimage4' ) ) {
            $like = $this->getConfigParam( 'actionimage4' );
        }

        return $this->getSwipestack($swipestack,array(
            'margin' => '20 20 0 20',
            'overlay_left' => $this->getImage($nope,array('text-align' => 'right','width'=> '400','height'=> '400')),
            'overlay_right' => $this->getImage($like,array('text-align' => 'left','width'=> '400','height'=> '400'))
        ));
    }

    public function getMatchingSpacer($id){
        if($this->getSavedVariable('is_admin') == 1){
            $menu1['action'] = 'submit-form-content';
            $menu1['id'] = 'delete-user-'.$id;
            $menu2['action'] = 'swipe-left';
            return $this->getImage('delete-icon.png',array('width' => 35,'float' => 'center','margin' => '10 0 0 0','onclick' => array($menu2,$menu1)));
        } elseif($this->aspect_ratio > 0.57){
            return $this->getSpacer('9');
        } else {
            return $this->getSpacer('9');
        }
    }

    public function getBtns($id,$detail_view,$i){
        //$column[] = $this->getImagebutton('golf-reload.png', 'refresh', false, array('width' => '25%', 'margin' => '0 0 0 0'));

        $args = array(
            'width' => '20%',
            'sync_open' => '1',
            'action' => 'open-action',
            'height' => 'auto',
            'margin' => '38 0 0 0',
            'vertical-align' => 'middle',
            'config' => $detail_view,
        );

        if ( !$i ) {
            $args['context'] = 'profile-' . $id;
        }

        $column[] = $this->getImagebutton('golf2-no.png', 'no_' . $id, false, array('width' => '30%', 'margin' => '0 0 0 0','action' => 'swipe-left'));
        $column[] = $this->getImagebutton('golf3-info.png', $id, false, $args);
        $column[] = $this->getImagebutton('golf2-yes.png', 'yes_' . $id, false, array('width' => '30%','action' => 'swipe-right'));
        return $this->getRow($column, array('align' => 'center', 'text-align' => 'center','noanimate' => true,'vertical-align' => 'middle'));
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){
        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->sync_open = 1;
        $onclick->back_button = 1;
        $onclick->id = $id;
        $onclick->action_config = $detail_view;

        if ( !$i ) {
            $onclick->context = 'profile-' .$id;
        }

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 600;
        $options['imgheight'] = 600;
        $options['width'] = $this->screen_width - 28;
        $options['height'] = $this->screen_width - 28;

        $options['imgcrop'] = 'yes';
        $options['crop'] = 'yes';
        $options['priority'] = '9';

        if($profilepic){
            $profilepic = $this->getImage($profilepic,$options);
        } else {
            $profilepic = $this->getImage('anonymous2.png',$options);
        }

        $page[] = $profilepic;

        $texparam['color'] = '#ffffff';
        $texparam['background-color'] = '#80000000';

        $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

        $city = isset($one['city']) ? ThirdpartyServices::translateString( $one['city'], 'en', $tr_lang ) : '';
        $texparam['font-size'] = 26;
        $texparam['margin'] = '-38 0 0 0';
        $texparam['padding'] = '3 4 4 4';
        $texparam['text-align'] = 'center';
        $texparam['white-space'] = 'no-wrap';

        $text = $this->getFirstName($one) . ', ' . $city;

        if(strlen($text) > 23) {
            $texparam['font-size'] = 16;
            $texparam['padding'] = '10 4 9 4';
        } elseif(strlen($text) > 17){
            $texparam['font-size'] = 18;
            $texparam['padding'] = '8 4 8 4';
        }

        $page[] = $this->getText($text, $texparam);

        unset($texparam['background-color']);
        unset($texparam['padding']);
        $texparam['margin'] = '3 5 0 5';
        $texparam['font-size'] = 12;
        $texparam['text-align'] = 'left';

        $toolbar[] = $this->getImage('icon-location-marker.png',array('width' => '20'));
        $toolbar[] = $this->getText($distance . ' km', $texparam);
        $toolbar[] = $this->getImage('icon-persona.png', array('width' => '20', 'margin' => '0 5 0 5'));
        $toolbar[] = $this->getText($this->mobilematchingobj->getNumberOfMatches($id), $texparam);
        $toolbar[] = $this->getImage('icon-camera.png', array('width' => '20', 'margin' => '0 5 0 5'));
        $toolbar[] = $this->getText($piccount, $texparam);

        if(isset($one['hcp'])){
            $toolbar[] = $this->getImage('hcp-filled-icon.png', array('width' => '20', 'margin' => '0 5 0 5'));
            $toolbar[] = $this->getText('{#hcp#}: ' .$one['hcp'], $texparam);
        }

        $page[] = $this->getRow($toolbar, array('margin' => '8 10 10 10','vertical-align' => 'middle'));

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'background-color' => '#34a343',
            'rightswipeid' => 'right' . $id
        ));


    }


    public function getUsersNearby(){
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $users = $this->mobilematchingobj->getUsersNearby($search_dist,'exclude',false);

        if($this->mobilematchingobj->debug){
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        return $users;
    }



}