<?php

class matchswappMobilematchingSubController extends MobilematchingController {


    public $mymatchtitleimage = 'my-matches-text.png';


    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '120';
        $params['margin'] = '100 0 0 0';

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

        $match_img = 'its-a-match2.png';

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
        }

        $row[] = $this->getImage($match_img, $params);

        $textstyle['font-ios'] = 'Roboto-Regular';
        $textstyle['margin'] = '0 0 0 0';
        $textstyle['color'] = '#000000';
        $textstyle['text-align'] = 'center';

        $row[] = $this->getText('{#you_and#} ' .$this->getFirstName($vars) .' {#like_each_other#}',$textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '106';
        $params['text-align'] = 'center';
        $params['border-width'] = '3';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '53';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic,$params);
        $pics[] = $this->getImage($this->getVariable('profilepic'),$params);

        $row[] = $this->getRow($pics,array('margin' => '80 30 0 30','text-align' => 'center'));

        $row[] = $this->getSpacer('50');

        $row[] = $this->getTextbutton('{#send_a_message#}',array('id' => $this->getTwoWayChatId($id),'style' => 'general_button_style_red',
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));


        $row[] = $this->getSpacer(15);
        $row[] = $this->getTextbutton('{#keep_playing#}',array('id' => 'keep-playing','style' => 'general_button_style_red'));

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
            $args['context'] = 'profile-' . $id;
        }

        $column[] = $this->getImagebutton('btn4-info2.png', $id, false, $args);
        return $this->getRow($column, array('align' => 'center', 'noanimate' => true));
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $detail_view;
        $onclick->sync_open = 1;
        $onclick->id = $id;

        if ( !$i ) {
            //$onclick->context = 'profile-' . $id;
        }

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 600;
        $options['imgheight'] = 600;
        $options['imgcrop'] = 'yes';
        $options['margin'] = '10 10 5 10';
        $options['priority'] = '9';
        $options['crop'] = 'yes';

        /* look at function swipe() for main margins */
        $options['width'] = $this->screen_width - 80;
        $options['height'] = $this->screen_width - 80;

        if($profilepic){
            $profilepic = $this->getImage($profilepic,$options);
        } else {
            $profilepic = $this->getImage('anonymous2.png',$options);
        }

        $page[] = $profilepic;

        $texparam['font-size'] = 13;

        $city = isset($one['city']) ? $one['city'] .', ' : '';
        $age = isset($one['age']) ? ', ' . $one['age'] : '';

        $toolbar[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getText($this->getFirstName($one), array( 'font-size' => 13, 'font-style' => 'bold' )),
                $this->getText($age, array( 'font-size' => 13 ))
            ), array( 'width' => '100%' )),
            $this->getText($city . $distance . ' ' . $this->units, array( 'width' => '100%', 'font-size' => 13 )),
        ), array(
            'width' => '70%',
            'vertical-align' => 'middle',
        ));

        $toolbar[] = $this->getColumn(array(
            $this->getRow(array(
                // $this->getImage('toolbar-friends.png', array('width' => '20', 'margin' => '0 5 0 5')),
                // $this->getText($this->mobilematchingobj->getNumberOfMatches($id), $texparam),
                $this->getImage('toolbar-photos.png', array('width' => '20', 'margin' => '0 5 0 5')),
                $this->getText($piccount, $texparam),
            ), array(
                'text-align' => 'right',
            )),
        ), array(
            'width' => '30%',
            'text-align' => 'right',
        ));

        $page[] = $this->getRow($toolbar, array(
            'margin' => '0 10 0 10',
            'height' => '40',
            'vertical-align' => 'middle',
        ));

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'rightswipeid' => 'right' . $id,
            'background-color' => '#ffffff',
            //'width' => '100%',
            'padding' => '5 5 5 5',
            'text-align' => 'center',
            'shadow-color' => '#66000000',
            'shadow-radius' => '4','shadow-offset' => '0 0',
            'width' => $this->screen_width-50,
        ));
    }

    public function swipe($swipestack){
        $nope = 'nope.png';
        $like = 'like.png';

        if ( $this->getConfigParam( 'actionimage3' ) ) {
            $nope = $this->getConfigParam( 'actionimage3' );
        }

        if ( $this->getConfigParam( 'actionimage4' ) ) {
            $like = $this->getConfigParam( 'actionimage4' );
        }

        return $this->getSwipestack($swipestack,array(
            'margin' => '20 10 0 10',
            'overlay_left' => $this->getImage($nope,array('text-align' => 'right','width'=> '400','height'=> '400')),
            'overlay_right' => $this->getImage($like,array('text-align' => 'left','width'=> '400','height'=> '400'))
        ));
    }
    
}