<?php

class dittoMobilematchingSubController extends MobilematchingController {


    public $mymatchtitleimage = 'my-matches-text.png';

    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '230';
        $params['margin'] = '30 0 0 0';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('20');

        $row[] = $this->getText('{#sorry_no_one_new#}',array('style' => 'register-text-step-2'));
        $row[] = $this->getSpacer(20);

        $row[] = $this->getTextbutton('{#invite_friends#}', array(
                'id' => '1234',
                'action' => 'share',
                'style' => 'ditto-matching-btn',
        ));

        $menu = $this->getPushPermissionMenu();
        $menu2 = new StdClass();
        $menu2->action = 'submit-form-content';
        $menu2->id = 12344;

        if($menu){
            $row[] = $this->getText('{#scan_again#}',array('style' => 'ditto-matching-btn','onclick' => $menu));
        } else {
            $row[] = $this->getText('{#scan_again#}',array('style' => 'ditto-matching-btn-hollow','onclick' => $menu2));
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
        $textstyle['color'] = '#ffffff';
        $textstyle['text-align'] = 'center';

        $row[] = $this->getText('{#you_and#} ' .$this->getFirstName($vars) .' {#like_each_other#}',$textstyle);

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

        $row[] = $this->getTextbutton('{#send_a_message#}',array('id' => $this->getTwoWayChatId($id),'style' => 'ditto-matching-btn',
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));


        $row[] = $this->getSpacer(15);
        $row[] = $this->getTextbutton('{#keep_playing#}',array('id' => 'keep-playing','style' => 'ditto-matching-btn-hollow'));

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'height' => '100%'
        ));

        $this->setFooter();
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
            'margin' => '20 20 0 20',
            'overlay_left' => $this->getImage($nope,array('text-align' => 'right','width'=> '400','height'=> '400')),
            'overlay_right' => $this->getImage($like,array('text-align' => 'left','width'=> '400','height'=> '400'))
        ));
    }

    public function getBtns($id,$detail_view,$i){
        $column[] = $this->getImagebutton('ditto-switch.png', 'refresh', false, array('width' => '25%', 'margin' => '0 0 0 0'));
        $column[] = $this->getImagebutton('ditto-dislike.png', 'no_' . $id, false, array('width' => '20%', 'margin' => '15 5 0 10','action' => 'swipe-left'));
        $column[] = $this->getImagebutton('ditto-like.png', 'yes_' . $id, false, array('width' => '20%', 'margin' => '15 10 0 5', 'action' => 'swipe-right'));

        $args = array(
            'width' => '25%',
            'sync_open' => '1',
            'action' => 'open-action',
            'config' => $detail_view,
        );

        if ( !$i ) {
            $args['context'] = 'profile-' . $id;
        }

        $column[] = $this->getImagebutton('ditto-info.png', $id, false, $args);

        return $this->getRow($column, array('align' => 'center', 'noanimate' => true));
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){
        $onclick = new StdClass();
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
        $options['imgcrop'] = 'yes';
        $options['crop'] = 'yes';
        $options['priority'] = '9';

        if($profilepic){
            $profilepic = $this->getImage($profilepic,$options);
        } else {
            $profilepic = $this->getImage('anonymous2.png',$options);
        }

        $page[] = $profilepic;

        $texparam['font-ios'] = 'Roboto-Light';
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
        $texparam['font-size'] = 14;
        $texparam['text-align'] = 'left';
        $texparam['font-ios'] = 'Roboto-Regular';

        $toolbar[] = $this->getImage('icon-location-marker.png',array('width' => '20', 'margin' => '0 5 0 5'));
        $toolbar[] = $this->getText($distance . ' km', $texparam);
        $toolbar[] = $this->getImage('icon-persona.png', array('width' => '20', 'margin' => '0 5 0 5'));

        $toolbar[] = $this->getText($this->mobilematchingobj->getNumberOfMatches($id), $texparam);
        $toolbar[] = $this->getImage('icon-camera.png', array('width' => '20', 'margin' => '0 5 0 5'));
        $toolbar[] = $this->getText($piccount, $texparam);
        $page[] = $this->getRow($toolbar, array('margin' => '8 10 10 10'));

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'background-color' => '#ba5678',
            'rightswipeid' => 'right' . $id
        ));


    }

}