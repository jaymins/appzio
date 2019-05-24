<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class fashionMobileuserprofileSubController extends MobileuserprofileController {

    public $availability;
    public $mobileplacesobj;


    public function showProfileView(){

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);
        Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

        $this->data->scroll[] = $this->getImageScroll($vars);
        //$this->data->scroll[] = $this->getRow($top,array('background-color' => '#ffffff','text-align' => 'center'));

        $txt = $this->getFirstName($vars);
        if ( isset($vars['company']) AND !empty($vars['company']) ) {
            $txt = $vars['company'];
        }
        
        $city = isset($vars['city']) ? $this->localizationComponent->smartLocalize('{#'. $vars['city'] .'#}' ) : '{#hidden_city#}';
        $country = isset($vars['country']) ? $this->localizationComponent->smartLocalize('{#'. $vars['country'] .'#}' ) : '{#hidden_country#}';

        $txt2 = $city;
        if ( $country ) {
            $txt2 .= ', ' . $country;
        }

        $this->data->scroll[] = $this->getText('‹ ' .strtoupper('{#instagram_verified#}') .' ›',array('text-align' => 'center','padding' => '8 8 8 8','font-size' => '10',
            'background-color' => '#d7d7d7'));

        if(isset($vars['instagram_username'])){
        }

        $this->data->scroll[] = $this->getSettingsTitle('{#about#}');

        $user[] = $this->getText($txt,array('height' => '30', 'margin' => '15 20 0 20','font-size' => '22'));
        $user[] = $this->getText($txt2,array('height' => '30', 'margin' => '0 23 0 20', 'font-size' => '16'));
        $this->data->scroll[] = $this->getColumn($user, array( 'vertical-align' => 'middle' ));

        if(!isset($vars['real_name'])){
            $this->data->scroll[] = $this->getText('{#info_missing#}');
            return true;
        }

        $this->profileCommentView($vars);
        $this->facebookFriends($vars);

        if(isset($vars['website'])){

            if(stristr($vars['website'],'http')){
                $url = $vars['website'];
            } else {
                $url = 'http://'.$vars['website'];
            }

            $click = new stdClass();
            $click->action = 'open-url';
            $click->action_config = $url;

            $this->data->scroll[] = $this->getSettingsTitle('{#website#}');
            $this->data->scroll[] = $this->getText(strtolower($vars['website']),array('margin' => '5 23 0 20', 'font-size' => '16','onclick' => $click));
        }

        $this->instaInfo($vars);


        $ref_name = '';
        $cache_name = 'current-user-branch-' . $this->playid;
        $cached_value = Appcaching::getGlobalCache( $cache_name );

        if ( isset($_REQUEST['referring_branch']) ) {
            Appcaching::setGlobalCache( $cache_name, 'ref-my-matches' );
            $ref_name = 'ref-my-matches';
        } else if ( $cached_value ) {
            $ref_name = $cached_value;
        }

        $id = 'report';
        if ( $ref_name ) {
            $id = 'report|' . $ref_name;
        }

        // $this->data->footer[] = $this->getText( $id );
        $this->data->scroll[] = $this->getSpacer('20');
        $this->data->scroll[] = $this->getSettingsTitle('{#report_inappropriate#}');

        $this->data->scroll[] = $this->getTextbutton('{#report_user#}', array( 'id' => 'report', 'style' => 'report-user-button' ));

        if ($this->menuid == 'report') {
            $this->data->scroll[] = $this->getText( '{#user_reported#}', array( 'text-align' => 'center', 'padding' => '0 5 8 5', 'font-size' => '13' ));
        }

        $this->data->scroll[] = $this->getSpacer('20');


    }

    public function showProfileEdit(){

        $this->setGridWidths();
        $this->profileEditSaves();

        /* set the screen pixels */
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if($this->menuid == 'del-images'){
            $user[] = $this->getText('{#click_on_images_to_delete#}',array('vertical-align' => 'top','font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        } else {
            /* top part */
            $txt = $this->getVariable('real_name');

            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

            $txt2 = ThirdpartyServices::translateString( $this->getVariable('city'), 'en', $tr_lang );
            if ( $this->getVariable('country') ) {
                $txt2 .= ', ' . ThirdpartyServices::translateString( $this->getVariable('country'), 'en', $tr_lang );
            }

            $user[] = $this->getText($txt, array('font-size' => '22','font-ios' => 'Didot','font-android' => 'Didot'));
            $user[] = $this->getText($txt2, array('font-size' => '12'
                //'font-ios' => 'Didot','font-android' => 'Didot'
            ));

            $this->data->scroll[] = $this->getColumn($user,array('margin' => $total_margin, 'vertical-align' => 'middle'));
        }


        $this->data->scroll[] = $this->getGrid();

        //$titlecol[] = $this->getImage('toolbar-info.png',array('width' => '30','height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#about_me#}',array('height' => '30', 'margin' => '10 0 0 0','font-size' => '16','font-ios' => 'Didot','font-android' => 'Didot'));

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        if($this->getSavedVariable('profilepic2')) {
            $titlecol[] = $this->getImage('del-photos.png', array('height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }
        $this->data->scroll[] = $this->getRow($titlecol,array('margin' => '10 '.$this->margin.' 0 '.$this->margin));

        $this->data->scroll[] = $this->getTextArea();
        $this->data->scroll[] = $this->getText('{#saving_can_take_a_while#}',array('margin' => '10 24 10 24','text-align' => 'center','font-size' => 14));
        $this->data->scroll[] = $this->saveButton();

    }

    public function instaInfo($vars){

        if(!isset($vars['instagram_username'])){
            return false;
        }

        $username = isset($vars['instagram_username']) ? $vars['instagram_username'] : '{#unknown#}';
        $mediacount = isset($vars['instagram_media_count']) ? $vars['instagram_media_count'] : '{#unknown#}';
        $followers = isset($vars['instagram_followed_by']) ? $vars['instagram_followed_by'] : '{#unknown#}';

        $this->data->scroll[] = $this->getSettingsTitle('{#instagram_info#}');
        $this->data->scroll[] = $this->getText('{#name#}: ' .$username, array('style' => 'profile_info_fields'));
        $this->data->scroll[] = $this->getText('{#followers#}: ' .$followers, array('style' => 'profile_info_fields'));
        $this->data->scroll[] = $this->getText('{#photos#}: ' .$mediacount, array('style' => 'profile_info_fields'));

        if(isset($vars['instagram_bio']) AND strlen($vars['instagram_bio'] > 5)){
            $this->data->scroll[] = $this->getSettingsTitle('{#instagram_bio#}');
            $this->data->scroll[] = $this->getText('username:' .$username);
        }

        $link = new stdClass();
        $link->action = 'open-url';
        $link->open_popup = 1;
        $link->action_config = 'https://www.instagram.com/' .$username;

        $this->data->scroll[] = $this->getSpacer('15');
        $this->data->scroll[] = $this->getTextbutton('{#open_instagram_profile#}',array('onclick' => $link,'style' => 'general_button_style_red','id' => 'whatever'));





    }


    public function getImageScroll($vars){

        if(!isset($vars['profilepic'])){
            return $this->getText('{#no_profile_pic#}');
        }

        $count = 1;
        $params['imgwidth'] = '600';
        $params['imgheight'] = '600';
        $params['imgcrop'] = 'yes';
        $params['height'] = $this->screen_width;
        $params['width'] = $this->screen_width;
        $params['not_to_assetlist']  = true;
        $params['priority'] = 9;
        //$params['mask'] = 'mask-sample.png';

        $swipnavi['margin'] = '-35 0 0 0';
        $swipnavi['align'] = 'center';
        $totalcount = 1;

        while($count < 10){
            $n = 'profilepic' .$count;
            if(isset($vars[$n])){
                $path = $_SERVER['DOCUMENT_ROOT'] .$vars[$n];
            }

            if(isset($vars[$n]) AND strlen($n) > 2 AND isset($path) AND file_exists($path) AND filesize($path) > 40){
                $totalcount++;
            }
            $count++;
        }

        $count = 1;
        $mycount = 1;

        $scroll[] = $this->getImage($vars['profilepic'],$params);
        if($totalcount > 1){
            $scroll[] = $this->getSwipeNavi($totalcount,1,$swipnavi);
        }
        $item[] = $this->getColumn($scroll,array('width' => '100%'));
        unset($scroll);

        while($count < 10){
            $n = 'profilepic' .$count;
            if(isset($vars[$n]) AND strlen($n) > 2){
                $path = $_SERVER['DOCUMENT_ROOT'] .$vars[$n];

                if(file_exists($path) AND filesize($path) > 40){
                    $mycount++;
                    $scroll[] = $this->getImage($vars[$n],$params);
                    $scroll[] = $this->getSwipeNavi($totalcount,$mycount,$swipnavi);
                    $item[] = $this->getColumn($scroll,array());
                    unset($scroll);
                }
            }
            $count++;
        }

        return $this->getSwipearea($item);
    }

    public function places($vars){

        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#clubs_played#}', array('height' => '30', 'margin' => '10 0 0 0'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));
        $this->data->scroll[] = $this->getSpacer('10');

        Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->profileid;
        $this->mobileplacesobj->game_id = $this->gid;
        $this->mobileplacesobj->places_wantsee = json_decode($vars['places_wantsee'],true);
        $this->mobileplacesobj->places_havebeen = json_decode($vars['places_havebeen'],true);

        $wordlist = $this->mobileplacesobj->getMyClubs();

        if(!empty($wordlist)){
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        }
        
        $titlecol2[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol2[] = $this->getText('{#clubs_want_to_visit#}', array('height' => '30', 'margin' => '10 0 0 0'));
        $this->data->scroll[] = $this->getRow($titlecol2, array('width' => '100%', 'margin' => '10 0 0 12'));
        $this->data->scroll[] = $this->getSpacer('10');

        $wordlist = $this->mobileplacesobj->getMyWishlist();

        foreach($wordlist as $word){
            $this->setPlaceSimple($word);
        }
    }


    public function homeClub($vars)
    {

        $club_name = '{#no_home_club_set#}';

        if(isset($vars['home_club'])){
            $clubinfo = MobileplacesModel::model()->findByPk($vars['home_club']);

            if(isset($clubinfo->name)){
                $club_name = $clubinfo->name;
            }
        }

        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#home_club#} : ' . $club_name, array('height' => '30', 'margin' => '10 0 0 0','font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));

    }


    public function hcp($vars)
    {
        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#HCP#} : ' . $vars['hcp'], array('height' => '30', 'margin' => '10 0 0 0','font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));

    }

    public function availabilityView($vars){
        $titlecol[] = $this->getImage('calendar-icon-availability2.png',array('width' => '30','height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#general_availability_for#} '.$this->getFirstName($vars),array('height' => '30', 'margin' => '10 0 0 0','font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol,array('width' => '100%','margin' => '10 0 0 12'));

        $this->availability = json_decode($vars['availability'],true);
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getDayMorningAfternoon('monday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('tuesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('wednesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('thursday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('friday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('saturday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('sunday');
        $this->data->scroll[] = $this->getSpacer('10');
    }

    public function getDayMorningAfternoon($daytitle){
        $col[] = $this->getText('{#'.$daytitle.'#}',array('style' => 'selector_daytitle'));

        $varname = $daytitle.'_morning';
        if(isset($this->availability[$varname])){
            $col[] = $this->getText('{#morning#}',array('style' => 'selector_day_selected'));
        } else {
            $col[] = $this->getText('{#morning#}',array('style' => 'selector_day'));
        }

        $varname = $daytitle.'_afternoon';
        if(isset($this->availability[$varname])){
            $col[] = $this->getText('{#afternoon#}',array('style' => 'selector_day_selected'));
        } else {
            $col[] = $this->getText('{#afternoon#}',array('style' => 'selector_day'));
        }

        return $this->getRow($col,array('margin' => '4 15 4 10'));
    }


    public function setPlaceSimple($data){
        if(!isset($data['logo']) OR $data['logo'] == 'dummylogo.png'){
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $col[] = $this->getImage($data['logo'],array('width' => '15%','vertical-align' => 'middle'));
        $col[] = $this->getPlaceRowPart($data,'100%');
        $this->data->scroll[] = $this->getRow($col,array('margin' => '0 15 2 15','padding' => '5 5 5 5'));
    }

    public function getPlaceRowPart($data,$width='55%'){
        $distance = round($data['distance'],0) .'km';
        $id = $data['id'];

        $openinfo = new StdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        $row[] = $this->getText($data['name'],array('background-color' => '#ffffff','padding' => '3 5 3 5','color' => '#000000','font-size' => '12'));
        $row[] = $this->getText($data['county'],array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        $row[] = $this->getText($data['city'].', ' .$distance,array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        return $this->getColumn($row,array('width' => $width,'onclick'=>$openinfo));
    }

}