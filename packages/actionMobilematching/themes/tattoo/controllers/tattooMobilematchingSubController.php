<?php

Yii::import('application.modules.aelogic.packages.actionMtattoos.models.*');

use packages\actionMtattoos\Models\TattooModel;

class tattooMobilematchingSubController extends MobilematchingController
{

	public function matches()
	{

		if ( isset($this->params['swid']) AND $this->menuid == 'yes' ) {
			$id = $this->params['swid'];
			$this->likeTattoo($id);
		}

		if ( isset($this->params['swid']) AND $this->menuid == 'no' ) {
			$id = $this->params['swid'];
			$this->skipTattoo($id);
		}

		$this->showMatches();

	}

    public function showMatches($skipfirst = false)
    {

        $this->rewriteActionConfigField('background_color', '#ffffff');

        $this->getHeader();

        $tattoos = \packages\actionMitems\Models\Model::findUnlikedItems($this->playid);

        // shuffle list to avoid tattoos by the same artist stacking up
        shuffle($tattoos);

        if ($this->mobilematchingobj->debug) {
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        if (empty($tattoos)) {
            $this->notFound();
            return false;
        } else {
            $swipestack = $this->buildSwipeStack($tattoos, $skipfirst, false);
        }

        if (empty($swipestack)) {
            return false;
        }

        $this->data->scroll[] = $this->getSwipearea($swipestack, array(
            'id' => 'mainswipe',
            'item_scale' => 1,
            'dynamic' => 1,
            'margin' => '10 0 0 0',
            'item_width' => '95%',
            'remember_position' => 1,
            'transition' => 'tablet',
            'world_ending' => 'refill_items',
            'height' => 'auto',
            'lazy' => 0,
        ));

        $this->data->scroll[] = $this->getBtns(1, $this->requireConfigParam('detail_view'), 1);

        $aechat = new Aechat;
        $aechat->play_id = $this->current_playid;
        $unreadMessageCount = $aechat->getUsersUnreadCount();

        if($unreadMessageCount){
            $openMessages = new stdClass();
            $openMessages->action = 'open-action';
            $openMessages->action_config = $this->getActionidByPermaname('messages');

            $this->data->footer[] = $this->getRow(array(
                $this->getRow(array(
                    $this->getImage('close-envelope.png', array(
                        'width' => '22',
                    ))
                ), array(
                    'text-align' => 'left'
                )),
                $this->getRow(array(
                    $this->getText('You have ' . $unreadMessageCount . ' unread messages', array(
                        'color' => '#ffffff'
                    ))
                ), array(
                    'text-align' => 'left',
                    'margin' => '0 0 0 10'
                ))

            ), array(
                'background-color' => '#29292c',
                'padding' => '15 20 10 20',
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'onclick' => $openMessages
            ));
        }

        return true;
    }

    private function getHeader() {

        $openSidemenu = new stdClass();
        $openSidemenu->action = 'open-sidemenu';

        $openFilters = new stdClass();
        $openFilters->action = 'open-action';
        $openFilters->action_config = $this->getActionidByPermaname('filter');
        $openFilters->sync_close = 1;

        $this->data->header[] = $this->getRow(array(
            $this->getImage('icon-open-menu.png', array(
                'onclick' => $openSidemenu,
                'width' => '40',
                'padding' => '0 5 0 12',
            )),
            $this->getText('Discover Tattoos', array(
                'color' => '#ffffff',
                'width' => 'auto',
                'text-align' => 'center',
            )),
            $this->getImage('icon-menu-search.png', array(
                'width' => '22',
                'margin' => '0 12 0 5',
                'text-align' => 'right',
                'onclick' => $openFilters
            )),
        ), array(
            'vertical-align' => 'middle',
            'padding' => '5 0 5 0',
            'background-color' => '#29292c',
            'width' => 'auto'
        ));
    }

    protected function likeTattoo($id)
    {
        $like = new \packages\actionMitems\Models\ItemLikeModel();
        $like->play_id = $this->playid;
        $like->item_id = $id;
        $like->status = 'like';
        $like->save();

        $tattoo = \packages\actionMitems\Models\ItemModel::model()->findByPk($id);
    }

    protected function skipTattoo($id)
    {
        $like = new \packages\actionMitems\Models\ItemLikeModel();
        $like->play_id = $this->playid;
        $like->item_id = $id;
        $like->status = 'skip';
        $like->save();
    }

    public function buildSwipeStack($tattoos, $skipfirst, $include_buttons = true)
    {
        $detail_view = $this->getActionidByPermaname('singletattoo');
        $swipestack = array();
        $vars = $this->getAMatch($tattoos);

        $itemcount = 0;

        // Didn't find any tattoos
        if (!$vars) {
            return false;
        }

        foreach ($vars as $index => $tattoo) {
            if (is_null($tattoo['play_id'])) {
                $this->addToDebug('skipped, no play id found ');
                continue;
            }

            $id = $tattoo['id'];
            $tattoopic = isset($tattoo['itempic']) ? $tattoo['itempic'] : false;

            $piccount = $this->getPicCount($tattoo);
            $distance = 5;
            $filecheck = $this->imagesobj->findFile($tattoopic);

//            $filter = $this->filter($one);

//            if ($filter AND $filecheck AND $itemcount < 20) {
            if (true) {
                if (!$skipfirst) {
                    $this->addToDebug('added to matchstack: ' . $id);

                    $rows[] = $this->getCard($tattoopic, $detail_view, $distance, $piccount, $tattoo['id'], $tattoo, $index);
                    $rows[] = $this->getImage('bottom-part.png', array(
                    	'width' => '100%',
                    	'margin' => '0 28 0 28',
                    ));
                    //$rows[] = $this->getMatchingSpacer($id);

                    if ($include_buttons) {
                        $rows[] = $this->getBtns($id, $detail_view, $index);
                    }

                    $swipestack[] = $this->getColumn($rows, array(
                    	'text-align' => 'center',
	                    'swipe_id' => $tattoo['id'],
                    ));
                    $itemcount++;

                    unset($page);
                    unset($rows);
                    unset($toolbar);
                    unset($column);

                } else {
                    $this->addToDebug('skipped first ' . $id);
                    $skipfirst = false;
                }
            } else {
                /* its important that we exclude non-matches as otherwise we will sooner or later
                run out of matches as only the skipped get excluded by query, rest is handled here in php */

                if (!true) {
                    $this->addToDebug('Ignored ' . $id . 'filecheck:' . $filecheck . 'filter:' . true);
                    $this->mobilematchingobj->skipMatch();
                } else {
                    $this->addToDebug('Skipped for now ' . $id . 'filecheck:' . $filecheck . 'filter:' . true);
                }
            }

        }

        if (empty($swipestack) AND $this->swipe_iterations < 10) {
            $this->swipe_iterations++;
            return self::buildSwipeStack($tattoos, false, $include_buttons);
        }

        return $swipestack;
    }

    public function getAMatch($tattoos)
    {
        // Don't query more than 40 tattoos at a time, because it impacts performance
//        if (count($tattoos) > 40) {
//            $tattoos = array_splice($tattoos, 0, 40);
//        }

        $pointer = false;

        foreach ($tattoos as $i => $tattoo) {

            $vars = array();
            $images = (array)json_decode($tattoo->images);

            if (empty($images) || !isset($images['itempic'])) {
                continue;
            }

            $vars['itempic'] = $images['itempic'];
            $vars['id'] = $tattoo['id'];
            $vars['play_id'] = $tattoo['play_id'];
            $vars['name'] = $tattoo['name'];
            $vars['description'] = $tattoo['description'];
            $vars['price'] = $tattoo['price'];
            $vars['time'] = $tattoo['time'];
            $vars['images'] = $tattoo['images'];
            $vars['owner'] = AeplayVariable::getArrayOfPlayvariables($tattoo['play_id']);
            $outvars[] = $vars;
            if (!$pointer) {
                $this->mobilematchingobj->setPointer($tattoo['id']);
                $pointer = true;
            }
        }

        if (empty($outvars)) {
            $this->notFound();
            return false;
        }

        return $outvars;
    }

    public function getCard($profilepic, $detail_view, $distance, $piccount, $id, $one, $i)
    {
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $detail_view;
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;
        $onclick->id = $id;

        $toolbar[] = $this->getRow(array(
            $this->getText($one['name'], array(
                'color' => '#ffffff',
                'padding' => '0 0 10 10',
                'font-size' => '20'
            )),
        ), array(
            'vertical-align' => 'bottom',
            'background-image' => $this->getImageFileName('shadow-image-wide.png'),
            'background-size' => 'cover',
            'height' => '180',
            'width' => '100%',
            'lazy' => 0,
        ));

//        $time = $one['time'] > 0 ?
//            $one['time'] . ' h' : '';

        $time = '';
        $toolbar[] = $this->getRow(array(
            $this->getImage($one['owner']['profilepic'], array(
                'width' => '28',
                'priority' => 9,
                'crop' => 'round',
                'margin' => '0 10 0 0'
            )),
            $this->getText($one['owner']['firstname'] . ' ' . $one['owner']['lastname'], array(
                'style' => 'tattoo-artist-text'
            )),
            $this->getText($time, array(
                'color' => '#3ba95d',
                'font-size' => '18',
                'floating' => 1,
                'float' => 'right'
            ))
        ), array(
            'background-color' => '#29292c',
            'padding' => '12 10 12 10'
        ));

        $images = json_decode($one['images']);

        $card = $this->getColumn($toolbar, array(
            'border-radius' => '5',
            'background-image' => $this->getImageFileName($images->itempic, [
                'priority' => 9,
            ]),
            'background-size' => 'cover',
            'shadow-color' => '#66000000',
            'shadow-radius' => '4',
            'shadow-offset' => '0 3',
            'width' => $this->screen_width - 60,
            'height' => $this->screen_width - 60,
            'lazy' => 0,
            'vertical-align' => 'bottom',
            'onclick' => $onclick
        ));

        return $this->getColumn(array($card), array(
            'background-color' => '#FAFAFA',
            'width' => $this->screen_width - 60,
            'height' => $this->screen_width - 60,
            'lazy' => 0,
            'border-radius' => '5',
            'leftswipeid' => 'left' . $id,
            'rightswipeid' => 'right' . $id,
            'backswipeid' => 'back' . $id,
            'shadow-color' => '#66000000',
            'shadow-radius' => '4',
            'shadow-offset' => '0 3',
        ));
    }

    public function swipe($swipestack)
    {

        $nope = 'nope.png';
        $like = 'like.png';

        if ($this->getConfigParam('actionimage3')) {
            $nope = $this->getConfigParam('actionimage3');
        }

        if ($this->getConfigParam('actionimage4')) {
            $like = $this->getConfigParam('actionimage4');
        }

        return $this->getSwipestack($swipestack, array(
            'margin' => '20 20 0 20',
            'item_scale' => 1,
            'dynamic' => 1,
            'item_width' => '95%',
            'remember_position' => 1, 'transition' => 'tablet', 'world_ending' => 'refill_items',
            'overlay_left' => $this->getImage($nope, array('text-align' => 'right', 'width' => '400', 'height' => '400')),
            'overlay_right' => $this->getImage($like, array('text-align' => 'left', 'width' => '400', 'height' => '400'))
        ));
    }

    public function getBtns($id, $detail_view, $i)
    {
        $skip = new stdClass();
        $skip->action = 'swipe-delete';
        $skip->container_id = 'mainswipe';
        $skip->id = 'no';
        $skip->send_ids = 1;

        $menu = $this->getPushPermissionMenu();

        if ($menu) {
            $menu2 = new stdClass();
            $menu2->action = 'swipe-right';
            $menu2->id = 'yes_' . $id;
            $column[] =  $this->getTextbutton('{#add_to_favorites#}', array(
                'id' => '{#like_bth#}',
                'style' => 'add-to-fav',
                'onclick' => $menu
            ));
        } else {
            $like = new stdClass();
            $like->action = 'swipe-delete';
            $like->container_id = 'mainswipe';
            $like->id = 'yes';
            $like->send_ids = 1;

            $column[] =  $this->getTextbutton('{#add_to_favorites#}', array(
                'id' => '{#like_bth#}',
                'style' => 'add-to-fav',
                'onclick' => $like
            ));
        }


        return $this->getRow($column, array('text-align' => 'center', 'margin' => '20 0 20 0', 'noanimate' => true));
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

        $row[] = $this->getText('{#there_are_no_tattoos_that_match_your_criteria#}', array(
            'text-align' => 'center',
            'color' => '#000000'
        ));

        $this->setFooter();

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
        ));

    }

    public function myMatches(){

        $this->configureBackground('actionimage3');

        if($this->getConfigParam('mymatches_search_withscreenname')){
            $this->screenNameSearch();
        }

        $matches = $this->mobilematchingobj->getMyMatches();

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

    }

    public function matchList($matches, $groupchat = false, $message = '{#no_messages_yet#}')
    {
        if (empty($matches)) {
            $this->data->scroll[] = $this->getText($message, array(
                'text-align' => 'center',
                'font-weight' => 'bold',
                'font-size' => '14',
                'margin' => '10 0 0 0'
            ));
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

    public function getMyMatchItem($vars,$id,$search=false){

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        $name = $vars['firstname'] . ' ' . $vars['lastname'];
        $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;

        $row_click = new stdClass();
        $row_click->action = 'open-action';
        $row_click->id = $this->getTwoWayChatId($id,$this->current_playid);
        $row_click->back_button = true;
        $row_click->sync_open = true;
        $row_click->sync_close = true;
        $row_click->viewport = 'bottom';
        $row_click->action_config = $this->getActionidByPermaname('chat');

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

        /* unread marker */
        if ( $unread ) {
            $right_col_rows[] = $this->getImage('red-dot.png',array('width' => '10', 'margin' => '5 10 0 0', 'float' => 'right', 'floating' => 1));
        }

        $right_col_rows[] = $this->getRow(array(
            $this->getText($name, array(
            	'style' => 'imate_title'
            )),
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

        $right_col_rows[] = $this->getRow(array(
            $this->getText($shortext, array(
                'style' => 'imate_title_subtext'
            )),
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getImage( 'overlay-white.png', array( 'crop' => 'round', 'floating' => '1', ) ),
                $this->getImage($profilepic, array(
                    'style' => 'round_image_imate',
                    'priority' => 9,
                    'onclick' => $image_onclick
                )),
            ), array(
                'width' => '20%',
                'text-align' => 'center',
            )),
            $this->getColumn($right_col_rows, array(
                'width' => '80%',
                'padding' => '0 0 0 10',
                'vertical-align' => 'middle',
            ))
        ), array(
            'onclick' => $row_click,
            'margin' => '5 15 5 15',
            'padding' => '5 0 5 0',
            'height' => 'auto',
        ));

        $this->data->scroll[] = $this->getHairline('#eeeeee');

    }

    public function initMobileMatching($otheruserid=false,$debug=false){

        $theme = $this->getConfigParam('article_action_theme');
        Yii::import('application.modules.aelogic.packages.actionMobilematching.themes.'. $theme .'.models.*');
        Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

        $this->mobilematchingobj = new TattooMobilematchingModel();
        $this->mobilematchingobj->playid_thisuser = $this->current_playid;
        $this->mobilematchingobj->playid_otheruser = $otheruserid;
        $this->mobilematchingobj->gid = $this->current_gid;
        $this->mobilematchingobj->actionid = $this->actionid;
        $this->mobilematchingobj->uservars = $this->varcontent;
        $this->mobilematchingobj->factoryInit($this);
        $this->mobilematchingobj->initMatching($otheruserid,true);

        $this->mobilematchingmetaobj = new MobilematchingmetaModel();
    }

}