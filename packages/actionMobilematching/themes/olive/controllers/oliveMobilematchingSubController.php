<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class oliveMobilematchingSubController extends MobilematchingController {

    public $enable_advertising = false;

    public $current_item_id;

    // maximum items per load
    public $max_items = 30;
    public $showing_search_results;


    public function tab1(){

        $this->searchActions();

        if ( $this->getSavedVariable( 'new_registration' ) ) {
            $this->deleteVariable( 'new_registration' );
            $this->deleteVariable( 'temp_interests' );
            $this->loadVariableContent();
        }

        if ( $this->getSavedVariable('temp_interests') OR $this->getSavedVariable('temp_gendersearch') ) {
            $this->showing_search_results = true;
        }

        $this->data = new StdClass();

        // $this->configureBackground( 'actionimage10' );
        if(strstr($this->menuid,'delete-user-')){
            $delid = str_replace('delete-user-','',$this->menuid);
            UserGroupsUseradmin::model()->deleteByPk($delid);
        }

        $this->activateLocationTracking();
        if(!$this->checkForPermission()){
            return $this->data;
        }

        if(isset($this->params['swid'])){
            $this->current_item_id = $this->params['swid'];
            Appcaching::setGlobalCache($this->playid.'currentitemid',$this->current_item_id);
        } else {
            $this->current_item_id = Appcaching::getGlobalCache($this->playid.'currentitemid');
        }

        $this->askPermissions();

        Aenotification::addUserNotification( $this->current_playid, '', '', '0', $this->current_gid );

        // Rewrite the action's config
        $this->rewriteActionConfigField('mobile_sharing', 1);

        if ( $share_url = $this->getConfigParam( 'share_url' ) ) {
            $this->rewriteActionConfigField('share_description', $share_url);
        }

        if ( $share_title = $this->getConfigParam( 'share_title' ) ) {
            $this->rewriteActionConfigField('share_title', $share_title);
        }

        $mode = $this->requireConfigParam('mode');
        $cachepointer = $this->current_playid .'-matchid';
        $cache = Appcaching::getGlobalCache($cachepointer);
        $regphase = $this->getSavedVariable('reg_phase');

        if ( $mode == 'matching' ) {
            $this->getHeader(1);
        }

        if($regphase != 'complete' AND $this->getConfigParam('register_branch') AND !$this->getSavedVariable('fb_token')){
            $this->loadBranchList();
            $branch = $this->getConfigParam('register_branch');

            if(isset($this->available_branches[$branch])){
                $reg = new StdClass();
                $reg->sync_open = 1;
                $reg->id = 'register';
                $reg->action = 'open-branch';
                $reg->action_config = $branch;

                $this->data->scroll[] = $this->getSpacer('40');
                $this->data->scroll[] = $this->getText('{#finish_registration_first#}',array('style' => 'imate_title'));
                $this->data->scroll[] = $this->getSpacer('40');
                $this->data->scroll[] = $this->getText('{#continue_registration#}',array('style' => 'general_button_style_red','onclick' => $reg));
                return $this->data;
            }
        }

        if($this->menuid == 'keep-playing'){
            Appcaching::removeGlobalCache($cachepointer);
            $cache = false;
        }

        // Delete the current-user-branch cache
        // This is used under the My Matches section
        $branch_cache_name = 'current-user-branch-' . $this->current_playid;
        Appcaching::removeGlobalCache($branch_cache_name);

        if(strstr($this->menuid,'markread-')) {
            $id = str_replace('markread-', '', $this->menuid);
            $this->initMobileMatching($id);
            $this->mobilematchingobj->resetNotifications();
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $this->initMobileMatching($cache);

        if($cache AND $mode == 'matching'){
            $this->itsAMatch($cache);
            return $this->data;
        }

        switch ($mode) {
            case 'my_matches':

                $this->myMatches();

                break;
            case 'my_matches_new_messages':

                $this->myMatchesMessages();

                break;
            case 'my_invites':

                $matches = $this->mobilematchingobj->getMyInbox();
                $this->data->scroll[] = $this->getSpacer( 15 );
                $this->matchListInvites($matches);

                break;
            default:

                if(isset($this->submit['swid'])){
                    $swid = $this->submit['swid'];

                    // Open interstitials
                    if($this->enable_advertising == true){
                        $this->openInterstitial();
                    }

                    if(strstr($swid,'left')) {
                        $id = str_replace('left', '', $swid);
                        $this->initMobileMatching($id);
                        $this->mobilematchingobj->skipMatch();
                        $this->matches();
                    } elseif(strstr($swid,'right')){
                        $id = str_replace('right', '', $swid);
                        $this->initMobileMatching($id);

                        $this->mobilematchingobj->send_accept_push = false;

                        if($this->mobilematchingobj->saveMatch() == true){
                            $this->ismatch = true;
                            $this->triggerConfirmationPush( $id );
                            $this->itsAMatch($id);
                        } else {
                            $this->showMatches();
                        }
                    } else {
                        $this->matches();
                    }

                } else {
                    $this->matches();
                }

                $this->setFooter();

                break;
        }

        return $this->data;
    }


    public function askPermissions() {

        if ( $this->userid != $this->getSavedVariable('push_permission_checker') ) {

            $onload = new StdClass();
            $onload->id = 'push-permission';
            $onload->action = 'push-permission';
            $this->data->onload[] = $onload;

            $this->saveVariable('push_permission_checker', $this->userid);
        }

    }


    public function tab2() {

        $this->searchActions();

        // $this->configureBackground( 'actionimage11' );
        // $this->rewriteActionField('background_image_portrait', '40f4e79cad9a95e231ffb91981680e508d77a6aa116391e2125d7cb76dcb19ae.png');

        $this->data = new stdClass();

        if($this->current_tab != 2){
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $this->initMobileMatching();
        $this->initMobileChat(false,false);
        $this->mobilechatobj->chat_sorting = 'olive';

        $this->getHeader(2);
        $this->data->scroll[] = $this->getSettingsTitle('{#public_chats#}', false, false);

        $chats = $this->moduleGroupChatList(array(
            'mode' => 'public_chats',
            'show_users_count' => true,
            'show_chat_tags' => true,
            'allow_delete' => false,
            'return_array' => true,
            'separator_styles' => $this->getChatSeparatorStyles(),
            'filter' => json_decode($this->getSavedVariable('temp_interests'),true),
            'filter_distance' => $this->getSavedVariable('temp_distance')
        ));

        if ( count($chats) == 1 AND !is_array($chats) ) {
            $this->data->scroll[] = $chats;
        } else {
            
            foreach ($chats as $row){
                $this->data->scroll[] = $row;
            }

        }

        $onclick = new StdClass();
        $onclick->id = 'new-group-chat';
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam( 'action_id_groupchats' );
        $onclick->sync_open = 1;
        $onclick->back_button = 1;

        $this->data->footer[] = $this->getTextbutton('{#add_a_new_chat#}', array(
            'style' => 'olive-submit-button',
            'id' => 'new-chat',
            'submit_menu_id' => 'new-chat',
            'onclick' => $onclick,
        ));

        if($this->showing_search_results){
            $this->searchFooter();
        }

        return $this->data;
    }


    public function searchActions(){

        if($this->menuid == 'cancel-search'){
            $this->deleteVariable('temp_interests');
            $this->deleteVariable('temp_gendersearch');
            $this->loadVariableContent(true);
        }
    }


    public function matches(){

        if($this->menuid == 'no'){
            if(isset($this->params['swid'])){
                $this->skip($this->params['swid']);
            }
            return true;
        }

        if($this->menuid == 'yes'){
            if(isset($this->params['swid'])){
                $this->doMatch($this->params['swid']);
            }

            return true;
        }

        $this->showMatches();

    }

    public function doMatch($id=false){

        if($id){
            $this->mobilematchingobj->initMatching($id);
        }

        $this->mobilematchingobj->send_accept_push = false;

        if($this->mobilematchingobj->saveMatch() == true){
            $this->triggerConfirmationPush( $id );
            $this->itsAMatch();
        } else {

            // Begin matching - so send a push for a Match Invite
            $text = 'New Olive invitation';
            $description = 'You have received new invitation. Go and check it out!';
            Aenotification::addUserNotification( $id, $text, $description, '0', $this->gid );

            $this->no_output = true;
            return true;
        }
    }

    public function skip($id=false){
        if($id){
            $this->mobilematchingobj->initMatching($id);
        }

        $this->mobilematchingobj->skipMatch();
        $this->no_output = true;
        return true;
    }


    public function showMatches($skipfirst=false){

        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;

        $interests_varname = $this->showing_search_results ? 'temp_interests' : 'interests';

        $strict = $this->showing_search_results ? true : false;
        $users = $this->mobilematchingobj->oliveQuery($search_dist,$interests_varname,$this->getVariableId('interests'),$strict);

        if($this->mobilematchingobj->debug){
            $this->addToDebug($this->mobilematchingobj->debug);
        }
        
        if(empty($users)){
            $this->notFound();
            return false;
        } else {

            /* if its a search view, we are injecting variable values for gender selection */
            if($this->showing_search_results){
                $genders = json_decode($this->getSavedVariable('temp_gendersearch'),true);

                if(!empty($genders)){
                    $this->varcontent['men'] = $genders['men'];
                    $this->varcontent['women'] = $genders['women'];
                } else {
                    $this->varcontent['men'] = 1;
                    $this->varcontent['women'] = 1;
                }
            }

            $swipestack = $this->buildSwipeStack($users,$skipfirst,false);
        }

        if(empty($swipestack)){
            $this->notFound();
            return false;
        } else {
            if($this->screen_width / $this->screen_height < 0.6){
                $height = $this->screen_width + 65;
            } else {
                $height = round($this->screen_height/3,0) + 110;
            }

            $this->data->scroll[] = $this->getSwipearea($swipestack,array(
                'id' => 'mainswipe','item_scale' => 1, 'dynamic' => 1,'item_width' => '95%',
                //'animation' => 'nudge',
                'remember_position' => 1,'transition' => 'tablet','world_ending' => 'refill_items',
                'height' => $height));
            $this->data->scroll[] = $this->getBtns(1,$this->requireConfigParam('detail_view'),1);
        }

    }

    public function getBtns($id,$detail_view,$i){

        $menu = new stdClass();
        $menu->action = 'swipe-delete';
        $menu->container_id = 'mainswipe';
        $menu->id = 'no';
        $menu->send_ids = 1;

        $col_left = $this->getColumn(array(
            $this->getImage('btn-does-not-like.png', array('priority' => '1',  'margin' => '0 0 0 50','onclick' => $menu,'send_ids' => 1))
        ), array( 'width' => '38%', 'vertical-align' => 'middle' ));

        $col_center = $this->getColumn(array(
            $this->getImagebutton('btn-info.png', $id, false, array('priority' => '1', 'margin' => '0 5 0 5', 'action' => 'open-action', 'config' => $detail_view,'sync_open' => 1,'send_ids' => 1))
        ), array( 'width' => '24%', 'vertical-align' => 'middle' ));

        $menu->id = 'yes';
        $col_right = $this->getColumn(array(
            $this->getImage('btn-like.png', array('margin' => '0 50 0 0', 'priority' => '1', 'onclick' => $menu))
        ), array( 'width' => '38%', 'vertical-align' => 'middle' ));

        return $this->getRow(array(
            $col_left, $col_center, $col_right
        ), array('text-align' => 'center', 'vertical-align' => 'center', 'noanimate' => true));
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $detail_view;
        $onclick->sync_open = 1;
        $onclick->id = $id;

        if ( !$i ) {
            $onclick->context = 'profile-' . $id;
        }

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 600;
        $options['imgheight'] = 600;
        $options['imgcrop'] = 'yes';
        $options['margin'] = '10 10 5 10';
        $options['priority'] = '9';
        $options['crop'] = 'yes';
        $options['width'] = $this->screen_width - 80;

        if($this->screen_width / $this->screen_height < 0.6){
            $options['height'] = $this->screen_width - 80;
        } else {
            $options['height'] = round($this->screen_height/3,0);
        }

        if($profilepic){
            $profilepic = $this->getImage($profilepic,$options);
        } else {
            $profilepic = $this->getImage('anonymous2.png',$options);
        }

        //$page[] = $this->getText($this->screen_width / $this->screen_height);

        $page[] = $profilepic;

        $city = isset($one['city']) ? $one['city'] .', ' : '';

        $name[] = $this->getText($this->getFirstName($one), array(
            'font-size' => 14
        ));

        $page[] = $this->getRow($name, array(
            'margin' => '5 10 5 10',
            'vertical-align' => 'middle',
            'height' => '20'
        ));

        $toolbar[] = $this->getImage('icon-location.png', array('width' => '18', 'margin' => '0 5 0 0'));
        $toolbar[] = $this->getText($city . $distance . ' km', array(
            'font-size' => 12
        ));

        $toolbar[] = $this->getImage('icon-likes.png', array(
            'width' => '23',
            'floating' => 1,
            'margin' => '0 12 0 0',
            'float' => 'right',
        ));

        $toolbar[] = $this->getText($this->getInterestsCount( $one ), array(
            'font-size' => 12,
            'floating' => 1,
            'float' => 'right',
            'height' => '20'
        ));

        $page[] = $this->getRow($toolbar, array(
            'margin' => '5 10 10 10',
            'vertical-align' => 'middle'
        ));

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'background-color' => '#ffffff',
            'width' => $this->screen_width - 60,
            'shadow-color' => '#33000000','shadow-radius' => '3','shadow-offset' => '0 0',
            'rightswipeid' => 'right' . $id
        ));
    }

    public function getInterestsCount( $vars ) {
        if ( !isset($vars['interests']) OR
            empty($vars['interests']) OR
            !isset($this->varcontent['interests']) OR
            empty($this->varcontent['interests'])
        ) {
            return '0';
        }

        $my_interests = json_decode( $this->varcontent['interests'], true );
        $user_interests = json_decode( $vars['interests'], true );

        $tmp_my_interests = array();
        $tmp_user_interests = array();

        foreach ($my_interests as $mi_key => $mi_value) {
            if ( $mi_value ) {
                $tmp_my_interests[] = $mi_key;
            }
        }

        foreach ($user_interests as $ui_key => $ui_value) {
            if ( $ui_value ) {
                $tmp_user_interests[] = $ui_key;
            }
        }

        $common = array_intersect( $tmp_my_interests, $tmp_user_interests );

        if ( $common ) {
            return count( $common );
        }

        return '0';
    }

    public function activateLocationTracking(){

        $cache = Appcaching::getGlobalCache($this->playid.'-locationtracking');
        if($cache){
            return true;
        }

        // $menu2 = new StdClass();
        // $menu2->action = 'ask-location';
        // $this->data->onload[] = $menu2;
        // $this->getNearbyCities(true);

        $params['lat'] = $this->getSavedVariable('lat');
        $params['lon'] = $this->getSavedVariable('lon');
        $params['gid'] = $this->gid;
        $params['playid'] = $this->playid;

        Aetask::registerTask($this->playid,'update:cities',json_encode($params),'async');
    }

    public function itsAMatch($id=false){
        $this->configureBackground('actionimage2');

        $cachepointer = $this->playid .'-matchid';
        Appcaching::setGlobalCache($cachepointer,$id);

        if(!$id){
            $id = $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';

        //$match_img = 'its-a-match.png';

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
            $row[] = $this->getImage($match_img, $params);
        }


        $textstyle['margin'] = '-20 0 0 70';
        $textstyle['color'] = '#ffffff';
        $row[] = $this->getText('{#you_and#} ' . $this->getFirstName($vars) .' {#like_each_other#}',$textstyle);

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

        $row[] = $this->getRow($pics,array('margin' => '30 30 0 30','text-align' => 'center'));

        $row[] = $this->getSpacer('50');
        $row[] = $this->getTextbutton('Start a conversation',array('style' => 'general_button_style','id' => $this->getTwoWayChatId($id),
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));
        $textstyle['margin'] = '15 0 10 0';
        $textstyle['text-align'] = 'center';
        $row[] = $this->getText('or',$textstyle);
        $row[] = $this->getTextbutton('Keep surfing',array('style' => 'general_button_style','id' => 'keep-playing'));
        $row[] = $this->getSpacer('50');

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'background-image' => $this->getImage('no-match-bg.png')
        ));

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
            $this->data->scroll[] = $this->getSettingsTitle('{#my_buddies#}', false, false);
        }

        if ( $this->menuid == 'dosearch' ) {
            $matches = $this->getSearchUsers();
            $search_q = 'Search results for: ' . $this->submitvariables['searchterm'];
            $this->data->scroll[] = $this->getText($search_q, array( 'style' => 'olive-search-q' ));
        }

        $this->matchList($matches, false, '{#no_buddies#}');

        // if($this->getConfigParam('mymatches_show_group_chats')){
        //     $params['mode'] = 'mychats';
        //     $params['expended_connection_ids'] = $this->expended_connection_ids;
        //     $this->data->scroll[] = $this->getSettingsTitle('{#group_chats#}', false, false);
        //     $this->data->scroll[] = $this->moduleGroupChatList($params);
        // }
    }

    public function myMatchesMessages(){

        $this->configureBackground('actionimage3');

        $matches = $this->mobilematchingobj->getMyMatches();
        $filtered_matches = $this->getMatchesWithMessages( $matches );

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $this->data->scroll[] = $this->getImage($this->getConfigParam( 'actionimage1' ));
        } else {
            $this->data->scroll[] = $this->getSettingsTitle('{#my_unread_messages#}', false, false);
        }

        $this->matchList($filtered_matches, false, '{#no_unread_messages#}');
    }

    public function getMatchesWithMessages( $matches ){

        if ( empty($matches) ) {
            return false;
        }

        $users = array();

        foreach ($matches as $match_id) {
            $contextkey = $this->getTwoWayChatId($match_id, $this->current_playid);
            $chat = Aechatusers::model()->findByAttributes(array( 'context_key' => $contextkey ));
            // $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey, 'chat_user_play_id' => $this->current_playid));

            if ( empty($chat) ) {
                continue;
            }

            $chatid = $chat->chat_id;

            $messages = Aechatmessages::model()->findBySql("SELECT * FROM ae_chat_messages WHERE chat_id = " . $chatid . ' AND
             author_play_id = ' . $match_id .' ORDER BY id DESC');

            if(isset($messages->chat_message_is_read) AND $messages->chat_message_is_read == 0){
                $users[] = $match_id;
            }

        }

        return $users;
    }

    public function matchListInvites( $matches ){

        if ( empty($matches) ) {
            $othertxt['style'] = 'imate_title_nomatch';
            $this->data->scroll[] = $this->getText('{#no_invites_yet#}', $othertxt);
        }

        $this->handleMatchInviteAction();

        foreach ($matches as $key => $res) {
            $vars = AeplayVariable::getArrayOfPlayvariables($res);

            if ( empty($vars) ) {
                continue;
            }

            if (!isset($vars['profilepic'])) {
                continue;
            }

            $this->getMyMatchItemInvites($vars,$res);
        }

    }

    public function getMyMatchItemInvites($vars,$id,$search=false){
        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        $name = $this->getFirstName($vars);
        $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;

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

        // Actual markup

        $left_col_rows[] = $this->getImage($profilepic, array(
            'style' => 'profile-image-olive',
            'priority' => 9,
            'onclick' => $image_onclick
        ));

        $right_col_rows[] = $this->getRow(array(
            $this->getText($name, array( 'style' => 'imate_title' )),
        ));

        if ( $profiledescription ) {
            if ( strlen($profiledescription) > 35 ) {
                $profiledescription = $this->truncate_words( $profiledescription, 3 ) . '...';
            }

            $right_col_rows[] = $this->getRow(array(
                $this->getText($profiledescription, array( 'style' => 'imate_title_subtext' )),
            ));
        }

        $onclick_accept = new StdClass();
        $onclick_accept->action = 'submit-form-content';
        $onclick_accept->id = 'accept-user-' . $id;

        $onclick_decline = new StdClass();
        $onclick_decline->action = 'submit-form-content';
        $onclick_decline->id = 'decline-user-' . $id;

        $right_col_rows[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText('{#accept#}', array( 'style' => 'olive-match-button' )),
            ), array( 'width' => '50%', 'onclick' => $onclick_accept )),
            $this->getColumn(array(
                $this->getText('{#decline#}', array( 'style' => 'olive-match-button' )),
            ), array( 'width' => '50%', 'onclick' => $onclick_decline ))
        ));

        $col_left = $this->getColumn(
            $left_col_rows
            , array( 'width' => '33%', 'text-align' => 'center', ));

        $col_right = $this->getColumn(
            $right_col_rows
            , array( 'width' => '67%', 'vertical-align' => 'middle' ));

        $rowparams['margin'] = '5 10 5 10';
        $rowparams['vertical-align'] = 'middle';

        if($search){
            $rowparams['margin'] = '0 25 0 8';
            $rowparams['background-color'] = $this->color_topbar;
        }

        // $rowparams['vertical-align'] = 'middle';
        // $rowparams['height'] = '80';

        $this->data->scroll[] = $this->getRow(array(
            $col_left,
            $col_right
        ), $rowparams);

        $this->data->scroll[] = $this->getText( '', array( 'style' => 'olive-row-divider' ) );
    }

    public function handleMatchInviteAction() {

        $open_chat = false;
        $do_refresh = false;

        if ( preg_match('~accept-user-~', $this->menuid) ) {
            $ac_user_id = str_replace('accept-user-', '', $this->menuid);
            $this->initMobileMatching( $ac_user_id );
            $this->mobilematchingobj->send_accept_push = false;
            $this->triggerConfirmationPush( $ac_user_id );
            $this->mobilematchingobj->saveMatch();
            $open_chat = true;
        }

        if ( preg_match('~decline-user-~', $this->menuid) ) {
            $ac_user_id = str_replace('decline-user-', '', $this->menuid);
            $this->initMobileMatching( $ac_user_id );
            $this->mobilematchingobj->skipMatch();
            $do_refresh = true;
        }

        // Re-init the matching in order to restore the corresponding play ID
        $this->initMobileMatching();

        if ( $open_chat ) {

            $onload = new StdClass();
            $onload->action = 'open-action';
            $onload->id = $this->getTwoWayChatId($ac_user_id, $this->current_playid);
            $onload->back_button = true;
            $onload->sync_open = true;
            $onload->sync_close = true;
            $onload->viewport = 'bottom';
            $onload->action_config = $this->requireConfigParam('chat');

            $this->data->onload[] = $onload;

        } else if ( $do_refresh ) {

            $onload = new StdClass();
            $onload->action = 'submit-form-content';
            $onload->id = 'refresh-view';

            $this->data->onload[] = $onload;

        }

    }

    public function getHeader($active=1) {


        $content = array(
            'tab1' => strtoupper('{#people#}'),
            'tab2' => strtoupper('{#public_chats#}'),
        );

        $params = array(
            'active' => $active,
            'color_topbar' => '#1f72b6',
            'color_topbar_hilite' => '#2f8fcb',
            'indicator_mode' => 'fulltab',
            'btn_padding' => '12 10 12 10',
            'divider' => true,
        );

        $this->data->header[] = $this->getTabs($content, $params);

    }

    public function searchFooter(){
        $style = array(
            'background-color' => $this->color_topbar_hilite,
            'padding' => '4 4 4 4',
            'margin' => '0 5 0 0',
            'border-radius' => '4',
            'font-size' => 12,
            'width' => '45%',
            'text-align' => 'center',
            'color' => '#ffffff'
        );

        $this->data->footer[] = $this->getText('{#showing_search_results#}',array(
            'font-size' => 12,
            'text-align' => 'center',
            'padding' => '5 2 0 2',
            'background-color' => $this->color_topbar,
            'color' => '#ffffff'
        ));

        $style['onclick'] = new stdClass;
        $style['onclick']->action = 'open-action';
        $style['onclick']->action_config = $this->getConfigParam('action_id_categorysearch');
        $style['onclick']->open_popup = 1;

        $col[] = $this->getText('{#change_search#}',$style);

        $style['onclick'] = new stdClass;
        $style['onclick']->action = 'submit-form-content';
        $style['onclick']->id = 'cancel-search';

        $col[] = $this->getText('{#cancel#}',$style);

        $this->data->footer[] = $this->getRow($col,array(
            'background-color' => $this->color_topbar,
            'padding' => '5 0 5 15',
            'height' => 36
        ));
    }

    public function setFooter() {

        if($this->showing_search_results){
            $this->searchFooter();
            return true;
        }

        $likes = $this->mobilematchingobj->getMyInbox();
        $likes_count = ( $likes ? count($likes) : 0 );

        $matches = $this->mobilematchingobj->getMyMatches();
        $filtered_matches = $this->getMatchesWithMessages( $matches );

        $matches_count = ( $filtered_matches ? count($filtered_matches) : 0 );

        $click_id_messages = $this->getConfigParam('my_matches_messages');
        $click_id_invites = $this->getConfigParam('my_matches_invites');

        $button_messages = $this->getColumn(array(
            $this->getText( '{#messages#} (' . $matches_count . ')', array( 'style' => 'olive-footer-button' ) ),
        ), array( 'style' => 'olive-footer-button-holder', 'onclick' => $this->getOnclick( 'action', true, $click_id_messages ) ));

        $spacer = $this->getVerticalSpacer( '1', array( 'background-color' => '#7f7f7f' ) );

        $button_invites = $this->getColumn(array(
            $this->getText( '{#invites#} (' . $likes_count . ')', array( 'style' => 'olive-footer-button' ) ),
        ), array( 'style' => 'olive-footer-button-holder', 'onclick' => $this->getOnclick( 'action', true, $click_id_invites ) ));

        $this->data->footer[] = $this->getRow(array(
            $button_messages, $spacer, $button_invites
        ), array( 'height' => '45', 'vertical-align' => 'middle', ));

    }

    public function screenNameSearch(){
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';

        if($this->menuid == 'close-search'){
            $value = false;
        }

        $args = array(
            'style' => 'olive-searchbox-text',
            'hint' => '{#search_user_by_screen_name#}',
            'submit_menu_id' => 'dosearch',
            'variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'suggestions_style_row' => 'example_list_row',
            'suggestions_text_style' => 'example_list_text',
            //'submit_on_entry' => '1',
        );

        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($value, $args);

        if(isset($this->submitvariables['searchterm']) AND $this->menuid == 'dosearch'){
            //$this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
            $cc = new stdClass();
            $cc->id = 'close-search';
            $cc->action = 'submit-form-content';
            $row[] = $this->getImage('fancy_close.png',array('width' => '25','onclick' => $cc,'margin' => '0 0 0 0'));
        }

        $col[] = $this->getRow($row,array('style' => 'olive-searchbox'));
        $col[] = $this->getTextbutton('Search',array('style' => 'olive-search-btn','id' => 'dosearch'));

        $this->data->footer[] = $this->getRow($col,array('background-color' => '#7b7b7b'));
    }

    public function notFound(){
        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '150';
        $params['margin'] = '50 0 0 0';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');


        if($this->showing_search_results){
            $row[] = $this->getText('{#change_your_search_parameters_to_find_people#}',array('style' => 'register-text-step-2'));
            $row[] = $this->getText('{#you_need_to_select_at_least_one_interest#}',array('style' => 'register-text-step-2'));

            $row[] = $this->getTextbutton('{#change_search#}',array('style' => 'olive-submit-button','id' => '1234',
                'action' => 'open-action',
                'open_popup' => true,
                'config' => $this->getConfigParam('action_id_categorysearch')));

            $row[] = $this->getTextbutton('{#invite_friends#}',array('style' => 'olive-submit-button','id' => '1234',
                'action' => 'open-action',
                'open_popup' => true,
                'config' => $this->getConfigParam('invite_action')));

        } else {
            $row[] = $this->getText('{#there_is_no_one_new_around_you#}',array('style' => 'register-text-step-2'));

            $row[] = $this->getTextbutton('{#invite_friends#}',array('style' => 'olive-submit-button','id' => '1234',
                'action' => 'open-action',
                'open_popup' => true,
                'config' => $this->getConfigParam('invite_action')));

            $row[] = $this->getTextbutton('Scan again',array('style' => 'olive-submit-button','id' => '12344'));

        }

        $this->data->scroll[] = $this->getColumn( $row );
    }

    public function getSearchUsers() {
        $varid = $this->getVariableId('screen_name');
        $model = new AeplayVariable();

        $q = $this->submitvariables['searchterm'];

        if ( empty($q) ) {
            return array();
        }

        $matches = $this->mobilematchingobj->getMyMatches();

        $ids = array();

        foreach ($matches as $play_id) {
            $query = new CDbCriteria( array(
                'condition' => "play_id = :play_id AND variable_id = :var_id AND value LIKE :value",
                'params'    => array(
                    ':play_id' => $play_id,
                    ':var_id' => $varid,
                    ':value' => "%$q%",
                )
            ) );

            $match = $model->findByAttributes( array(), $query );

            if ( $match ) {
                $ids[] = $match->play_id;
            }
        }

        return $ids;
    }

    public function triggerConfirmationPush( $play_id ) {
        $name = $this->getFirstName( $this->varcontent );
        $text = 'New Olive match';
        $description =  $name . ' accepted your invitation. You can chat now. on tap it should open the chat with this user.';
        Aenotification::addUserNotification( $play_id, $text, $description, '0', $this->gid );
    }

    public function getChatSeparatorStyles() {
        return array(
            'margin' => '8 0 4 0',
            'background-color' => '#e5e5e5',
            'height' => '3',
        );
    }

}