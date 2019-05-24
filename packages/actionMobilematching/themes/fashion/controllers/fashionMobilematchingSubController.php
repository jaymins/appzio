<?php

class fashionMobilematchingSubController extends MobilematchingController {


    public $mymatchtitleimage = 'my-matches-text.png';

    public $enable_advertising = false;

    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '180';
        $params['margin'] = '100 0 0 0';
        $params['priority'] = 9;

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('30');
        $row[] = $this->getText('{#there_are_no_matches_right_now#}',array('text-align' => 'center','font-ios' => 'Didot-Italic','font-android' => 'Didot','font-size' => 24,'margin' => '0 60 8 60'));
        $row[] = $this->getText('{#new_members_are_added_every_day#}',array('text-align' => 'center','font-size' => 14,'color' => '#A0A0A0'));
        $row[] = $this->getSpacer(20);
        /*
        $row[] = $this->getTextbutton('{#invite_friends#}',array('id' => '1234',
            'action' => 'open-action',
            'style' => 'general_button_style_red',
            'open_popup' => true,
            'config' => $this->getConfigParam('invite_action')));
        */

        $row[] = $this->getTextbutton(strtoupper('{#invite_friends#}'), array(
                'id' => '1234',
                'action' => 'share',
                'style' => 'didot_blue_button',
        ));


        $menu2 = new StdClass();
        $menu2->action = 'submit-form-content';
        $menu2->id = 12344;
        $row[] = $this->getText(strtoupper('{#scan_again#}'),array('style' => 'didot_hollow_button','onclick' => $menu2));

        $this->collectPushPermission();

        $this->setFooter();

/*        $img = $this->getImageFileName('match-bg.jpg');*/

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
/*            'background-image' => $img,
            'background-size'=>'cover'*/
        ));

    }


    public function collectPushPermission() {

        if ( !$this->sessionGet('push-permission-asked') OR $this->getSavedVariable('perm_push') == 0) {
            $pusher = $this->getOnclick('push-permission');
            $this->data->onload[] = $pusher;
            $this->sessionSet('push-permission-asked',true);
        }
    }



    public function getMyMatchItem($vars,$id,$search=false){

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        if(isset($vars['role']) AND $vars['role'] == 'brand' AND isset($vars['company']) AND $vars['company']){
            $name = $vars['company'];
        } else {
            $name = $this->getFirstName($vars);
            $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;
        }

        $textparams['onclick'] = new StdClass();
        $textparams['onclick']->action = 'open-action';
        $textparams['onclick']->id = $this->getTwoWayChatId($id,$this->current_playid);
        $textparams['onclick']->back_button = true;
        $textparams['onclick']->sync_open = true;
        $textparams['onclick']->sync_close = true;
        $textparams['onclick']->viewport = 'bottom';
        $textparams['onclick']->action_config = $this->requireConfigParam('chat');
        $textparams['style'] = 'imate_title';

        $image_onclick = new StdClass();
        $image_onclick->action = 'open-action';
        $image_onclick->id = $id;
        $image_onclick->back_button = true;
        $image_onclick->sync_open = true;
        $image_onclick->action_config = $this->requireConfigParam('detail_view');

        $unread = false;

        /* notification column */
        $contextkey = $this->getTwoWayChatId($id,$this->current_playid);
        $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey,'chat_user_play_id' => $this->current_playid));

        if(is_object($chat) AND isset($chat->chat_id)){
            $chatid = $chat->chat_id;
            $messages = Aechatmessages::model()->findBySql("SELECT * FROM ae_chat_messages WHERE chat_id = " .$chatid .' AND
             author_play_id = ' .$id .' ORDER BY id DESC');

            if(isset($messages->chat_message_timestamp)){
                $timestamp = strtotime($messages->chat_message_timestamp);
                $time = Controller::humanTiming($timestamp);

                if(isset($messages->chat_message_is_read) AND $messages->chat_message_is_read == 0){
                    $unread = true;
                }
            }
        }

        // Actual markup

        $left_col_rows[] = $this->getImage( 'overlay-white.png', array( 'crop' => 'round', 'floating' => '1', ) );
        $left_col_rows[] = $this->getImage($profilepic, array(
            'style' => 'round_image_imate',
            'priority' => 9,
            'onclick' => $image_onclick
        ));

        /* unread marker */
        if ( $unread ) {
            $left_col_rows[] = $this->getImage('red-dot.png',array('width' => '10', 'margin' => '5 10 0 0', 'float' => 'right', 'floating' => 1));
        }


        $right_col_rows[] = $this->getRow(array(
            $this->getText($name, $textparams),
        ));

        if ( isset($time) ) {
            $txt = ($time == '{#now#}') ? $time : $time .' ago';
            $right_col_rows[] = $this->getRow(array(
                $this->getText( '{#last_message#} - ' . $txt,array('style' => 'imate_title_msgtime')),
            ));
        }

        if ( $profiledescription ) {
            if ( strlen($profiledescription) > 35 ) {
                $profiledescription = $this->truncate_words( $profiledescription, 3 ) . '...';
            }

            $textparams['style'] = 'imate_title_subtext';

            $right_col_rows[] = $this->getRow(array(
                $this->getText($profiledescription, $textparams),
            ));
        }

        $col_left = $this->getColumn(
            $left_col_rows
            , array( 'width' => '23%', 'text-align' => 'center', ));

        $col_right = $this->getColumn(
            $right_col_rows
            , array( 'width' => '75%', 'vertical-align' => 'middle' ));

        $rowparams['margin'] = '5 10 5 10';

        if($search){
            $rowparams['margin'] = '0 25 0 8';
            $rowparams['background-color'] = $this->color_topbar;
        }

        // $rowparams['vertical-align'] = 'middle';
        $rowparams['height'] = '65';

        $this->data->scroll[] = $this->getRow(array(
            $col_left,
            $col_right
        ), $rowparams);
    }

    public function checkForPermission(){

        if($this->getSavedVariable('user_approved')){
            return true;
        } else {

            if($this->getConfigParam('actionimage1')){
                $this->data->scroll[] = $this->getImage($this->getConfigParam('actionimage1'));
            }

            $this->data->scroll[] = $this->getSpacer('150');
            $this->data->scroll[] = $this->getText('{#account_pending_for_admin_approval#}',array('style' => 'welcome_back_text'));
            $this->data->scroll[] = $this->getText('{#fill_your_profile_in_the_meanwhile#}',array('style' => 'welcome_back_text'));
            return false;
        }

    }

    public function showMatches($skipfirst=false){
        if(strstr($this->menuid,'delete-user-')){
            $delid = str_replace('delete-user-','',$this->menuid);
            UserGroupsUseradmin::model()->deleteByPk($delid);
        }

        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;

        // whether to do the opposite sex query
        $users = $this->mobilematchingobj->getUsersNearby($search_dist,'exclude',false,true);

        if($this->mobilematchingobj->debug){
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        $detail_view = $this->requireConfigParam('detail_view');
        
        $counter = 0;

        $swipestack = array();

        if(empty($users)){
            $this->notFound();
        } else {
            $vars = $this->getAMatch($users);
            $itemcount = 0;

            /* didn't find any users */
            if($vars == false){
                return false;
            }

            foreach($vars as $i => $one){

                $count = 2;
                $piccount = 1;

                if(!isset($one['play_id'])){
                    continue;
                }

                $id = $one['play_id'];

                while ($count < 10) {
                    $n = 'profilepic' . $count;
                    if (isset($one[$n]) AND strlen($one[$n]) > 2) {
                        $piccount++;
                    }
                    $count++;
                }

                $distance = round($one['distance'], 0);
                $profilepic = isset($one['profilepic']) ? $one['profilepic'] : false;
                $path = $_SERVER['DOCUMENT_ROOT'] .$profilepic;

                if($profilepic AND file_exists($path) AND filesize($path) > 40){
                    $filecheck = true;
                } else {
                    $path2 = Controller::getDocumentsFolder($this->gid);
                    $path2 = $path2.'/instagram/'.$one['profilepic'];

                    if($profilepic AND file_exists($path2) AND filesize($path2) > 40) {
                        $filecheck = true;
                    } else {
                        $filecheck = false;
                    }
                }

                $filter = $this->filter($one);

                if($filter AND $filecheck AND $itemcount < 20) {
                    if(!$skipfirst){

                        $rows[] = $this->getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i);
                        //$rows[] = $this->getImage('bottom-part.png',array('width' => '100%'));
                        $w = $this->screen_width*0.05;

/*                        $rows[] = $this->getRow($cont,array('text-align' => 'center',
                            'shadow-color' => '#66000000','shadow-radius' => '4','shadow-offset' => '0 0',
                            'background-color' => '#ffffff',
                            'margin' => "$w $w 0 $w"
                        ));*/

                        $width = $this->screen_width - $w;
                        $rows[] = $this->getImage('last-shadow.png',array('width' => $width,'margin' => "0 0 0 0"));
                        $rows[] = $this->getMatchingSpacer($id);
                        $rows[] = $this->getBtns($id,$detail_view,$i);

                        $swipestack[] = $this->getColumn($rows,array('text-align' => 'center'));
                        $itemcount++;

                        unset($page);
                        unset($rows);
                        unset($toolbar);
                        unset($column);
                        unset($cont);

                    } else {
                        $skipfirst = false;
                    }
                } else {
                    /* its important that we exclude non-matches as otherwise we will sooner or later
                    run out of matches as only the skipped get excluded by query, rest is handled here in php */

                    if(!$filter){
                        $this->addToDebug('Ignored '.$id .'filecheck:' .$filecheck .'filter:' .$filter);
                        $this->mobilematchingobj->skipMatch();
                    } else {
                        $this->addToDebug('Skipped for now '.$id .'filecheck:' .$filecheck .'filter:' .$filter);
                    }
                }

            }

            if(empty($swipestack)){
                $this->notFound();
                return false;
            }

            unset($col);
            $col[] = $this->swipe($swipestack);
            $this->data->scroll[] = $this->getRow($col,array('text-align' => 'center'
                //,'shadow-color' => '#66000000','shadow-radius' => '4','shadow-offset' => '0 0',
                //'background-color' => '#ffffff',
                //'margin' => "$w $w 0 $w"
                ));

            $this->setFooter();

        }
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){
        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->sync_open = 1;
        $onclick->back_button = 1;
        $onclick->id = $id;
        $onclick->action_config = $detail_view;

        //$page[] = $this->getText($this->aspect_ratio);

        if ( !$i ) {
            $onclick->context = 'profile-' .$id;
        }

        $width = $this->screen_width*0.9;
        $margin = $this->screen_width*0.1;

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 800;
        $options['imgheight'] = 800;
        $options['width'] = $width;
        $options['margin'] = '0 0 0 0';
        $options['imgcrop'] = 'yes';
        //$options['border-color'] =  '#000000';
        $options['crop'] = 'yes';
        $options['priority'] = '9';

        if($profilepic){
            $profilepic = $this->getImage($profilepic,$options);
        } else {
            $profilepic = $this->getImage('anonymous2.png',$options);
        }

        $page[] = $profilepic;

        $name = isset($one['real_name']) ? $one['real_name'] : '{#anonymous#}';
        if ( isset($one['company']) AND !empty($one['company']) ) {
            $name = $one['company'];
        }

        $page[] = $this->getText($name, array(
            'font-ios' => 'Didot',
            'font-android' => 'Didot',
            'color' => '#000000',
            'font-size' => '22',
            'text-align' => 'center',
            'white-space' => 'no-wrap',
            'height' => '34',
            'vertical-align' => 'center',
        ));

        $followers = isset($one['instagram_followed_by']) ? $one['instagram_followed_by'] : '< 1000';
        $width = $width-20;

        $plip[] = $this->getImage('plip4.png',array('height' => '1', 'vertical-align' => 'middle','padding' => '0 0 0 10','width' => $width*0.25));
        $plip[] = $this->getText($followers . ' {#followers#}', array(
            'font-ios' => 'Didot',
            'font-android' => 'Didot',
            'color' => '#9b9b9b',
            'font-size' => '14',
            'text-align' => 'center',
            'white-space' => 'no-wrap',
            'padding' => '0 4 0 4',
            'margin' => '-8 0 0 0',
            'width' => $width * 0.5,
        ));
        $plip[] = $this->getImage('plip4.png',array('height' => '1', 'vertical-align' => 'middle','padding' => '0 10 0 0','width' => $width*0.25));
        $page[] = $this->getRow($plip, array(
            'width' => $width,
            'text-align' => 'center',
            'vertical-align' => 'middle',
            'height' => '42',
            'margin' => '0 0 0 0',
            'padding' => '0 0 8 0'
        ));

        $margin = ($this->screen_width - $width) / 2;

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'background-color' => '#ffffff',
            'rightswipeid' => 'right' . $id,
            'margin' => "$margin 0 0 0",
            'shadow-color' => '#66000000','shadow-radius' => '3','shadow-offset' => '0 0',
            'width' => $width
        ));
        
        $output[] = $this->getImage('bottom-part.png',array('width' => '100%'));

        return $this->getColumn($output,array(
            //'shadow-color' => '#66000000','shadow-radius' => '4','shadow-offset' => '0 0',
            'margin' => "$margin $margin $margin $margin"));
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
            'margin' => '0 0 0 0',
            //'width' => $this->screen_width - $this->screen_width*0.2,
            'overlay_left' => $this->getImage($nope,array('text-align' => 'right','width'=> '200','height'=> '200')),
            'overlay_right' => $this->getImage($like,array('text-align' => 'left','width'=> '200','height'=> '200'))
        ));
    }

    public function filter($one){


        if($this->filterByDistance($one) == false){
            $this->addToDebug('Filtered by distance');
            return false;
        }

        if($this->filterByInstaCount($one) == false AND $this->getSavedVariable('role') == 'brand'){
            $this->addToDebug('Filtered by insta count');
            return false;
        }

        if($this->filterByCountry($one) == false){
            $this->addToDebug('Filtered by country');
            return false;
        }

        return true;

    }

    public function filterByCountry($one){

        if($this->getSavedVariable('filter_country') == 'All' OR !$this->getSavedVariable('filter_country')){
            return true;
        }

        $countries = array_flip(json_decode($this->getSavedVariable('filter_country'),true));

        if(isset($one['country'])){
            $usercountry = $one['country'];
            if(isset($countries[$usercountry])){
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
        
    }

    public function filterByInstaCount($one){
        $followers = $this->getSavedVariable('filter_followers') ? $this->getSavedVariable('filter_followers') : 1;

        return true;
        print_r($one);die();
        if(isset($one['instagram_followed_by']) AND $followers < $one['instagram_followed_by']){
            return true;
        } else {
            return false;
        }
    }


    public function itsAMatch($id=false){
        //$this->data->scroll[] = $this->getImage('redbg.png');

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
        $params['priority'] = 9;

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
            $row[] = $this->getImage($match_img, $params);
        }

        $textstyle['margin'] = '30 0 0 0';
        $textstyle['color'] = '#000000';
        $textstyle['text-align'] = 'center';
        $textstyle['font-ios'] = 'Didot';
        $textstyle['font-android'] = 'Didot';
        $textstyle['font-size'] = '28';

        $row[] = $this->getText('{#its_a_match#}!',$textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '106';
        $params['text-align'] = 'center';
        $params['border-width'] = '10';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '53';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic,$params);
        $pics[] = $this->getImage($this->getVariable('profilepic'),$params);

        $row[] = $this->getRow($pics,array('margin' => '80 30 0 30','text-align' => 'center'));

        $row[] = $this->getSpacer('50');

        $row[] = $this->getTextbutton(strtoupper('{#send_a_message#}'),array('id' => $this->getTwoWayChatId($id),'style' => 'didot_blue_button',
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid));


        $row[] = $this->getSpacer(15);
        $row[] = $this->getTextbutton(strtoupper('{#keep_playing#}'),array('id' => 'keep-playing','style' => 'didot_hollow_button'));

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'height' => '100%'
        ));

        $this->setFooter();
    }


    public function getBtns($id,$detail_view,$i){
        //$column[] = $this->getImagebutton('fashion-reload-4.png', 'refresh', false, array('width' => '25%', 'margin' => '0 0 0 0'));
        $column[] = $this->getImagebutton('fashion-skip.png', 'no_' . $id, false, array('width' => '20%', 'margin' => '0 0 0 0','action' => 'swipe-left'));
        $column[] = $this->getVerticalSpacer('15');
        $column[] = $this->getImagebutton('fashion-like.png', 'yes_' . $id, false, array('width' => '20%','action' => 'swipe-right'));

        $args = array(
            'width' => '25%',
            'sync_open' => '1',
            'action' => 'open-action',
            'config' => $detail_view,
        );

        if ( !$i ) {
            $args['context'] = 'profile-' . $id;
        }

        //$column[] = $this->getImagebutton('fashion-info-4.png', $id, false, $args);
        return $this->getRow($column, array('align' => 'center', 'text-align' => 'center','noanimate' => true));
    }


    public function getFirstName($vars){
        
        if ( isset($vars['company']) AND !empty($vars['company']) ) {
            return $vars['company'];
        }

        if ( isset($vars['real_name']) AND !empty($vars['real_name']) ) {
            $firstname = explode(' ', trim($vars['real_name']));
            $firstname = $firstname[0];
            return $firstname;
        }

        return false;
    }

    public function myMatches(){

        $this->configureBackground('actionimage3');

        if($this->getConfigParam('mymatches_search_withscreenname')){
            $this->screenNameSearch();
        }

        $matches = $this->mobilematchingobj->getMyMatches();

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $this->data->scroll[] = $this->getImage($this->getConfigParam( 'actionimage1' ));
        } else {
            //$this->data->scroll[] = $this->getSettingsTitle('{#my_matches#}');
            $this->data->scroll[] = $this->getSpacer(20);
        }

        $this->matchList($matches);

        if($this->getConfigParam('mymatches_show_group_chats')){
            $params['mode'] = 'mychats';
            $params['expended_connection_ids'] = $this->expended_connection_ids;
            $params['allow_delete'] = false;
            $this->data->scroll[] = $this->getSettingsTitle('{#group_chats#}');
            $this->data->scroll[] = $this->moduleGroupChatList($params);
        }

        if($this->getConfigParam('mymatches_show_incoming_requests')){
            $matches = $this->mobilematchingobj->getMyInbox();
            $this->data->scroll[] = $this->getSettingsTitle('{#inbox#} ({#valid_for_24h#})');
            $this->matchList($matches);
        }

        if($this->getConfigParam('mymatches_show_outgoing_requests')){
            $matches = $this->mobilematchingobj->getMyOutbox();
            $this->data->scroll[] = $this->getSettingsTitle('{#outbox#} ({#valid_for_24h#})');
            $this->matchList($matches);
        }

        /*        if($this->getConfigParam('main_view')){
                    $this->data->scroll[] = $this->getSpacer('40');
                    $this->data->scroll[] = $this->getTextbutton('{#match_more_people#}',array(
                        'style' => 'general_button_style',
                        'action' => 'open-action',
                        'config' => $this->getConfigParam('main_view')));
                }*/


    }





}