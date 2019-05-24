<?php


class MobilematchingDeseeController extends MobilematchingController
{

    public $users;

    private $metas;
    private $default_time = 43200;
    private $swiped_back_ids = array();
    private $next_mutual_like_num;
    private $fakeusers;
    private $viewname;

    public function __construct($obj)
    {
        parent::__construct($obj);
        //$viewname = 'ae_ext_mobilematching';
        $this->viewname = 'matching_'.$this->gid;

        // Clear the profile's cache
        $cachename = $this->playid .'-currentprofileview';
        Appcaching::setGlobalCache($cachename, '');
    }

    /*
     * This method would set the $users array globally for class
     */
    public function getUsers() {

        $this->mobilematchingobj->playid = $this->playid;
        //$this->mobilematchingobj->updateTempUsers();
        $this->mobilematchingobj->createPivotView();
        //$this->mobilematchingobj->deleteAutomaticUnmatches();

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false, __FILE__);

        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 50;
        $query_by_gender = $this->queryByGender();
        $query_by_gender = false;

        $sorting = 'boosted';

        if ($this->metas->checkMeta('active-users-first', $this->playid)) {
            $sorting = 'active-and-boosted';
        }

        $this->mobilematchingobj->extra_selects = ", (
            SELECT ae_game_play_keyvaluestorage.key FROM ae_game_play_keyvaluestorage
            WHERE $this->viewname.play_id = ae_game_play_keyvaluestorage.play_id
            AND ae_game_play_keyvaluestorage.key = 'matches'
            AND ae_game_play_keyvaluestorage.value = $this->playid
            LIMIT 1
        ) as status";

        $this->users = $this->mobilematchingobj->getUsersNearbyDesee($search_dist, 'exclude', $query_by_gender, false, $sorting, $this->units);
        $this->determineNextMutualLike();

        return true;
    }

    public function matches()
    {
        if (strstr($this->menuid, 'no_')) {
            $id = str_replace('no_', '', $this->menuid);
            $this->skip($id);
            return true;
        }

        if (strstr($this->menuid, 'yes_')) {
            $id = str_replace('yes_', '', $this->menuid);
            $this->doMatch($id);
            return true;
        }

        if (strstr($this->menuid, 'superlikes-')) {
            $id = str_replace('superlikes-', '', $this->menuid);
            $this->sendSuperLikeNotification($id);
            $this->doMatch($id, true);
        }

        $this->showMatches();

    }

    public function sendSuperLikeNotification($id)
    {
        $notifications = new Aenotification();
        $notifications->id_channel = 1;
        $notifications->app_id = $this->gid;
        $notifications->play_id = $id;
        $notifications->subject = '{#someone_thinks_you_are_superhot#}';
        $notifications->message = '{#swipe_to_find_out_who#}';
        $notifications->type = 'push';
        $notifications->action_id = $this->getActionidByPermaname('people');

        $menu1 = new stdClass();
        $menu1->action = 'open-action';
        $menu1->action_config = $this->getActionidByPermaname('people');
        $menu1->sync_open = 1;
        $params = new stdClass;
        $params->onopen = array($menu1);
        $notifications->parameters = json_encode($params);

        $notifications->badge_count = +1;
        $notifications->insert();
    }

    public function handleSwipes()
    {

        $this->metas = new MobilematchingmetaModel();
        $this->rewriteActionConfigField('background_portrait_image', '');
        $this->rewriteActionConfigField('background_color', '#ffffff');

        $this->handleExtrasPayments();

        if (isset($this->submit['swid'])) {

            $swid = $this->submit['swid'];

            // Update the user's activity
            $this->mobilematchingobj->obj_thisuser->last_update = date('Y-m-d H:i:s');
            $this->mobilematchingobj->obj_thisuser->update();

            if (strstr($swid, 'left')) {
                $id = str_replace('left', '', $swid);

                // Save the user's swipe event
                $this->swiped_back_ids[$id] = 'left';
                $this->addToVariable('swiped_back_ids', $this->swiped_back_ids, true);

                $this->initMobileMatching($id);
                $this->mobilematchingobj->skipMatch();

                if ( $this->showLimit()  ) {
                    $this->viewLimitReached();
                } else {
                    $this->matches();
                }

            } elseif (strstr($swid, 'right')) {
                $id = str_replace('right', '', $swid);
                $this->initMobileMatching($id);
                $this->swiped_back_ids[$id] = 'right';

                if ($this->mobilematchingobj->saveMatch() == true) {
                    $this->ismatch = true;
                    $this->triggerConfirmationPush( $id );
                    $this->itsAMatch($id);
                }

                // Save the user's swipe event
                $this->addToVariable('swiped_back_ids', $this->swiped_back_ids, true);

                if ( $this->showLimit()  ) {
                    $this->viewLimitReached();
                } else {
                    $this->showMatches();
                }

            } elseif (strstr($swid, 'back')) {
                $id = str_replace('back', '', $swid);
                $this->initMobileMatching($id);
                $this->mobilematchingobj->deleteMatchEntry();
                $this->metas->decrementValue( 'swipe-back' );
                $this->removeFromVariable('swiped_back_ids', $id, true);

                $this->matches();
            } else {
                $this->matches();
            }

        } else {

            if ( $this->showLimit()  ) {
                $this->viewLimitReached();
            } else {
                $this->matches();
            }

            return true;
        }

        return true;
    }

    public function doMatch($id = false, $save_superhot = false)
    {

        if ($id) {
            $this->mobilematchingobj->initMatching($id);
        }

        if ($save_superhot) {
            $this->mobilematchingobj->saveSuperhot('extra-superhot');
        }

        if ($this->mobilematchingobj->saveMatch() == true) {
            $this->itsAMatch();
        } else {
            $this->showMatches(true);
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

    /* get the users, put the layout code in place */
    public function showMatches($skipfirst = false)
    {

        $this->getUsers();
        $this->setHeader();

        if ($this->mobilematchingobj->debug) {
            $this->addToDebug($this->mobilematchingobj->debug);
        }


        if (empty($this->users)) {
            $this->fakeusers = $this->mobilematchingobj->getFakeUsers(1000000, 'exclude', false, false, 'boosted', $this->units,$this->viewname);

            if($this->fakeusers){
                $swipestack = $this->buildSwipeStack($this->fakeusers, $skipfirst);
            } else {
                $this->notFound();
                return false;
            }
        } else {
            $swipestack = $this->buildSwipeStack($this->users, $skipfirst);
        }

        if (empty($swipestack)) {
            $this->notFound();
        } else {
            $this->data->scroll[] = $this->swipe($swipestack);
            $this->setFooter();
        }

        return true;
    }

    public function setHeader()
    {
        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $openMessages = new stdClass();
        $openMessages->action = 'open-action';
        $openMessages->action_config = $this->getActionidByPermaname('messaging');

        $menuBar = array(
            $this->getRow(array(
                $this->getImage('ic_menu_new.png', array(
                    'onclick' => $toggleSidemenu,
                    'width' => '30'
                )),
            ), array(
                'width' => $this->screen_width / 3.5
            )),
            $this->getNextMutualLikeCounter(),
            $this->getRow(array(
                $this->getImage('ic_chat_new.png', array(
                    'width' => '30',
                    'onclick' => $openMessages
                ))
            ), array(
                'width' => $this->screen_width / 3.5,
                'text-align' => 'right'
            )),
        );

        $this->data->header[] = $this->getRow($menuBar, array('margin' => '20 20 0 20'));
    }

    protected function checkUnreadMessages()
    {
        $matches = $this->mobilematchingobj->getMyMatches();

        if (empty($matches)) {
            return false;
        }

        foreach ($matches as $match_id) {
            $contextkey = $this->getTwoWayChatId($match_id, $this->current_playid);
            $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey));
            // $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey, 'chat_user_play_id' => $this->current_playid));

            if (empty($chat)) {
                continue;
            }

            $chatid = $chat->chat_id;

            $messages = Aechatmessages::model()->findBySql("SELECT * FROM ae_chat_messages WHERE chat_id = " . $chatid . ' AND
             author_play_id = ' . $match_id . ' ORDER BY id DESC');

            if (isset($messages->chat_message_timestamp) && isset($messages->chat_message_is_read) && $messages->chat_message_is_read == 0) {
                return true;
            }
        }

        return false;
    }

    public function filterBySex($one,$opposites=false,$loose=true){
        $gender = isset($one['gender']) ? $one['gender'] : 'man';
        $myGender = $this->getSavedVariable('gender');

        $otherMatchWomen = isset($one['women']) ? $one['women'] : 0;
        $otherMatchMen = isset($one['men']) ? $one['men'] : 0;

        $match_women = $this->getSavedVariable('women');
        $match_men = $this->getSavedVariable('men');

        $otherMatches = false;

        if ($otherMatchMen && $otherMatchMen) {
            $otherMatches = true;
        } else if ($otherMatchWomen && ($myGender == 'woman' || $myGender == 'female' || $myGender == 'women')) {
            $otherMatches = true;
        } else if ($otherMatchMen && ($myGender == 'man' || $myGender == 'male' || $myGender == 'men')) {
            $otherMatches = true;
        }

        if(!$match_women){ $this->getSavedVariable('look_for_women',true); }
        if(!$match_men){ $this->getSavedVariable('look_for_men',true); }

        if($opposites ){
            if($this->getSavedVariable('gender') == $gender){
                return false;
            } else {
                return true;
            }
        }

        if(($gender == 'man' OR $gender == 'male' OR $gender == 'men') AND $match_men == true){
            $sex = true;
        } elseif(($gender == 'woman' OR $gender == 'female' OR $gender == 'women') AND $match_women == true){
            $sex = true;
        } else {
            $sex = false;
        }

        if($loose){
            return $sex;
        }

        return $sex && $otherMatches;
    }

	public function getAMatch($users,$loose=false) {

		$outvars = [];
		$pointer = false;

		foreach ($users as $i => $user) {

			if ( !isset($user['play_id']) ) {
				continue;
			}

			$id = $user['play_id'];

			$vars['play_id'] = $id;
			$vars['distance'] = $user['distance'];
			$vars['hidden-friends'] = isset($user['hidden-friends']) ? $user['hidden-friends'] : null;

			$profilepic = isset($user['profilepic']) ? $user['profilepic'] : false;
			$filecheck = $this->imagesobj->findFile($profilepic);
            $filter = $this->filter( $user ,$loose);

			// its important that we exclude non-matches as otherwise we will sooner or later
			// run out of matches as only the skipped get excluded by query, rest is handled here in php
			if ( !$filter OR !$filecheck ) {
			    //echo('Ignored ' . $id . 'filecheck:' . $filecheck .'filter:' . $filter);
				$this->addToDebug('Ignored ' . $id . 'filecheck:' . $filecheck .'filter:' . $filter);
				$this->mobilematchingobj->playid_otheruser = $id;
				$this->mobilematchingobj->skipMatch(false,true);
			} else {
				$outvars[] = $user;
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

		return false;
	}

    public function filter($one,$loose=false)
    {

        if ($this->filterByHiddenFriends($one) == false AND !$one['lat']) {
            $this->addToDebug('Filtered by user visibility restrictions');
            return false;
        }

        if ($this->filterByBlocked($one) == false) {
            $this->addToDebug('Filtered due to blocking');
            return false;
        }

        if (isset($one['hide_user']) AND $one['hide_user'] == '1' AND !$one['lat']) {
            return false;
        }

        return true;
    }


    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Check if the currently filtered user wants to be hidden from the filtering user.
     *
     * @param $one
     * @return bool
     */
    public function filterByHiddenFriends($one)
    {
        $hideFromFriends = $this->getVariable('hide-from-fb-friends');
        $friendIds = json_decode($this->getVariable('facebook-friends-ids'));

        // No Facebook friends IDs - so don't filter
        if ( !isset($one['facebook-friends-ids']) ) {
            return false;
        }

        if(in_array($this->getVariable('fb_id'), json_decode($one['facebook-friends-ids'])) &&
            (isset($one['hide-from-fb-friends']) && $one['hide-from-fb-friends'] == 1)) {
            return false;
        }

        if (
            (isset($one['fb_id']) AND in_array((string)$one['fb_id'], $friendIds)) &&
            $hideFromFriends
        ) {
            return false;
        }

        return true;
    }

    public function filterByUnmatched($one)
    {
        $unmatchedUsers = isset($one['unmatched_users']) ? json_decode($one['unmatched_users']) : null;
        $activeUserUnmatched = json_decode($this->getVariable('unmatched_users'));
        $activeUserUnmatched = empty($activeUserUnmatched) ? array() : $activeUserUnmatched;

        foreach ($activeUserUnmatched as $item) {
            if ($item[0] == $one['play_id']) {
                return false;
            }
        }

        if (!empty($unmatchedUsers)) {
            foreach ($unmatchedUsers as $item) {
                if ($item[0] == $this->playid) {
                    return false;
                }
            }
        }

        return true;
    }

    public function filterByBlocked($one)
    {
        $blockedUsers = isset($one['blocked_users']) ? json_decode($one['blocked_users']) : null;
        $activeUserBlocked = json_decode($this->getVariable('blocked_users'));
        $activeUserBlocked = empty($activeUserBlocked) ? array() : $activeUserBlocked;

        foreach ($activeUserBlocked as $item) {
            if ($item[0] == $one['play_id']) {
                return false;
            }
        }

        if (!empty($blockedUsers)) {
            foreach ($blockedUsers as $item) {
                if ($item[0] == $this->playid) {
                    return false;
                }
            }
        }

        return true;
    }

    public function swipe($swipestack)
    {
        $nope = 'nope_p.png';
        $like = 'like_p.png';

        if ($this->getConfigParam('actionimage3')) {
            $nope = $this->getConfigParam('actionimage3');
        }

        if ($this->getConfigParam('actionimage4')) {
            $like = $this->getConfigParam('actionimage4');
        }

        $prev_swipes = $this->getPreviousSwipes();

        return $this->getSwipestack($swipestack, array(
            'swipe_back_content' => $prev_swipes,
            'margin' => '0 0 0 0',
            'overlay_left' => $this->getImage($nope, array('text-align' => 'right', 'width' => '100', 'height' => '100')),
            'overlay_right' => $this->getImage($like, array('text-align' => 'left', 'width' => '100', 'height' => '100'))
        ));
    }

    public function getPreviousSwipes()
    {

        $current_prev_swipes = $this->getVariable('swiped_back_ids');
        $current_prev_swipes = @json_decode($current_prev_swipes, true);

        // No previous swipes
        if (empty($current_prev_swipes) OR count($current_prev_swipes) < 5) {
            return array();
        }

        // Check whether the current user has a match with a swiped user

        $users_data = $this->mobilematchingobj->getUsersByIDs(array_keys($current_prev_swipes),$this->units,$this->viewname);

        if (empty($users_data)) {
            return array();
        }

        $prev_swipestack = $this->buildSwipeStack($users_data, false);

        $return = array();

        foreach (array_reverse($prev_swipestack) as $i => $item) {

            if (!isset($current_prev_swipes[$item->swipe_id])) {
                continue;
            }

            $item->prevswipeid = $current_prev_swipes[$item->swipe_id] . $item->swipe_id;

            // Return 5 items at a time
            if ($i > 4) {
                continue;
            }

            $return[] = $item;
        }

        return $return;
    }

    public function getBtns($id, $detail_view, $i) {

        $btn_no_img = 'icn_dislike.png';
        if ($this->getConfigParam('actionimage5')) {
            $btn_no_img = $this->getConfigParam('actionimage5');
        }

        $btn_yes_img = 'icn_like.png';
        if ($this->getConfigParam('actionimage6')) {
            $btn_yes_img = $this->getConfigParam('actionimage6');
        }

        $column[] = $this->getSwipebackButton();
        $column[] = $this->getImagebutton($btn_no_img, 'no_' . $id, false, array('width' => '25%', 'margin' => '0 5 0 5', 'action' => 'swipe-left'));
        $column[] = $this->getProfileBoostButton();
        $column[] = $this->getImagebutton($btn_yes_img, 'yes_' . $id, false, array('width' => '25%', 'action' => 'swipe-right', 'margin' => '0 5 0 5'));
        $column[] = $this->getSuperlikeButton( $id );

        return $this->getRow($column, array(
        	'width' => '100%',
        	'text-align' => 'center',
	        'noanimate' => true
        ));
    }

    public function myMatches()
    {
        $this->rewriteActionConfigField('background_color', '#f9fafb');
        $this->rewriteActionField('subject', 'Messages');

        if ($this->getConfigParam('mymatches_search_withscreenname')) {
            $this->screenNameSearch();
        }

        $matches = $this->mobilematchingobj->getMyMatches();

        $this->matchList($matches);

        if ($this->getConfigParam('mymatches_show_group_chats')) {
            $params['mode'] = 'mychats';
            $params['expended_connection_ids'] = $this->expended_connection_ids;
            $this->data->scroll[] = $this->getSettingsTitle('{#group_chats#}');
            $this->data->scroll[] = $this->moduleGroupChatList($params);
        }

        if ($this->getConfigParam('mymatches_show_incoming_requests')) {
            $this->getIncomingRequests();
        }

        if ($this->getConfigParam('mymatches_show_outgoing_requests')) {
            $this->getOutgoingRequests();
        }

    }

    public function matchList($matches, $groupchat = false, $message = '{#no_matches#}')
    {

        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#matches#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center'
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
            'padding' => '10 20 10 20',
            'width' => '100%'
        ));
        $this->data->header[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        if (empty($matches)) {
            $othertxt['style'] = 'imate_title_nomatch';
            $this->data->scroll[] = $this->getText($message, $othertxt);
            return true;
        }

        foreach ($matches as $key => $res) {
            $vars = AeplayVariable::getArrayOfPlayvariables($res);

            if (empty($vars)) {
                continue;
            }

            if (!isset($vars['profilepic'])) {
                $vars['profilepic'] = 'anonymous2.png';
            }

            if (isset($this->expended_connection_ids[$res])) {
                continue;
            }

            $this->getMyMatchItem($vars, $res);
            $this->expended_connection_ids[$res] = true;
        }

    }

    public function getMyMatchItem($vars, $id, $search = false)
    {
        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        if (isset($vars['real_name']) AND $vars['real_name']) {
            $name = $vars['real_name'];
        } else if (isset($vars['name']) AND $vars['name']) {
            $name = $vars['name'];
        } else {
            $name = '{#anonymous#}';
        }

        $image_onclick = new stdClass();
        $image_onclick->action = 'open-action';
        $image_onclick->id = $id;
        $image_onclick->back_button = true;
        $image_onclick->sync_open = true;
        $image_onclick->sync_close = true;
        $image_onclick->action_config = $this->requireConfigParam('detail_view');

        $unread = false;

        /* notification column */
        $contextkey = $this->getTwoWayChatId($id, $this->current_playid);
        $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey, 'chat_user_play_id' => $this->current_playid));

        /* there is a situation where the record might not exist, so we should create it */
        if (!is_object($chat)) {
            $ouchat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey));
            if (is_object($ouchat)) {
                $ob = new Aechatusers();
                $ob->chat_id = $ouchat->chat_id;
                $ob->context_key = $contextkey;
                $ob->chat_user_play_id = $this->current_playid;
                $ob->context = $ouchat->context;
                $ob->insert();
                $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey, 'chat_user_play_id' => $this->current_playid));
            }
        }

        if (is_object($chat) AND isset($chat->chat_id)) {
            $chatid = $chat->chat_id;
            $messages = Aechatmessages::model()->findBySql("SELECT * FROM ae_chat_messages WHERE chat_id = " . $chatid . ' AND
             author_play_id = ' . $id . ' ORDER BY id DESC');

            if (isset($messages->chat_message_timestamp)) {
                $timestamp = strtotime($messages->chat_message_timestamp);
                $time = Controller::humanTiming($timestamp);

                if (isset($messages->chat_message_is_read) AND $messages->chat_message_is_read == 0) {
                    $unread = true;
                }
            }
        }

        if (isset($vars['private_photos']) AND $vars['private_photos'] AND $this->getConfigParam('article_action_theme') == 'sila') {
            $test = AeplayKeyvaluestorage::model()->findByAttributes(array('play_id' => $id, 'key' => 'two-way-matches', 'value' => $this->playid));
            if (!is_object($test)) {
                $profilepic = 'sila-private-photos.png';
            }
        }

        $img_args = array(
            'crop' => 'round',
            'imgwidth' => 250,
            'imgheight' => 250,
            'vertical-align' => 'middle',
            'text-align' => 'center',
            'margin' => '5 1 5 1',
            'width' => '60',
            'priority' => 9,
            'onclick' => $image_onclick
        );

        if ($this->userUnmatched($id)) {
            $img_args['blur'] = 1;
        }

        $left_col_rows[] = $this->getImage($profilepic, $img_args);

        if ($unread) {
            $right_col_rows[] = $this->getImage('red-dot.png', array('width' => '10', 'margin' => '5 10 0 0', 'float' => 'right', 'floating' => 1));
        }

        $right_col_rows[] = $this->getRow(array(
            $this->getText($name, array(
                'style' => 'imate_title',
            )),
        ));

        if (isset($time)) {
            $txt = ($time == '{#now#}') ? $time : $time . ' ago';
            $right_col_rows[] = $this->getRow(array(
                $this->getText('{#last_message#} - ' . $txt, array('style' => 'imate_title_msgtime')),
            ));
        }

        if (isset($messages) AND isset ($messages->chat_message_text)) {
            if (strlen($messages->chat_message_text) > 35) {
                $shortext = $this->truncate_words($messages->chat_message_text, 4) . '...';
            } else {
                $shortext = $messages->chat_message_text;
            }
        } elseif ($profiledescription) {
            if (strlen($profiledescription) > 35) {
                $shortext = $this->truncate_words($profiledescription, 4) . '...';
            } else {
                $shortext = $profiledescription;
            }
        } else {
            $shortext = '';
        }

        $right_col_rows[] = $this->getRow(array(
            $this->getText($shortext, array(
                'style' => 'imate_title_subtext',
            )),
        ));

        $col_left = $this->getColumn(
            $left_col_rows
            , array('width' => '23%', 'text-align' => 'center',));

        $col_right = $this->getColumn(
            $right_col_rows
            , array('width' => '75%', 'vertical-align' => 'middle'));

        $rowparams['margin'] = '0 0 0 0';
        $rowparams['background-color'] = '#FFFFFF';

        $append_location = 'scroll';

        if ($search) {
            $rowparams['padding'] = '10 20 10 20';
            $rowparams['margin'] = '0 0 0 0';
            $append_location = 'footer';
        }

        $rowparams['vertical-align'] = 'middle';
        $rowparams['height'] = '65';

        $open_chat_click = new stdClass();
        $open_chat_click->action = 'open-action';
        $open_chat_click->id = $this->getTwoWayChatId($id, $this->current_playid);
        $open_chat_click->back_button = true;
        $open_chat_click->sync_open = true;
        $open_chat_click->sync_close = true;
        $open_chat_click->viewport = 'bottom';
        $open_chat_click->action_config = $this->requireConfigParam('chat');

        $rowparams['onclick'] = $open_chat_click;

        $this->data->{$append_location}[] = $this->getRow(array(
            $col_left,
            $col_right
        ), $rowparams);
        $this->data->{$append_location}[] = $this->getHairline('#F5F5F5');
    }

    public function getCard($profilepic, $detail_view, $distance, $piccount, $id, $one, $i)
    {

        $this->metas->current_playid = $id;
            
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $detail_view;
        $onclick->sync_open = 1;
        $onclick->id = $id;

        // initiate dark magic
        $this->sessionSet('distance-' . $id, $distance);

        $age = 'N/A';
        if ( isset($one['birth_year']) ) {
            $age = date('Y') - $one['birth_year'];
        } else if ( isset($one['age']) AND $one['age'] ) {
            $age = $one['age'];
        }

        $name = (isset($one['real_name']) ? $one['real_name'] : '{#anonymous#}');

        if (strlen($name) > 18) {
            $name = substr($name, 0, strpos(wordwrap($name, 18), "\n"));
        }

        $title_text = $name . ', ' . $age;
        if (
            $this->metas->checkMeta('hide-age') AND
            (isset($one['profile_age_invisible']) AND $one['profile_age_invisible'])
        ) {
            $title_text = $name;
        }

        $distance_text = $distance . ' ' . $this->units . ' away';

        if (
            $this->metas->checkMeta('hide-distance') AND
            (isset($one['profile_location_invisible']) AND $one['profile_location_invisible'])
        ) {
            $distance_text = '';
        }

        if(!$one['lat'] OR $one['lat'] == '0.00000000'){
            $distance_text = '';
        }

        $toolbar[] = $this->getRow(array(
            $this->getDataColumn($title_text, $distance_text),
            $this->getBoostedColumn(),
        ), array(
            'vertical-align' => 'bottom',
            'background-image' => $this->getImageFileName('shadow-image-wide.png'),
            'background-size' => 'cover',
            'height' => '180',
            'width' => '100%',
            'border-radius' => '5',
        ));

        $card = $this->getColumn($toolbar, array(
            'border-radius' => '5',
            'background-image' => $this->getImageFileName($profilepic),
            'background-size' => 'cover',
            'margin' => '0 0 0 0',
            'padding' => '0 0 0 0',
            'shadow-color' => '#66000000',
            'shadow-radius' => '4',
            'shadow-offset' => '0 3',
            'width' => $this->screen_width - 60,
            'height' => $this->screen_width - 60,
            'vertical-align' => 'bottom',
            'onclick' => $onclick
        ));

        return $this->getColumn(array($card), array(
            'background-color' => '#FAFAFA',
            'width' => $this->screen_width - 60,
            'height' => $this->screen_width - 60,
            'margin' => '50 0 0 0',
            'border-radius' => '5',
            'leftswipeid' => 'left' . $id,
            'rightswipeid' => 'right' . $id,
            'backswipeid' => 'back' . $id,
            'shadow-color' => '#66000000',
            'shadow-radius' => '4',
            'shadow-offset' => '0 3',
        ));
    }

    public function getDataColumn($title_text, $distance_text)
    {

        $items[] = $this->getText($title_text, array(
            'width' => '100%',
            'font-size' => 18,
            'color' => '#FFFFFF',
            'border-radius' => '5',
            'font-ios' => 'VarelaRound',
            'font-android' => 'VarelaRound',
        ));

        if ( $distance_text ) {
            $items[] = $this->getRow(array(
                $this->getImage('icon-marker.png', array(
                    'width' => '13',
                    'margin' => '0 5 0 0'
                )),
                $this->getText($distance_text, array(
                    'width' => '100%',
                    'font-size' => 13,
                    'color' => '#FFFFFF'
                )),
            ), array(
                'margin' => '5 0 0 0',
                'border-radius' => '5'
            ));
        }

        return $this->getColumn($items, array(
            'width' => '70%',
            'padding' => '10 0 10 20',
            'vertical-align' => 'bottom',
            'height' => '75',
        ));
    }

    public function getBoostedColumn()
    {

        $data = array();

        if ($this->metas->checkMeta('spark-profile')) {
            $data[] = $this->getColumn(array(
                $this->getImage('ic_home_boost.png', array(
                    'width' => '40',
                ))
            ));
        }

        $superlikes = AeplayKeyvaluestorage::model()->findByAttributes(array(
            'play_id' => $this->metas->current_playid,
            'key' => 'superlikes',
            'value' => $this->playid
        ));

        if (is_object($superlikes)) {
            $data[] = $this->getColumn(array(
                $this->getImage('ic_home_superlike.png', array(
                    'width' => '40',
                ))
            ));
        }

        return $this->getColumn(array(
            $this->getRow($data, array(
                'text-align' => 'right',
            ))
        ), array(
            'width' => '30%',
            'padding' => '10 20 10 0',
            'vertical-align' => 'bottom',
            'height' => '75',
        ));
    }

    public function itsAMatch($id = false)
    {

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';
        $params['priority'] = 9;

        if ($this->getConfigParam('actionimage2')) {
            $match_img = $this->getConfigParam('actionimage2');
            $row[] = $this->getImage($match_img, $params);
        } else {
            $row[] = $this->getSpacer(20);
        }

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

        $pics[] = $this->getImage($profilepic, $params);
        $pics[] = $this->getImage($this->getVariable('profilepic'), $params);

        $onclick = new stdClass();
        $onclick->action = 'hide-div';
        $onclick->div_id = 'div1';

        $clear = new stdClass();
        $clear->action = 'submit-form-content';
        $clear->id = 'keep-playing';

        $clicker = array();
        $clicker[] = $onclick;
        $clicker[] = $clear;

        $this->data->divs['div1'] = $this->getColumn(array(
            $this->getRow(array(
                $this->getImage('itsmatch.png', array(
                    'width' => '70%',
                ))
            ), array(
                'width' => '100%',
                'margin' => '50 0 0 0',
                'text-align' => 'center'
            )),
            $this->getRow($pics, array(
                'width' => '100%',
                'text-align' => 'center',
                'margin' => '20 0 0 0'
            )),
            $this->getColumn(array(
                $this->getImage('ic_its_a_match_heart.png', array(
                    'width' => '50'
                ))
            ), array(
                'width' => '100%',
                'text-align' => 'center',
                'margin' => '-80 0 0 0',
            )),
            $this->getText('{#you_and#} ' . $this->getFirstName($vars) . lcfirst(' {#like_each_other#}'), array(
                'width' => '100%',
                'text-align' => 'center',
                'color' => '#FFFFFF',
                'margin' => '50 0 0 0'
            )),
            $this->getSpacer('50'),
            $this->getTextbutton('{#chat_with#} ' . $this->getFirstName($vars), array(
                'style' => 'desee_general_button_style',
                'id' => $this->getTwoWayChatId($id, $this->current_playid),
                'sync_open' => '1',
                'sync_close' => '1',
                'action' => 'open-action',
                'config' => $chatid,
                'viewport' => 'bottom'
            )),
            $this->getSpacer(10),
            $this->getTextbutton('Keep surfing', array(
                'style' => 'desee_green_button_style',
                'id' => 'id',
                'onclick' => $clicker
            )),
            $this->getSpacer('50')
        ), array(
            'width' => '100%',
        ));

        $onload = new stdClass();
        $onload->action = 'show-div';
        $onload->div_id = 'div1';
        $onload->tap_to_close = 1;
        $onload->transition = 'fade';
        $onload->background = 'blur';
        $onload->layout = new stdClass();
        $onload->layout->top = 10;
        $onload->layout->right = 10;
        $onload->layout->left = 10;

        $this->data->onload[] = $onload;

        $this->setFooter();
    }

    public function notFound()
    {

        $this->configureBackground('actionimage1');

        $row[] = $this->getSpacer('60');
        $row[] = $this->getText('{#there_is_no_one_new_arround_you#}!', array('style' => 'desee-black-text'));
        $row[] = $this->getRow(array(
            $this->getImage('icon-preferences.png', array(
                'width' => '70',
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '15 0 15 0',
        ));

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('preferences');
        $row[] = $this->getText('{#change_search_preferences#}', array(
            'style' => 'desee-orange-text',
            'onclick' => $onclick
        ));

        $row[] = $this->getSpacer('10');
        $row[] = $this->getText('or', array('style' => 'desee-black-text'));
        $row[] = $this->getRow(array(
            $this->getImage('m_change_location.png', array(
                'width' => '70',
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '15 0 15 0',
        ));

        if ( !$this->metas->checkMeta( 'change-location', $this->playid )) {
            $row[] = $this->getText('Change your location to anywhere in the world for next 30 days and swipe.', array(
                'padding' => '5 20 15 20',
                'text-align' => 'center',
                'font-size' => '16',
            ));

            $row[] = $this->getRow(array(
                $this->getText('{#buy_now#}', array(
                    'style' => 'desee-matching-orange-button',
                )),
            ), array(
                'margin' => '10 20 10 20',
                'onclick' => $this->getOnclick('purchase', false, array(
                    'product_id_ios' => 'change_location.01',
                    'product_id_android' => 'change_location.001',
                )),
            ));
        } else {
            $row[] = $this->getRow(array(
                $this->getText('{#change_your_current_location#}', array(
                    'style' => 'desee-matching-orange-button',
                )),
            ), array(
                'margin' => '10 20 10 20',
                'onclick' => $this->getOnclick('permaname', false, 'location')
            ));
        }

        $this->data->scroll[] = $this->getColumn($row);

        $this->setFooter();
    }

    public function viewLimitReached()
    {

	    $this->setHeader();

        $options['mode'] = 'countdown';
        $options['style'] = 'swipes-timer';
        $options['timer_id'] = 'swipes-timer';

        $last_swipe = $this->mobilematchingobj->getLastSwipe();

        $row[] = $this->getSpacer('50');
        $row[] = $this->getText('{#out_of_swipes#}!', array('style' => 'desee-black-text'));

        if ($last_swipe) {

            $last_swipe_time = @strtotime($last_swipe['timestamp']);
            $seconds_left = $this->default_time - (time() - $last_swipe_time);

            $row[] = $this->getRow(array(
                $this->getTimer($seconds_left, $options),
            ), array(
                'padding' => '4 4 4 4',
                'margin' => '10 0 0 0',
                'text-align' => 'center',
            ));

        }

        $row[] = $this->getRow(array(
            $this->getImage('m_unlimited_swipes.png', array(
                'width' => '70',
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '10 0 10 0',
        ));

        $row[] = $this->getText('Get unlimited swipes for the next 30 days', array(
            'padding' => '5 20 5 20',
            'text-align' => 'center',
            'font-size' => '15',
        ));

        $row[] = $this->getRow(array(
            $this->getText('{#buy_now#}', array(
                'width' => '100%',
                'background-color' => '#fec02e',
                'color' => '#1d0701',
                'padding' => '13 5 13 5',
                'text-align' => 'center',
                'border-radius' => '8',
            )),
        ), array(
            'margin' => '10 20 10 20',
            'onclick' => $this->getOnclick('purchase', false, array(
                'product_id_ios' => 'unlimited_swipes.01',
                'product_id_android' => 'unlimited_swipes.001',
            )),
        ));

        $this->data->scroll[] = $this->getColumn($row, array(
            'text-align' => 'center',
        ));

        $this->setFooter();

    }

    private function getSwipebackButton()
    {

        if ( !$this->metas->checkMeta( 'swipe-back', $this->playid ) ) {
            $this->registerProductDiv( 'buy-swipeback', 'm_swipe_back.png', '{#retrace_swipes#}', 'Retrace swipes up to 5 steps back for next 30 days','retrace_swipes.01', 'retrace_swipes.001' );

            return $this->getColumn(array(
                $this->getImage('ic_rewind_2.png')
            ), array(
                'width' => '15%',
                'vertical-align' => 'bottom',
                'onclick' => $this->showProductDiv( 'buy-swipeback' ),
            ));
        }

        $current_swipes = $this->getVariable('swiped_back_ids');
        $current_swipe_array = @json_decode($current_swipes, true);

        $count = count($current_swipe_array);

        if ($count <= 5) {

            $this->data->divs['swipeback-notification'] = $this->getColumn(array(
                $this->getRow(array(
                    $this->getText('{#you_need_to_swipe_at_least_10_people#}')
                ), array(
                    'width' => '100%',
                    'text-align' => 'center',
                    'margin' => '0 0 50 0',
                )),
            ), array(
                'width' => '100%',
            ));

            $onclick = new stdClass();
            $onclick->action = 'show-div';
            $onclick->div_id = 'swipeback-notification';
            $onclick->tap_to_close = 1;
            $onclick->transition = 'fade';
            $onclick->background = 'blur';
            $onclick->layout = new stdClass();
            $onclick->layout->bottom = 10;
            $onclick->layout->right = 10;
            $onclick->layout->left = 10;

            return $this->getColumn(array(
                $this->getImage('ic_rewind_2.png')
            ), array(
                'width' => '15%',
                'vertical-align' => 'bottom',
                'onclick' => $onclick,
            ));

        }

        // We should always have at least five items in the stack
        $possible_back_swipes = $count - 5;

        // Allow only 5 backswipes at a time
        // In order to keep the track correct, we should remove all unnecessary swipes from the swiped_back_ids array
        if ($count >= 10) {
            $this->resetBackswipesStack($current_swipe_array);
            $possible_back_swipes = 5;
        }

        return $this->getColumn(array(
            $this->getImagebutton('ic_rewind_2.png', 'do-swipe-back', false, array(
                'action' => 'swipe-back',
            )),
            $this->getText($possible_back_swipes, array(
                'style' => 'swipe-counter-tooltip'
            )),
        ), array(
            'width' => '15%',
            'vertical-align' => 'bottom',
        ));
    }

    private function getProfileBoostButton() {
        if ($this->metas->checkMeta('spark-profile', $this->playid)) {
            return $this->getImagebutton('blank-placeholder.png', '', false, array('width' => '10%', 'margin' => '25 0 0 0'));
        }

        $this->registerProductDiv( 'buy-spark-profile', 'm_spark_profile.png', '{#spark_profile#}', 'Spark your profile for next 12 hours and be noticed more by potential matches.', 'spark_profile.01', 'spark_profile.001' );

        return $this->getImage('icn_boost_profile.jpg', array(
            'width' => '10%',
            'margin' => '25 0 0 0',
            'onclick' => $this->showProductDiv( 'buy-spark-profile' ),
        ));
    }

    private function getSuperlikeButton( $id ) {

        $superlikes_left = $this->metas->checkMeta('extra-superhot', $this->playid);

        if ( !$superlikes_left ) {
            $this->registerProductDiv( 'buy-superhot', 'm_extra_superhot.png', '{#superhot#}', 'Get 5 SuperHots per day for next 30 days.', 'superhot.01', 'superhot.001' );

            return $this->getColumn(array(
                $this->getImage('icn_superlike.png'),
            ), array(
                'width' => '15%',
                'vertical-align' => 'bottom',
                'onclick' => $this->showProductDiv( 'buy-superhot' ),
            ));
        }

        $swipe_right = new StdClass();
        $swipe_right->action = 'swipe-right';

        return $this->getColumn(array(
            $this->getImage('icn_superlike.png'),
            $this->getText($superlikes_left, array(
                'style' => 'swipe-counter-tooltip'
            )),
        ), array(
            'width' => '15%',
            'vertical-align' => 'bottom',
            'onclick' => array(
                $this->getOnclick('id', false, 'superlikes-' . $id),
                $swipe_right
            ),
        ));
    }

    private function resetBackswipesStack($current_swipe_array)
    {

        if (empty($current_swipe_array)) {
            return false;
        }

        $swipes_reversed = array_reverse($current_swipe_array, true);
        $count = 0;

        foreach ($swipes_reversed as $id => $swipe_type) {
            $count++;

            if ($count > 10) {
                unset($swipes_reversed[$id]);
            }
        }

        $backswipes = json_encode($swipes_reversed);
        $this->saveVariable('swiped_back_ids', $backswipes);

        return true;
    }

    private function showLimit()
    {

        $allowed_swipes = $this->getConfigParam('total_swipes_per_day');

        if (!$this->metas->checkMeta('unlimited-swipes', $this->playid)) {
            $totals = $this->mobilematchingobj->getTotalSwipes();
            if ($totals >= $allowed_swipes) {
                return true;
            }
        }

        return false;
    }

    private function userUnmatched($play_id)
    {
        $unmatched_list = json_decode($this->getVariable('unmatched_me'));

        if (empty($unmatched_list)) {
            return false;
        }

        foreach ($unmatched_list as $user) {
            if ($user[0] == $play_id) {
                return true;
            }
        }

        return false;
    }

    public function canShowAds()
    {
        if ($this->metas->checkMeta('no-ads', $this->playid)) {
            return false;
        }

        return true;
    }

    private function getNextMutualLikeCounter() {
        $args = array(
            'width' => $this->screen_width / 3.5,
            'text-align' => 'center',
            'vertical-align' => 'middle',
        );

        $ob_data[] = $this->getImage('next_like.png', array(
            'width' => '35'
        ));

        if (!$this->metas->checkMeta('next-mutual-likes', $this->playid)) {
            $this->registerProductDiv( 'buy-next-mutual-like', 'm_next_mutual_like.png', '{#match_countdown#}', 'Show number of swipes away from mutual swipe for next 30 days.', 'match_countdown.01', 'match_countdown.001' );
            $args['onclick'] = $this->showProductDiv( 'buy-next-mutual-like' );
        } else {
            $ob_data[] = $this->getText($this->next_mutual_like_num, array(
                'margin' => '-3 0 0 3',
                'color' => '#ff6600',
            ));
        }

        return $this->getRow($ob_data, $args);
    }

    public function handleExtrasPayments() {

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->metas->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $this->metas->play_id = $this->playid;
        $this->metas->meta_key = $card_config['trigger'];
        $this->metas->meta_value = ( $card_config['measurement'] == 'time' ? time() : $card_config['amount'] );
        $this->metas->meta_limit = $card_config['measurement'];
        $this->metas->saveMeta();

        return true;
    }

    private function determineNextMutualLike() {

        if ( empty($this->users) ) {
            $this->next_mutual_like_num = false;
        } else {
            foreach ($this->users as $index => $user) {
                if ( isset($user['status']) AND $user['status'] == 'matches' ) {
                    $this->next_mutual_like_num = $index + 1;

                    // Brake the loop after the first encounter of a "matched" user.
                    break;
                }
            }

            if ( empty($this->next_mutual_like_num) OR $this->next_mutual_like_num > 30 ) {
                $this->next_mutual_like_num = '30+';
            }

        }


        return true;
    }

    public function triggerConfirmationPush( $play_id ) {
        $name = $this->getFirstName( $this->varcontent );
        $text = '{#you_have_a_new_match_with#} ' . $name;
        $description = '{#you_can_chat_right_away#}!';
        Aenotification::addUserNotification( $play_id, $text, $description, '+1', $this->gid );

        return true;
    }

}