<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobilematchingController extends ArticleController {

    public $data;
    public $configobj;
    public $theme;
    public $current_count;

    public $mymatchtitleimage = 'its-a-match.png';
    public $ismatch = false;
    
    public $enable_advertising = true;

    /* instead of using playid and gameid as usual, it is possible to associate
       everything to another play. This is to facilitate communication between
       two apps.
    */

    public $current_playid;
    public $current_gid;

    // in order to show connections only once, we save all shown id's here
    public $expended_connection_ids;
    public $swipe_iterations = 0;

    public $units;

    public function init(){

        /* we can be borrowing the data from another app */
        $this->fakePlay();

    }

    public function tab1(){
        $this->data = new StdClass();
        $this->activateLocationTracking();
        if(!$this->checkForPermission()){
            return $this->data;
        }

        $field_check_failed = $this->checkDBUserData();

        if ( $field_check_failed AND $this->getConfigParam( 'details_action_id' ) ) {
            $this->showDBUserDataView();
            return $this->data;
        }

        $use_miles = $this->getConfigParam( 'use_miles' );
        $this->units = ( $use_miles ? 'miles' : 'km' );

        $this->askPushPermission();

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
                $this->data->scroll[] = $this->getText('{#finish_registration_first#}',array('style' => 'loader-text'));
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

        if($cache AND $mode != 'my_matches'){
            $this->itsAMatch($cache);
            return $this->data;
        }

        /* admin function for deleting users */
        if(strstr($this->menuid,'delete-user-')){
            $delid = str_replace('delete-user-','',$this->menuid);
            UserGroupsUseradmin::model()->deleteByPk($delid);
        }

        if($mode == 'my_matches') {
            $this->myMatches();
        } else {
            $this->handleSwipes();
        }

        return $this->data;
    }

    public function initMobileMatching($otheruserid=false,$debug=false){
        Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

        $this->mobilematchingobj = new MobilematchingModel();
        $this->mobilematchingobj->playid_thisuser = $this->current_playid;
        $this->mobilematchingobj->playid_otheruser = $otheruserid;
        $this->mobilematchingobj->gid = $this->current_gid;
        $this->mobilematchingobj->actionid = $this->actionid;
        $this->mobilematchingobj->uservars = $this->varcontent;
        $this->mobilematchingobj->factoryInit($this);
        $this->mobilematchingobj->initMatching($otheruserid,true);

        $this->mobilematchingmetaobj = new MobilematchingmetaModel();
    }

    public function handleSwipes() {

        if ( isset($this->submit['swid']) ) {
            $swid = $this->submit['swid'];

            // Open interstitials
            if ( $this->enable_advertising == true ) {
                $this->openInterstitial();
            }

            if ( strstr($swid,'left') ) {
                $id = str_replace('left', '', $swid);
                $this->initMobileMatching($id);
                $this->mobilematchingobj->skipMatch();
                $this->matches();
            } elseif ( strstr($swid,'right') ) {
                $id = str_replace('right', '', $swid);
                $this->initMobileMatching($id);

                if ( $this->mobilematchingobj->saveMatch() == true ) {
                    $this->ismatch = true;
                    $this->itsAMatch($id);
                } else {
                    $this->showMatches();
                }

            } else {
                $this->matches();
            }

        } else {
            $this->matches();
            return true;
        }

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

    public function checkForPermission(){
        return true;
    }

    public function activateLocationTracking(){
        return true;
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
            $this->data->scroll[] = $this->getSettingsTitle('{#my_matches#}');
        }

        $this->matchList($matches);

        if($this->getConfigParam('mymatches_show_group_chats')){
            $params['mode'] = 'mychats';
            $params['expended_connection_ids'] = $this->expended_connection_ids;
            $this->data->scroll[] = $this->getSettingsTitle('{#group_chats#}');
            $this->data->scroll[] = $this->moduleGroupChatList($params);
        }

        if($this->getConfigParam('mymatches_show_incoming_requests')){
            $this->getIncomingRequests();
        }

        if($this->getConfigParam('mymatches_show_outgoing_requests')){
            $this->getOutgoingRequests();
        }

        /*       
        if($this->getConfigParam('main_view')){
            $this->data->scroll[] = $this->getSpacer('40');
            $this->data->scroll[] = $this->getTextbutton('{#match_more_people#}',array(
                'style' => 'general_button_style',
                'action' => 'open-action',
                'config' => $this->getConfigParam('main_view')));
        }
        */

    }

    public function getIncomingRequests() {
        $matches = $this->mobilematchingobj->getMyInbox();
        if($matches) {
            $this->data->scroll[] = $this->getSettingsTitle('{#inbox#}');
            $this->matchList($matches);
        }
    }

    public function getOutgoingRequests() {
        $matches = $this->mobilematchingobj->getMyOutbox();
        if($matches){
            $this->data->scroll[] = $this->getSettingsTitle('{#outbox#}');
            $this->matchList($matches);

        }
    }

    public function matchList($matches, $groupchat = false, $message = '{#no_matches#}'){

        if ( empty($matches) ) {
            $othertxt['style'] = 'imate_title_nomatch';
            $this->data->scroll[] = $this->getText($message, $othertxt);
            return true;
        }

        foreach ($matches as $key => $res) {
            $vars = AeplayVariable::getArrayOfPlayvariables($res);

            if ( empty($vars) ) {
                continue;
            }

            if (!isset($vars['profilepic'])) {
                $vars['profilepic'] = 'anonymous2.png';
            }

            if(isset($this->expended_connection_ids[$res])){
                continue;
            }

            $this->getMyMatchItem($vars,$res);
            $this->expended_connection_ids[$res] = true;
        }

    }

    public function getMyMatchItem($vars,$id,$search=false){

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';
        
        $name = $this->getFirstName($vars);
        $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;

        $textparams['onclick'] = new stdClass();
        $textparams['onclick']->action = 'open-action';
        $textparams['onclick']->id = $this->getTwoWayChatId($id,$this->current_playid);
        $textparams['onclick']->back_button = true;
        $textparams['onclick']->sync_open = true;
        $textparams['onclick']->sync_close = true;
        $textparams['onclick']->viewport = 'bottom';
        $textparams['onclick']->action_config = $this->requireConfigParam('chat');
        $textparams['style'] = 'imate_title';    

        $image_onclick = new stdClass();
        $image_onclick->action = 'open-action';
        $image_onclick->id = $id;
        $image_onclick->back_button = true;
        $image_onclick->sync_open = true;
        $image_onclick->action_config = $this->requireConfigParam('detail_view');

        $unread = false;

        /* notification column */
        $contextkey = $this->getTwoWayChatId($id,$this->current_playid);
        $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey,'chat_user_play_id' => $this->current_playid));

        /* there is a situation where the record might not exist, so we should create it */
        if(!is_object($chat)){
            $ouchat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey));
            if(is_object($ouchat)){
                $ob = new Aechatusers();
                $ob->chat_id = $ouchat->chat_id;
                $ob->context_key = $contextkey;
                $ob->chat_user_play_id = $this->current_playid;
                $ob->context = $ouchat->context;
                $ob->insert();
                $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey,'chat_user_play_id' => $this->current_playid));
            }
        }

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

        if(isset($vars['private_photos']) AND $vars['private_photos'] AND $this->getConfigParam('article_action_theme') == 'sila'){
            $test = AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $id, 'key' => 'two-way-matches','value' => $this->playid));
            if(!is_object($test)){
                $profilepic = 'sila-private-photos.png';
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
            $right_col_rows[] = $this->getImage('red-dot.png',array('width' => '10', 'margin' => '5 10 0 0', 'float' => 'right', 'floating' => 1));
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

        if(isset($messages) AND isset ($messages->chat_message_text)){
            $shortext = $messages->chat_message_text;
        }elseif ( $profiledescription ) {
            if ( strlen($profiledescription) > 35 ) {
                $shortext = $this->truncate_words( $profiledescription, 4 ) . '...';
            } else {
                $shortext = $profiledescription;
            }
        } else {
            $shortext = '';
        }

        $textparams['style'] = 'imate_title_subtext';

        $right_col_rows[] = $this->getRow(array(
            $this->getText($shortext, $textparams),
        ));

        $col_left = $this->getColumn(
            $left_col_rows
        , array( 'width' => '23%', 'text-align' => 'center', ));

        $col_right = $this->getColumn(
            $right_col_rows
        , array( 'width' => '75%', 'vertical-align' => 'middle' ));

        $rowparams['margin'] = '5 10 5 10';

        $append_location = 'scroll';

        if($search){
            $rowparams['padding'] = '5 0 0 0';
            $rowparams['margin'] = '0 0 0 0';
            $rowparams['background-color'] = $this->color_topbar;
            $append_location = 'footer';
        }

        // $rowparams['vertical-align'] = 'middle';
        $rowparams['height'] = '65';

        $this->data->{$append_location}[] = $this->getRow(array(
            $col_left,
            $col_right
        ), $rowparams);
    }

    public function matches(){

        if(strstr($this->menuid,'no_')){
            $id = str_replace('no_','',$this->menuid);
            $this->skip($id);
            return true;
        }

        if(strstr($this->menuid,'yes_')){
            $id = str_replace('yes_','',$this->menuid);
            $this->doMatch($id);
            return true;
        }

        $this->showMatches();

    }

    public function doMatch($id=false){

        if($id){
            $this->mobilematchingobj->initMatching($id);
        }

        if($this->mobilematchingobj->saveMatch() == true){
            $this->itsAMatch();
        } else {
            $this->showMatches(true);
        }
    }

    public function skip($id=false){
        if($id){
            $this->mobilematchingobj->initMatching($id);
        }

        $this->mobilematchingobj->skipMatch();
        $this->showMatches(true);
    }

    public function getFirstName($vars){

        if ( isset($vars['screen_name']) ) {
            return $vars['screen_name'];
        }

	    $name = '';

        if ( isset($vars['real_name']) AND !empty($vars['real_name']) ) {
	        $name = $vars['real_name'];
        }

	    if ( isset($vars['name']) AND !empty($vars['name']) ) {
		    $name = $vars['name'];
	    }

	    if ( empty($name) ) {
	    	return false;
	    }
        
        $firstname = explode(' ', trim($name));
        $firstname = ucfirst($firstname[0]);

        return $firstname;
    }

    public function itsAMatch($id=false){
        $this->configureBackground('actionimage2');

        $cachepointer = $this->current_playid .'-matchid';
        Appcaching::setGlobalCache($cachepointer,$id);

        if(!$id){
            $id = $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';
        $params['priority'] = 9;

        if ( $this->getConfigParam( 'actionimage2' ) ) {
            $match_img = $this->getConfigParam( 'actionimage2' );
            $row[] = $this->getImage($match_img, $params);
        } else {
            $row[] = $this->getSpacer(20);
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
        $params['priority'] = 9;
        $params['imgheight'] = '200';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic,$params);
        $pics[] = $this->getImage($this->getVariable('profilepic'),$params);

        $row[] = $this->getRow($pics,array('margin' => '30 30 0 30'));

        $row[] = $this->getSpacer('50');
        $row[] = $this->getTextbutton('Start a conversation',array('style' => 'general_button_style','id' => $this->getTwoWayChatId($id,$this->current_playid),
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));
        $textstyle['margin'] = '15 0 10 0';
        $textstyle['text-align'] = 'center';
        $row[] = $this->getText('or',$textstyle);
        $row[] = $this->getTextbutton('Keep surfing',array('style' => 'general_button_style','id' => 'keep-playing'));
        $row[] = $this->getSpacer('50');

        $this->data->scroll[] = $this->getColumn($row,array(
            'background-color' => '#e33124',
            //'background-image' => $this->getImage('no-match-bg.png')
        ));


        $this->setFooter();
    }

    public function setFooter() {

        $notifications = $this->mobilematchingobj->getNotifications();

        if (is_array($notifications) AND !empty($notifications)) {
            $notification = array_shift($notifications);
        }

        /* this will not work on simulator so needs to be commented out when testing
            problem is that simulator doesn't send system_push_id
        */

        $this->setFooterButtons();

        if (isset($notification) AND isset($notification['type']) AND $notification['type'] == 'match' AND isset($notification['user']) AND $this->ismatch == false) {
            $key = $notification['user'];

            $vars = AeplayVariable::getArrayOfPlayvariables($key);

            $name = $this->getFirstName($vars);
            $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';

            $markread = new StdClass();
            $markread->action = 'submit-form-content';
            $markread->id = 'markread-' . $key;

            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->id = $this->getTwoWayChatId($key, $this->current_playid);
            $onclick->back_button = true;
            $onclick->sync_open = true;
            $onclick->sync_close = true;
            $onclick->viewport = 'bottom';
            $onclick->action_config = $this->requireConfigParam('chat');

            $columns[] = $this->getImage($profilepic, array(
				'style' => 'round_image_imate_notify',
				'priority' => 9,
				'imgheight' => '200',
            ));

            $columns[] = $this->getText( $this->getMatchText( $name ), array(
            	'style' => 'imate_title_notify',
            ));

            $this->data->footer[] = $this->getRow($columns, array(
            	'background-color' => $this->getToolboxColor(),
            	'vertical-align' => 'middle',
            	'onclick' => array($markread, $onclick),
            	'height' => 60,
            ));

            return true;

        } elseif (isset($notification) AND isset($notification['type']) AND $notification['type'] == 'msg' AND isset($notification['user']) AND $this->ismatch == false) {
            $key = $notification['user'];

            $vars = AeplayVariable::getArrayOfPlayvariables($key);
            $name = $this->getFirstName($vars);
            $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';

            $markread = new StdClass();
            $markread->action = 'submit-form-content';
            $markread->id = 'markread-' . $key;

            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->id = $this->getTwoWayChatId($key, $this->current_playid);
            $onclick->back_button = true;
            $onclick->sync_open = true;
            $onclick->sync_close = true;
            $onclick->viewport = 'bottom';
            $onclick->action_config = $this->requireConfigParam('chat');

            $columns[] = $this->getImage($profilepic, array(
                'style' => 'round_image_imate_notify',
                'priority' => '9',
                'imgheight' => '200',
            ));
            $columns[] = $this->getText('You have a new message from ' . $name . '!', array(
                'style' => 'imate_title_notify'
            ));
            $columns[] = $this->getImage('small-chat-icon.png', array('style' => 'icon_imate_notify'));

            $this->data->footer[] = $this->getRow($columns, array(
                'height' => '60',
                'vertical-align' => 'middle',
                'background-color' => $this->color_topbar,
                'onclick' => array($markread, $onclick),
            ));

        } elseif ($this->getConfigParam('google_adcode_banners') AND $this->enable_advertising == true AND $this->canShowAds() ){


            $banner_url = $this->getConfigParam('google_adcode_banners');
            $dimensions = $this->screen_width / $this->screen_height;
            $min_user_dim = ( $this->getConfigParam('min_user_dimentions') ? $this->getConfigParam('min_user_dimentions') : '0.54' );

            if ( strstr($banner_url, 'ca-app-pub') AND $dimensions > $min_user_dim) {
                $this->data->footer[] = $this->getBanner($this->getConfigParam('google_adcode_banners'));
            } else if ( filter_var($banner_url, FILTER_VALIDATE_URL) ) {

                $onclick = new StdClass();
                $onclick = new StdClass();
                $onclick->id = 'open-url';
                $onclick->action = 'open-url';
                $onclick->action_config = $banner_url;

                $footer_banner = $this->getConfigParam( 'actionimage11' );
                if ( empty($footer_banner) ) {
                    $footer_banner = 'rantevu-banner.png';
                }

                $this->data->footer[] = $this->getImage( $footer_banner, array( 'onclick' => $onclick, 'margin' => '0 25 5 25' ) );
            }

        }
    }

    protected function setFooterButtons()
    {
        // to be overriden in themes
    }

    public function getMatchText( $name ) {
        return 'You and ' . $name . ' are a match!';
    }

    public function canShowAds() {

        // Show ads no matter what, there isn't any option to stop them
        if ( !$this->getConfigParam('user_can_disable_ads') ) {
            return true;
        }

        // User has purchased a subscription
        if (
            $this->getSavedVariable('purchase_matchswappunlimited') OR
            $this->getSavedVariable('purchase_matchswappdisableadvertising')
        ) {
            return false;
        }

    }

    public function notFound(){
        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '120';
        $params['margin'] = '100 0 0 0';
        $params['priority'] = 9;
        $params['imgwidth'] = '400';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');

        $row[] = $this->getText('There is no one new around you',array('style' => 'register-text-step-2'));
        $row[] = $this->getTextbutton('Invite your friends',array('style' => 'general_button_style','id' => '1234',
            'action' => 'open-action',
            'open_popup' => true,
            'config' => $this->getConfigParam('invite_action')));

        $row[] = $this->getTextbutton('Scan again',array('style' => 'general_button_style','id' => '12344'));

        $this->setFooter();

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
        ));

    }

	public function getAMatch($users) {

		$outvars = [];
		$pointer = false;
		
		foreach ($users as $i => $user) {

			if ( !isset($user['play_id']) ) {
				continue;
			}

			// For performance reasons we only query 40 users at a time
			if ( count($outvars) > 20 ) {
				continue;
			}
			
			$id = $user['play_id'];
			$vars = AeplayVariable::getArrayOfPlayvariables($id);

			$vars['play_id'] = $id;
			$vars['distance'] = $user['distance'];

			$profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : false;
			$filecheck = $this->imagesobj->findFile($profilepic);
			$filter = $this->filter( $vars );

			// its important that we exclude non-matches as otherwise we will sooner or later
			// run out of matches as only the skipped get excluded by query, rest is handled here in php
			if ( !$filter OR !$filecheck ) {
				$this->addToDebug('Ignored ' . $id . 'filecheck:' . $filecheck .'filter:' . $filter);
				$this->mobilematchingobj->playid_otheruser = $id;
				$this->mobilematchingobj->skipMatch(false,true);
			} else {

				$outvars[] = $vars;
				if (!$pointer) {
					$this->mobilematchingobj->setPointer($user['play_id']);
					$pointer = true;
				}

				$this->addToDebug('Skipped for now '.$id .'filecheck:' .$filecheck .'filter:' .$filter);
			}

		}

		if (!empty($outvars)) {
			return $outvars;
		}

		// Shouldn't be neccessary
		// $this->notFound();
		return false;
	}

    /* get the users, put the layout code in place */
    public function showMatches($skipfirst=false){
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $query_by_gender = $this->queryByGender();
        $users = $this->mobilematchingobj->getUsersNearby($search_dist, 'exclude', $query_by_gender, false, false, $this->units);

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

    public function buildSwipeStack($users,$skipfirst,$include_buttons=true){
        
        $detail_view = $this->requireConfigParam('detail_view');
        $swipestack = array();
        $vars = $this->getAMatch($users);
        $itemcount = 0;

        /* didn't find any users */
        if($vars == false){
            return false;
        }

        foreach($vars as $i => $one){

            if(!isset($one['play_id'])){
                $this->addToDebug('skipped, no play id found ');
                continue;
            }

            $id = $one['play_id'];
            $profilepic = isset($one['profilepic']) ? $one['profilepic'] : false;

            $piccount = $this->getPicCount($one);
            $distance = round($one['distance'], 0);

            if ( $itemcount < 20 ) {
                if(!$skipfirst){
                    $this->addToDebug('added to matchstack: '.$id);

                    $rows[] = $this->getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i);
                    $rows[] = $this->getMatchingSpacer($id);

                    if($include_buttons){
                        $rows[] = $this->getBtns($id,$detail_view,$i);
                    }

                    $swipestack[] = $this->getColumn($rows,array('text-align' => 'center','margin' => '28 0 0 0','swipe_id' => $id
                    ));
                    $itemcount++;

                    unset($page);
                    unset($rows);
                    unset($toolbar);
                    unset($column);

                } else {
                    $this->addToDebug('skipped first '.$id);
                    $skipfirst = false;
                }
            }

	        if ($itemcount > 20) {
		        break;
	        }

        }

        if(empty($swipestack) AND $this->swipe_iterations < 10){
            $this->swipe_iterations++;
            return self::buildSwipeStack($users,false,$include_buttons);
        }

        return $swipestack;
    }

    public function getPicCount($one){
        $count = 2;
        $piccount = 1;

        while ($count < 10) {
            $n = 'profilepic' . $count;
            if (isset($one[$n]) AND strlen($one[$n]) > 2) {
                $piccount++;
            }
            $count++;
        }

        return $piccount;
    }

    public function getMatchingSpacer($id){
        if($this->getSavedVariable('is_admin') == 1){
            $menu1['action'] = 'submit-form-content';
            $menu1['id'] = 'delete-user-'.$id;
            $menu2['action'] = 'swipe-left';
            return $this->getImage('delete-icon.png',array('width' => 35,'float' => 'center','margin' => '10 0 0 0','onclick' => array($menu2,$menu1)));
        } elseif($this->aspect_ratio > 0.57){
            return $this->getSpacer('4');
        } else {
            return $this->getSpacer('30');
        }
    }

    public function filter($one){
        
        if($this->filterByDistance($one) == false){
            $this->addToDebug('Filtered by distance');
            return false;
        }

        if($this->filterByAge($one) == false){
            $this->addToDebug('Filtered by age');
            return false;
        }

        if($this->filterByReligion($one) == false){
            $this->addToDebug('Filtered by religion');
            return false;
        }

        if($this->filterBySex($one) == false){
            $this->addToDebug('Filtered by sex ' .$this->getSavedVariable('gender'));
            return false;
        }

        if($this->filterByHcp($one) == false){
            $this->addToDebug('Filtered by hcp');
            return false;
        }

        if($this->filterByAvailability($one) == false){
            $this->addToDebug('Filtered by availability');
            return false;
        }

        if(isset($one['hide_user']) AND $one['hide_user'] == '1'){
            return false;
        }

        return true;
    }


    public function filterByDistance($one){
        if (!isset($one['distance'])) {
            return true;
        }

        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        
        if($search_dist > round($one['distance'], 0)){
            return true;
        } else {
            return false;
        }
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

        $texparam['font-size'] = 12;

        $city = isset($one['city']) ? $one['city'] .', ' : '';

        $fullline_text = $this->getFirstName($one) . ', ' . $city . $distance . ' ' . $this->units;

        if ( strlen($fullline_text) < 30 ) {
            $toolbar[] = $this->getText($this->getFirstName($one) . ', ' . $city . $distance . ' ' . $this->units, $texparam);
        } else {
            
            $toolbar[] = $this->getColumn(array(
                $this->getText($this->getFirstName($one), array( 'width' => '100%', 'font-size' => 12 )),
                $this->getText($city . $distance . ' ' . $this->units, array( 'width' => '100%', 'font-size' => 12 )),
            ), array(
                'width' => '70%',
                'vertical-align' => 'middle',
            ));

        }

        $toolbar[] = $this->getRow(array(
            $this->getImage('toolbar-friends.png', array('width' => '20', 'margin' => '0 5 0 5')),
            $this->getText($this->mobilematchingobj->getNumberOfMatches($id), $texparam),
            $this->getImage('toolbar-photos.png', array('width' => '20', 'margin' => '0 5 0 5')),
            $this->getText($piccount, $texparam),
        ), array('floating' => '1','float' => 'right'));

        $page[] = $this->getRow($toolbar, array(
            'margin' => '0 10 0 10',
            'height' => '40',
            'vertical-align' => 'middle',
        ));

        return $this->getColumn($page, array(
            'leftswipeid' => 'left' . $id,
            'background-color' => '#ffffff',
            //'width' => '100%',
            'padding' => '5 5 5 5',
            'text-align' => 'center',
            'shadow-color' => '#66000000',
            'shadow-radius' => '4','shadow-offset' => '0 0',
            'width' => $this->screen_width-50,
            'rightswipeid' => 'right' . $id
        ));
    }

    public function getBtns($id,$detail_view,$i){
        $column[] = $this->getImagebutton('btn_no_2.png', 'no_' . $id, false, array('width' => '41%', 'priority' => '1',  'margin' => '0 0 0 8','action' => 'swipe-left'));
        $column[] = $this->getImagebutton('btn_info_2.png', $id, false, array('width' => '14%', 'priority' => '1', 'action' => 'open-action', 'config' => $detail_view, 'context' => 'profile-'.$id));

        $menu = $this->getPushPermissionMenu();

        if($menu){
            $menu2 = new stdClass();
            $menu2->action = 'swipe-right';
            $menu2->id = 'yes_' . $id;
            $column[] = $this->getImage('btn_yes_3.png', array('width' => '41%', 'priority' => '1', 'onclick' => $menu,$menu2));
        } else {
            $column[] = $this->getImagebutton('btn_yes_3.png', 'yes_' . $id, false, array('width' => '41%', 'priority' => '1', 'action' => 'swipe-right'));
        }


        return $this->getRow($column, array('align' => 'center', 'noanimate' => true));
    }


    public function filterByHcp($one){
        $filter_start = $this->getSavedVariable('filter_min_hcp');
        $filter_end = $this->getSavedVariable('filter_max_hcp');

        if(!$filter_start OR !$filter_end){
            return true;
        }

        if($one['hcp'] > $filter_start-1 AND $one['hcp'] < $filter_end+1){
            return true;
        } else {
            return false;
        }
    }

    /* even one matching availability will result true
        or if either player hasn't set their value it will also result true,
    better to give a false positive
    */
    public function filterByAvailability($one){
        $filter = $this->getSavedVariable('filter_by_availability');

        if($filter){
            $my_availability = @json_decode($this->getSavedVariable('availability'),true);
            $other_availability = @json_decode($one['availability'],true);

            if(is_array($my_availability) AND !empty($my_availability) AND is_array($other_availability) AND !empty($other_availability)){
                foreach ($my_availability as $key=>$val){
                    if(isset($other_availability[$key])){
                        return true;
                    }
                }

                return false;
            }
        }

        return true;

    }

    public function filterByReligion($one){
        $myreligion = $this->getSavedVariable('religion');
        $filter = $this->getSavedVariable('filter_religion');

        if(!$filter){
            return true;
        }

        if($one['religion'] == $myreligion){
            return true;
        } else {
            return false;
        }
    }

    public function filterByAge($one){
        $start = $this->getSavedVariable('filter_age_start');
        $end = $this->getSavedVariable('filter_age_end');

        /* if filter is not set */
        if(!$start or !$end){
            return true;
        }

        if( !isset($one['birth_year']) OR empty($one['birth_year']) ){
            return true;
        }

        $day = isset($one['birth_day']) ? $one['birth_day'] : 01;
        $month = isset($one['birth_month']) ? $one['birth_month'] : 01;

        $age = Controller::getAge($day,$month,$one['birth_year']);

        if($start AND $start > $age){
            return false;
        }

        if($end AND $end < $age){
            return false;
        }

        return true;
    }

    public function queryByGender(){
        $gender = $this->getSavedVariable('gender');
        $match_women = $this->getSavedVariable('women',true);
        $match_men = $this->getSavedVariable('men',true);

        if(($gender == 'man' OR $gender == 'male') AND $match_men == true){
            return false;
        } elseif(($gender == 'woman' OR $gender == 'female') AND $match_women == true){
            return false;
        } else {
            return true;
        }

    }

    public function filterBySex($one,$opposites=false){
        $gender = isset($one['gender']) ? $one['gender'] : 'man';

        $match_women = $this->getSavedVariable('women');
        $match_men = $this->getSavedVariable('men');

	    if(!$match_women){ $this->getSavedVariable('look_for_women',true); }
	    if(!$match_men){ $this->getSavedVariable('look_for_men',true); }

	    if($opposites ){
            if($this->getSavedVariable('gender') == $gender){
                return false;
            } else {
                return true;
            }
        }

        if(($gender == 'man' OR $gender == 'male') AND $match_men == true){
            $sex = true;
        } elseif(($gender == 'woman' OR $gender == 'female') AND $match_women == true){
            $sex = true;
        } else {
            $sex = false;
        }

        return $sex;
    }

    public function screenNameSearch(){
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';

        if($this->menuid == 'close-search'){
            $value = false;
        }

        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25', 'padding' => '0 0 0 5'));
        $row[] = $this->getFieldtext($value,array('style' => 'example_searchbox_text',
            'hint' => '{#add_user_by_screen_name#}',
            'submit_menu_id' => 'dosearch',
            'variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'suggestions_style_row' => 'example_list_row',
            'suggestions_text_style' => 'example_list_text',
            //'submit_on_entry' => '1',
        ));

        if(isset($this->submitvariables['searchterm']) AND $this->menuid == 'dosearch'){
            //$this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
            $cc = new stdClass();
            $cc->id = 'close-search';
            $cc->action = 'submit-form-content';
            $row[] = $this->getImage('fancy_close.png',array('width' => '25','onclick' => $cc,'margin' => '0 0 0 0'));
        }

        $col[] = $this->getRow($row,array('style' => 'example_searchbox'));
        $col[] = $this->getTextbutton('Search',array('style' => 'example_searchbtn','id' => 'dosearch'));
        
        if($this->menuid == 'dosearch'){
            $varid = $this->getVariableId('screen_name');
            $results = AeplayVariable::model()->findAllByAttributes(array(
                        'variable_id' => $varid,
                        'value' => $this->submitvariables['searchterm']
                    ));

            foreach ($results as $result) {
                if ( $result->play_id == $this->playid ) {
                    continue;
                }

                $vars = AeplayVariable::getArrayOfPlayvariables( $result->play_id );
                $this->getMyMatchItem($vars, $result->play_id, true);
            }
        }

        $this->data->footer[] = $this->getRow($col,array('background-color' => $this->color_topbar));
    }

    public function openInterstitial() {

        // if ( !$this->getConfigParam( 'show_interstitials' ) ) {
        //     return false;
        // }

        $count_cache = $this->current_playid .'-swipedcount';
        $this->current_count = Appcaching::getGlobalCache($count_cache);

        if ( empty($this->current_count) ) {
            $this->current_count = 0;
        }

        $this->current_count++;

        Appcaching::setGlobalCache($count_cache, $this->current_count);

        if ( $this->current_count % 20 == 0 ) {
            $this->data->onload['action'] = 'open-interstitial';
        }
    }

    public function truncate_words( $str, $words_count = 5 ) {

        $pieces = explode(' ', $str);
        $output = array();

        foreach ($pieces as $i => $word) {
            if ( $i < $words_count ) {
                $output[] = $word;
            }
        }

        if ( empty($output) ) {
            return $str;
        }

        return implode(' ', $output);
    }

    public function checkDBUserData() {

        $show_screen = false;

        $details_action_id = $this->getActionidByPermaname( 'askdetails' );

        if ( empty($details_action_id) ) {
            return false;
        }

        $action = Aeaction::model()->findByPk( $details_action_id );
        $action_config = @json_decode( $action->config, true );

        if ( empty($action_config) OR
            ( !isset($action_config['required_variables']) OR empty($action_config['required_variables']) )
        ) {
            return false;
        }

        $vars = explode( PHP_EOL, $action_config['required_variables'] );
        $required = array();

        foreach ($vars as $var) {
            $pieces = explode('|', $var);
            $required[] = $pieces[0];
            unset($pieces);
        }

        foreach ($required as $var) {
            if ( !isset($this->varcontent[$var]) OR empty($this->varcontent[$var]) ) {
                $show_screen = true;
            }
        }

        if ( !$show_screen ) {
            return false;
        }

        return true;
    }

    public function showDBUserDataView() {

        if ( $this->sessionGet('detailpopup')+60 < time() OR !$this->sessionGet('detailpopup') ) {
            $onload = new StdClass();
            $onload->action = 'open-action';
            $onload->id = 'open-details-popup';
            $onload->sync_open = 1;
            $onload->open_popup = true;
            $onload->action_config = $this->getConfigParam( 'details_action_id' );
            $this->data->onload[] = $onload;
            $this->sessionSet('detailpopup',time());
        }

        $first_name = $this->getFirstName( $this->varcontent );

        if ( $first_name ) {
            $text = '{#hey#} '. $first_name .', {#missing_fields_description#}';
        } else {
            $text = '{#hey#}, {#missing_fields_description#}';
        }

        $this->data->scroll[] = $this->getText( $text, array( 'style' => 'general-centered-text' ) );


        $args = array(
            'id' => 'open-details-popup',
            'action' => 'open-action',
            'open_popup' => true,
            'sync_open' => 1,
            'config' => $this->getConfigParam( 'details_action_id' ),
            'style' => 'general_button_style_red'
        );

        $this->data->footer[] = $this->getTextbutton( '{#enter_details#}', $args );
    }

	public function getToolboxColor() {
    	return $this->color_topbar;
	}

}