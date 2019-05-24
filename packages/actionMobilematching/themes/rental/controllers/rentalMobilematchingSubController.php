<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileproperties.models.*');

class rentalMobilematchingSubController extends MobilematchingController
{

    public $enable_advertising = false;

    public $current_item_id;

    // maximum items per load
    public $max_items = 30;
    public $showing_search_results;
    public $propertyModel;


    public function tab1()
    {
        $this->searchActions();
        $this->rewriteActionConfigField('background_color', '#f6f6f6');

        if ($this->getSavedVariable('new_registration')) {
            $this->deleteVariable('new_registration');
            $this->deleteVariable('temp_interests');
            $this->loadVariableContent();
        }

        $this->data = new StdClass();

        // $this->configureBackground( 'actionimage10' );
        if (strstr($this->menuid, 'delete-user-')) {
            $delid = str_replace('delete-user-', '', $this->menuid);
            UserGroupsUseradmin::model()->deleteByPk($delid);
        }

        $this->activateLocationTracking();
        if (!$this->checkForPermission()) {
            return $this->data;
        }

        if (isset($this->params['swid'])) {
            $this->current_item_id = $this->params['swid'];
            Appcaching::setGlobalCache($this->playid . 'currentitemid', $this->current_item_id);
        } else {
            $this->current_item_id = Appcaching::getGlobalCache($this->playid . 'currentitemid');
        }

        $this->askPermissions();

        Aenotification::addUserNotification($this->current_playid, '', '', '0', $this->current_gid);

        // Rewrite the action's config
        $this->rewriteActionConfigField('mobile_sharing', 1);

        if ($share_url = $this->getConfigParam('share_url')) {
            $this->rewriteActionConfigField('share_description', $share_url);
        }

        if ($share_title = $this->getConfigParam('share_title')) {
            $this->rewriteActionConfigField('share_title', $share_title);
        }

        $mode = $this->requireConfigParam('mode');
        $cachepointer = $this->current_playid . '-matchid';
        $cache = Appcaching::getGlobalCache($cachepointer);
        $regphase = $this->getSavedVariable('reg_phase');

        if ($mode == 'matching') {
            $this->getHeader(1);
        }

        if ($regphase != 'complete' AND $this->getConfigParam('register_branch') AND !$this->getSavedVariable('fb_token')) {
            $this->loadBranchList();
            $branch = $this->getConfigParam('register_branch');

            if (isset($this->available_branches[$branch])) {
                $reg = new StdClass();
                $reg->sync_open = 1;
                $reg->id = 'register';
                $reg->action = 'open-branch';
                $reg->action_config = $branch;

                $this->data->scroll[] = $this->getSpacer('40');
                $this->data->scroll[] = $this->getText('{#finish_registration_first#}', array('style' => 'imate_title'));
                $this->data->scroll[] = $this->getSpacer('40');
                $this->data->scroll[] = $this->getText('{#continue_registration#}', array('style' => 'general_button_style_red', 'onclick' => $reg));
                return $this->data;
            }
        }

        if ($this->menuid == 'keep-playing') {
            Appcaching::removeGlobalCache($cachepointer);
            $cache = false;
        }

        // Delete the current-user-branch cache
        // This is used under the My Matches section
        $branch_cache_name = 'current-user-branch-' . $this->current_playid;
        Appcaching::removeGlobalCache($branch_cache_name);

        if (strstr($this->menuid, 'markread-')) {
            $id = str_replace('markread-', '', $this->menuid);
            $this->initMobileMatching($id);
            $this->mobilematchingobj->resetNotifications();
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $this->initMobileMatching($cache);

        if ($cache AND $mode == 'matching') {
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
                $this->data->scroll[] = $this->getSpacer(15);
                $this->matchListInvites($matches);

                break;
            default:

                if (isset($this->submit['swid'])) {


                    $swid = $this->submit['swid'];
                    // Open interstitials
                    if ($this->enable_advertising == true) {
                        $this->openInterstitial();
                    }

                    if (strstr($swid, 'left')) {
                        $id = str_replace('left', '', $swid);
                        $this->initMobileMatching($id);
                        $this->mobilematchingobj->skipMatch();
                        $this->matches();
                    } elseif (strstr($swid, 'right')) {
                        $id = str_replace('right', '', $swid);
                        $this->initMobileMatching($id);

                        $this->mobilematchingobj->send_accept_push = false;

                        if ($this->mobilematchingobj->saveMatch() == true) {
                            $this->ismatch = true;
                            $this->triggerConfirmationPush($id);
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


    public function askPermissions()
    {

        if ($this->userid != $this->getSavedVariable('push_permission_checker')) {

            $onload = new StdClass();
            $onload->id = 'push-permission';
            $onload->action = 'push-permission';
            $this->data->onload[] = $onload;

            $this->saveVariable('push_permission_checker', $this->userid);
        }

    }


    public function tab2()
    {

        $this->searchActions();

        // $this->configureBackground( 'actionimage11' );
        // $this->rewriteActionField('background_image_portrait', '40f4e79cad9a95e231ffb91981680e508d77a6aa116391e2125d7cb76dcb19ae.png');

        $this->data = new stdClass();

        if ($this->current_tab != 2) {
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $this->initMobileMatching();
        $this->initMobileChat(false, false);
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
            'filter' => json_decode($this->getSavedVariable('temp_interests'), true),
            'filter_distance' => $this->getSavedVariable('temp_distance')
        ));

        if (count($chats) == 1 AND !is_array($chats)) {
            $this->data->scroll[] = $chats;
        } else {

            foreach ($chats as $row) {
                $this->data->scroll[] = $row;
            }

        }

        $onclick = new StdClass();
        $onclick->id = 'new-group-chat';
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('action_id_groupchats');
        $onclick->sync_open = 1;
        $onclick->back_button = 1;

        $this->data->footer[] = $this->getTextbutton('{#add_a_new_chat#}', array(
            'style' => 'olive-submit-button',
            'id' => 'new-chat',
            'submit_menu_id' => 'new-chat',
            'onclick' => $onclick,
        ));

        return $this->data;
    }

    public function initModel()
    {
        $this->propertyModel = new MobilepropertiesModel();
        $this->propertyModel->game_id = $this->gid;
        $this->propertyModel->play_id = $this->playid;

        /* this is one way to do it, you simply pass the submitted
        variables to your model and do the saving there */
        $this->propertyModel->submitvariables = $this->submitvariables;
    }


    public function tab3(){
        $this->initModel();
        $this->data = new stdClass();
        $properties = MobilepropertiesBookmarkModel::getBookmarkedProperties($this->playid, $this->gid);
        $this->listProperties($properties);
        return $this->data;
    }

    public function listProperties(array $properties = null)
    {

        $this->rewriteActionConfigField('backarrow', false);

        if (is_null($properties)) {
            $properties = MobilepropertiesModel::model()->findAllByAttributes(array('play_id' => $this->playid, 'available' => 1));
        }

        if (!$properties) {
            $this->data->scroll[] = $this->getText('{#no_properties_yet#}', array('style' => 'rentals-info-message-text'));
        } else {

            foreach ($properties as $property) {
                $this->renderSingleProperty($property);
            }
        }

    }


    public function renderSingleProperty($property)
    {

        if (empty($property->name)) {
            return false;
        }

        $images = (array)json_decode($property->images);

        $image = 'property-add-photo-grey.png';

        if (isset($images['propertypic']) AND !empty($images['propertypic'])) {
            $image = $images['propertypic'];
        }

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('detail_view');
        $onclick->back_button = 1;
        $onclick->sync_open = true;
        $onclick->id = 'property-id-' . $property->id;

        $bg = $this->getImageFileName('shadow-image.png');

        $property_data = array();

        if ($property->square_meters) {
            $property_data[] = $this->getImage('icon-house.png', array('width' => 12, 'margin' => '0 5 0 0', 'vertical-align' => 'middle'));
            $property_data[] = $this->getText($property->square_meters . ' m2', array('style' => 'property-description',));
            $property_data[] = $this->getImage('line.png', array('width' => 2, 'height' => 18, 'margin' => '0 5 0 5'));
        }

        if ($property->num_bedrooms) {
            $property_data[] = $this->getImage('icon-bed.png', array('width' => 12, 'margin' => '0 5 0 0', 'vertical-align' => 'middle'));
            $property_data[] = $this->getText($property->num_bedrooms . '', array('style' => 'property-description',));
            $property_data[] = $this->getImage('line.png', array('width' => 2, 'height' => 18, 'margin' => '0 5 0 5'));
        }

        if ($property->price_per_month) {
            $property_data[] = $this->getText('$ ' . $property->price_per_month . ' p/m', array('style' => 'property-description',));
        }

        $row = $this->getRow(array(
            // $this->getImage( $image, array( 'width' => '100%', 'height' => '200', 'padding' => '0 0 0 0', 'border-radius' => '5', 'crop' => true ) ),
            // $this->getImage( 'shadow.png', array( 'width' => '100%', 'padding' => '0 0 0 0', 'floating' => 1, ) ),
            $this->getColumn(array(
                $this->getRow(array(
                    $this->getText($property->name, array('style' => 'property-title',)),
                ), array('text-align' => 'left', 'padding' => '3 10 0 10', 'background-color' => '#96000000',)),
                $this->getRow(
                    $property_data,
                    array('text-align' => 'left', 'padding' => '0 0 3 10', 'vertical-align' => 'middle', 'height' => 25, 'background-color' => '#96000000',)),
            ), array(
                'width' => '100%',
                'height' => '200',
                'padding' => '0 0 0 0',
                'vertical-align' => 'bottom',
                'background-image' => $bg,
                'background-size' => 'cover',
                'border-radius' => '5',
            )),
        ), array(
            'height' => '200',
            'background-image' => $this->getImageFileName($image),
            'background-size' => 'cover',
            'margin' => '3 3 0 3',
            'border-radius' => '5',
            'back_button' => 1,
            'onclick' => $onclick,
        ));

        $this->data->scroll[] = $row;

    }


    public function searchActions()
    {

        if ($this->menuid == 'cancel-search') {
            $this->deleteVariable('temp_interests');
            $this->deleteVariable('temp_gendersearch');
            $this->loadVariableContent(true);
        }
    }

    public function matches()
    {
        if ($this->menuid == 'no') {
            if (isset($this->params['swid'])) {
                $this->skip($this->params['swid']);
            }
            return true;
        }

        if ($this->menuid == 'yes') {
            if (isset($this->params['swid'])) {
                $this->doMatch($this->params['swid']);
            }

            return true;
        }

        $this->showMatches();

    }



    public function doMatch($id = false)
    {
        $property = MobilepropertiesModel::model()->findByPk($id);
        $userId = $property->play_id;
        MobilepropertiesBookmarkModel::like($property->id, $this->playid);
        $this->no_output = true;
        return true;
    }

    public function skip($id = false)
    {
        $property = MobilepropertiesModel::model()->findByPk($id);
        $userId = $property->play_id;
        MobilepropertiesBookmarkModel::skip($property->id, $this->playid);
        $this->no_output = true;
        return true;
    }


    public function showMatches($skipfirst = false)
    {
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;

        $interests_varname = $this->showing_search_results ? 'temp_interests' : 'interests';

        $strict = $this->showing_search_results ? true : false;
        $users = $this->mobilematchingobj->getUsersNearby($search_dist, $interests_varname, $this->getVariableId('interests'), $strict);

        $settings = MobilepropertiesSettingModel::findOrFail($this->playid);
        $properties = MobilepropertiesModel::findUnmatchedProperties($this->playid, $settings);

        if ($this->mobilematchingobj->debug) {
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        if (empty($properties)) {
            $this->notFound();
            return false;
        } else {
            $swipestack = $this->buildSwipeStack($properties, $skipfirst, false);
        }

        if (empty($swipestack)) {
            $this->notFound();
            return false;
        } else {
            if ($this->screen_width / $this->screen_height < 0.6) {
                $height = $this->screen_width + 65;
            } else {
                $height = round($this->screen_height / 3, 0) + 110;
            }

            $this->data->scroll[] = $this->getSwipearea($swipestack, array(
                'id' => 'mainswipe',
                'item_scale' => 1,
                'dynamic' => 1,
                'item_width' => '95%',
                //'animation' => 'nudge',
                'remember_position' => 1,
                'transition' => 'tablet',
                'world_ending' => 'refill_items',
                // 'height' => $height - 30
            ));
            $fade[] = $this->getImage('bottomfade.png',array('width' => '95%'));
            $this->data->scroll[] = $this->getRow($fade,array('text-align' => 'center','margin' => '0 0 20 0'));
            $this->data->scroll[] = $this->getBtns(1, $this->requireConfigParam('detail_view'), 1);
        }

    }

    public function buildSwipeStack($properties, $skipfirst, $include_buttons = true)
    {
        $detail_view = $this->requireConfigParam('detail_view');
        $swipestack = array();
        $vars = $this->getAMatch($properties);

        $itemcount = 0;

        // Didn't find any properties
        if (!$vars) {
            return false;
        }

        foreach ($vars as $index => $property) {
            if (is_null($property['play_id'])) {
                $this->addToDebug('skipped, no play id found ');
                continue;
            }

            $id = $property['play_id'];
            $propertypic = isset($property['propertypic']) ? $property['propertypic'] : false;

            $piccount = $this->getPicCount($property);
            $distance = 5;
            $filecheck = $this->imagesobj->findFile($propertypic);

//            $filter = $this->filter($one);

//            if ($filter AND $filecheck AND $itemcount < 20) {
            if (true) {
                if (!$skipfirst) {
                    $this->addToDebug('added to matchstack: ' . $id);

                    $rows[] = $this->getCard($propertypic, $detail_view, $distance, $piccount, $property['id'], $property, $index);
                    //$rows[] = $this->getMatchingSpacer($id);

                    if ($include_buttons) {
                        $rows[] = $this->getBtns($id, $detail_view, $index);
                    }

                    $swipestack[] = $this->getColumn($rows, array('text-align' => 'center',
                        'margin' => '10 0 0 0', 'swipe_id' => $property['id']
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
            return self::buildSwipeStack($properties, false, $include_buttons);
        }

        return $swipestack;
    }

    public function getAMatch($properties)
    {
        // Don't query more than 40 properties at a time, because it impacts performance
        if (count($properties) > 40) {
            $properties = array_splice($properties, 0, 40);
        }

        $pointer = false;

        foreach ($properties as $i => $property) {

            if (is_null($property['play_id'])) {
                continue;
            }

            $vars = array();
            $images = (array)json_decode($property->images);

            if (isset($images['propertypic'])) {
                $vars['propertypic'] = $images['propertypic'];
                $vars['play_id'] = $property['play_id'];
                $vars['id'] = $property['id'];
                $vars['bathrooms'] = $property['num_bathrooms'];
                $vars['bedrooms'] = $property['num_bedrooms'];
                $vars['price'] = $property['price_per_month'];
                $outvars[] = $vars;
                if (!$pointer) {
                    $this->mobilematchingobj->setPointer($property['play_id']);
                    $pointer = true;
                }
            } else {
                $this->mobilematchingobj->initMatching($property['play_id']);
                $this->mobilematchingobj->skipMatch();
            }
        }

        if (empty($outvars)) {
            $this->notFound();
            return false;
        }

        return $outvars;
    }

    public function getPicCount($one)
    {
        $count = 2;
        $piccount = 1;

        while ($count < 10) {
            $n = 'propertypic' . $count;
            if (isset($one[$n]) AND strlen($one[$n]) > 2) {
                $piccount++;
            }
            $count++;
        }

        return $piccount;

    }

    public function getBtns($id, $detail_view, $i)
    {
        $skipMenu = new stdClass();
        $skipMenu->action = 'swipe-delete';
        $skipMenu->container_id = 'mainswipe';
        $skipMenu->id = 'no';
        $skipMenu->send_ids = 1;

        $col_left = $this->getColumn(array(
            $this->getImage('dislike-big.png', array('priority' => '1', 'margin' => '0 0 0 40', 'onclick' => $skipMenu, 'send_ids' => 1,'width' => '100'))
        ), array('width' => '38%', 'vertical-align' => 'middle', 'text-align' => 'center'));

//        $col_center = $this->getColumn(array(
//            $this->getImagebutton('btn-info.png', $id, false, array('priority' => '1', 'margin' => '0 5 0 5', 'action' => 'open-action', 'config' => $detail_view, 'sync_open' => 1, 'send_ids' => 1))
//        ), array('width' => '24%', 'vertical-align' => 'middle'));

        $likeMenu = new stdClass();
        $likeMenu->action = 'swipe-delete';
        $likeMenu->container_id = 'mainswipe';
        $likeMenu->id = 'yes';
        $likeMenu->send_ids = 1;

        $col_right = $this->getColumn(array(
            $this->getImage('like-big.png', array('margin' => '0 40 0 0', 'priority' => '1', 'onclick' => $likeMenu,'width' => '100')),
        ), array('width' => '38%', 'vertical-align' => 'middle', 'text-align' => 'center'));

        return $this->getRow(array(
            $col_left, $col_right
        ), array('text-align' => 'center', 'vertical-align' => 'center', 'noanimate' => true));
    }

    public function getCard($profilepic, $detail_view, $distance, $piccount, $id, $one, $i)
    {
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $detail_view;
        $onclick->sync_open = 1;
        $onclick->id = 'property-id-' . $id;
        $onclick->context = 'property-id-' . $id;

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 800;
        $options['imgheight'] = 800;
        $options['imgcrop'] = 'yes';
        $options['margin'] = '5 5 0 10';
        $options['border-radius'] = '3';
        $options['priority'] = '9';
        $options['crop'] = 'yes';
        $options['width'] = $this->screen_width - 30;
        $options['priority'] = 9;

        if ($this->screen_width / $this->screen_height < 0.6) {
            $options['height'] = $this->screen_width - 40;
        } else {
            $options['height'] = round($this->screen_height / 3, 0);
        }

        if ($profilepic) {
            $profilepic = $this->getImage($profilepic, $options);
        } else {
            $profilepic = $this->getImage('anonymous2.png', $options);
        }

        //$page[] = $this->getText($this->screen_width / $this->screen_height);

        $page[] = $profilepic;

        $city = isset($one['city']) ? $one['city'] . ', ' : '';
//
//        $name[] = $this->getText($this->getLastName($one), array(
//            'font-size' => 14
//        ));

//        $page[] = $this->getRow($name, array(
//            'margin' => '5 10 5 10',
//            'vertical-align' => 'middle',
//            'height' => '20'
//        ));

//        $toolbar[] = $this->getImage('icon-location.png', array('width' => '18', 'margin' => '0 5 0 0'));
//        $toolbar[] = $this->getText($one['bathrooms'] . ' bathrooms', array(
//            'font-size' => 12
//        ));

        $toolbar[] = $this->getImage('icon-bedroom.png', array(
            'width' => '18',
            'margin' => '5 5 0 5'
        ));
        $toolbar[] = $this->getText($one['bedrooms'], array(
            'font-size' => 18
        ));
//
//        $toolbar[] = $this->getImage('icon-likes.png', array(
//            'width' => '23',
//            'floating' => 1,
//            'margin' => '0 12 0 0',
//            'float' => 'right',
//        ));

        $price[] = $this->getText('$ ' . $one['price'], array(
            'font-size' => 18,
            'font-weight' => 'bold',
            'color' => '#00AD0A',
            'text-align' => 'right',
//            'height' => '20',
        ));

        $price[] = $this->getText('p/m', array(
            'font-size' => 14,
            'font-weight' => 'bold',
            'color' => '#CBCBCB',
            'text-align' => 'right',
//            'height' => '20',

        ));

        $toolbar[] = $this->getColumn($price, array(
            'floating' => 1,
            'float' => 'right',
        ));

        $page[] = $this->getRow($toolbar, array(
            'margin' => '0 10 0 10',
            'vertical-align' => 'middle'
        ));



        return $this->getColumn($page, array(
            'background-color' => '#ffffff',
            'width' => $this->screen_width - 10,
            'border-radius' => '8',
            'leftswipeid' => 'left' . $one['id'],
            'rightswipeid' => 'right' . $one['id']
        ));


    }

    public function getInterestsCount($vars)
    {
        if (!isset($vars['interests']) OR
            empty($vars['interests']) OR
            !isset($this->varcontent['interests']) OR
            empty($this->varcontent['interests'])
        ) {
            return '0';
        }

        $my_interests = json_decode($this->varcontent['interests'], true);
        $user_interests = json_decode($vars['interests'], true);

        $tmp_my_interests = array();
        $tmp_user_interests = array();

        foreach ($my_interests as $mi_key => $mi_value) {
            if ($mi_value) {
                $tmp_my_interests[] = $mi_key;
            }
        }

        foreach ($user_interests as $ui_key => $ui_value) {
            if ($ui_value) {
                $tmp_user_interests[] = $ui_key;
            }
        }

        $common = array_intersect($tmp_my_interests, $tmp_user_interests);

        if ($common) {
            return count($common);
        }

        return '0';
    }

    public function activateLocationTracking()
    {

        $cache = Appcaching::getGlobalCache($this->playid . '-locationtracking');
        if ($cache) {
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

        Aetask::registerTask($this->playid, 'update:cities', json_encode($params), 'async');
    }

    public function itsAMatch($id = false)
    {
        $this->configureBackground('actionimage2');

        $cachepointer = $this->playid . '-matchid';
        Appcaching::setGlobalCache($cachepointer, $id);

        if (!$id) {
            $id = $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';
        $params['priority'] = '9';

        //$match_img = 'its-a-match.png';

        if ($this->getConfigParam('actionimage2')) {
            $match_img = $this->getConfigParam('actionimage2');
            $row[] = $this->getImage($match_img, $params);
        }


        $textstyle['margin'] = '-20 0 0 70';
        $textstyle['color'] = '#ffffff';
        $row[] = $this->getText('{#you_and#} ' . $this->getLastName($vars) . ' {#like_each_other#}', $textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '106';
        $params['text-align'] = 'center';
        $params['border-width'] = '3';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '53';
        $params['priority'] = '9';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic, $params);
        $pics[] = $this->getImage($this->getVariable('profilepic'), $params);

        $row[] = $this->getRow($pics, array('margin' => '30 30 0 30', 'text-align' => 'center'));

        $row[] = $this->getSpacer('50');
        $row[] = $this->getTextbutton('Start a conversation', array('style' => 'general_button_style', 'id' => $this->getTwoWayChatId($id),
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid, 'viewport' => 'bottom',));
        $textstyle['margin'] = '15 0 10 0';
        $textstyle['text-align'] = 'center';
        $row[] = $this->getText('or', $textstyle);
        $row[] = $this->getTextbutton('Keep surfing', array('style' => 'general_button_style', 'id' => 'keep-playing'));
        $row[] = $this->getSpacer('50');

        $this->data->scroll[] = $this->getColumn($row, array(
            //'background-color' => '#e33124',
            //'background-image' => $this->getImage('no-match-bg.png')
        ));

    }

    public function myMatches() {
        $this->configureBackground('actionimage3');

        if ($this->getConfigParam('actionimage1')) {
            $this->data->scroll[] = $this->getImage($this->getConfigParam('actionimage1'));
        } else {
            $this->data->scroll[] = $this->getSettingsTitle('{#my_contacts#}', false, false);
        }

        $matches = $this->mobilematchingobj->getMyMatches();
	    $inbox = $this->mobilematchingobj->getMyInbox();
	    $outbox = $this->mobilematchingobj->getMyOutbox();

	    $all_matches = array_merge( $matches, $inbox, $outbox );
	    $this->matchList($all_matches, false, '{#no_contacts_yet#}');

	    return true;
    }

    public function myMatchesMessages()
    {

        $this->configureBackground('actionimage3');

        $matches = $this->mobilematchingobj->getMyMatches();
        $filtered_matches = $this->getMatchesWithMessages($matches);

        if ($this->getConfigParam('actionimage1')) {
            $this->data->scroll[] = $this->getImage($this->getConfigParam('actionimage1'));
        } else {
            $this->data->scroll[] = $this->getSettingsTitle('{#my_unread_messages#}', false, false);
        }

        $this->matchList($filtered_matches, false, '{#no_unread_messages#}');
    }

    public function getMatchesWithMessages($matches)
    {

        if (empty($matches)) {
            return false;
        }

        $users = array();

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

            if (isset($messages->chat_message_is_read) AND $messages->chat_message_is_read == 0) {
                $users[] = $match_id;
            }

        }

        return $users;
    }

    public function matchListInvites($matches)
    {

        if (empty($matches)) {
            $othertxt['style'] = 'imate_title_nomatch';
            $this->data->scroll[] = $this->getText('{#no_invites_yet#}', $othertxt);
        }

        $this->handleMatchInviteAction();

        foreach ($matches as $key => $res) {
            $vars = AeplayVariable::getArrayOfPlayvariables($res);

            if (empty($vars)) {
                continue;
            }

            if (!isset($vars['profilepic'])) {
                continue;
            }

            $this->getMyMatchItemInvites($vars, $res);
        }

    }

    public function getMyMatchItemInvites($vars, $id, $search = false)
    {
        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        $name = $this->getLastName($vars);
        $name = isset($vars['city']) ? $name . ', ' . $vars['city'] : $name;

        $image_onclick = new StdClass();
        $image_onclick->action = 'open-action';
        $image_onclick->id = $id;
        $image_onclick->back_button = true;
        $image_onclick->sync_open = true;
        $image_onclick->action_config = $this->requireConfigParam('detail_view');

        $unread = false;

        /* notification column */
        $contextkey = $this->getTwoWayChatId($id, $this->current_playid);
        $chat = Aechatusers::model()->findByAttributes(array('context_key' => $contextkey, 'chat_user_play_id' => $this->current_playid));

        // Actual markup

        $left_col_rows[] = $this->getImage($profilepic, array(
            'style' => 'profile-image-olive',
            'priority' => 9,
            'onclick' => $image_onclick
        ));

        $right_col_rows[] = $this->getRow(array(
            $this->getText($name, array('style' => 'imate_title')),
        ));

        if ($profiledescription) {
            if (strlen($profiledescription) > 35) {
                $profiledescription = $this->truncate_words($profiledescription, 3) . '...';
            }

            $right_col_rows[] = $this->getRow(array(
                $this->getText($profiledescription, array('style' => 'imate_title_subtext')),
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
                $this->getText('{#accept#}', array('style' => 'olive-match-button')),
            ), array('width' => '50%', 'onclick' => $onclick_accept)),
            $this->getColumn(array(
                $this->getText('{#decline#}', array('style' => 'olive-match-button')),
            ), array('width' => '50%', 'onclick' => $onclick_decline))
        ));

        $col_left = $this->getColumn(
            $left_col_rows
            , array('width' => '33%', 'text-align' => 'center',));

        $col_right = $this->getColumn(
            $right_col_rows
            , array('width' => '67%', 'vertical-align' => 'middle'));

        $rowparams['margin'] = '5 10 5 10';
        $rowparams['vertical-align'] = 'middle';

        if ($search) {
            $rowparams['margin'] = '0 25 0 8';
            $rowparams['background-color'] = $this->color_topbar;
        }

        // $rowparams['vertical-align'] = 'middle';
        // $rowparams['height'] = '80';

        $this->data->scroll[] = $this->getRow(array(
            $col_left,
            $col_right
        ), $rowparams);

        $this->data->scroll[] = $this->getText('', array('style' => 'olive-row-divider'));
    }

    public function handleMatchInviteAction()
    {

        $open_chat = false;
        $do_refresh = false;

        if (preg_match('~accept-user-~', $this->menuid)) {
            $ac_user_id = str_replace('accept-user-', '', $this->menuid);
            $this->initMobileMatching($ac_user_id);
            $this->mobilematchingobj->send_accept_push = false;
            $this->triggerConfirmationPush($ac_user_id);
            $this->mobilematchingobj->saveMatch();
            $open_chat = true;
        }

        if (preg_match('~decline-user-~', $this->menuid)) {
            $ac_user_id = str_replace('decline-user-', '', $this->menuid);
            $this->initMobileMatching($ac_user_id);
            $this->mobilematchingobj->skipMatch();
            $do_refresh = true;
        }

        // Re-init the matching in order to restore the corresponding play ID
        $this->initMobileMatching();

        if ($open_chat) {

            $onload = new StdClass();
            $onload->action = 'open-action';
            $onload->id = $this->getTwoWayChatId($ac_user_id, $this->current_playid);
            $onload->back_button = true;
            $onload->sync_open = true;
            $onload->sync_close = true;
            $onload->viewport = 'bottom';
            $onload->action_config = $this->requireConfigParam('chat');

            $this->data->onload[] = $onload;

        } else if ($do_refresh) {

            $onload = new StdClass();
            $onload->action = 'submit-form-content';
            $onload->id = 'refresh-view';

            $this->data->onload[] = $onload;

        }

    }

    public function getHeader($active = 1)
    {

        $content = array(
            'tab1' => '{#landlord#}',
            'tab2' => '{#agent#}',
            'tab3' => '{#favourites#}'
        );

        $params = array(
            'active' => $active,
            'color_topbar' => '#f5f5f5',
            'color_topbar_hilite' => '#1A1C37',
            'indicator_mode' => 'fulltab',
            'btn_padding' => '12 10 12 10',
            'padding' => '7 7 7 7',
            'border-radius' => '3',
            'divider' => true,
            'background' => 'black'
        );

        $this->data->header[] = $this->getRoundedTabs($content, $params);

    }

    public function getMyMatchItem($vars,$id,$search=false){

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'anonymous2.png';
        $profiledescription = isset($vars['profile_comment']) ? $vars['profile_comment'] : '-';

        $name = $this->getFirstName($vars);
        $name = isset($vars['city']) ? $name.', '.$vars['city'] : $name;

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getTwoWayChatId($id,$this->current_playid);
        $onclick->back_button = true;
        $onclick->sync_open = true;
        $onclick->sync_close = true;
        $onclick->viewport = 'bottom';
        $onclick->action_config = $this->requireConfigParam('chat');
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

        // Actual markup

        $left_col_rows[] = $this->getImage($profilepic, array(
            'style' => 'round_image_imate',
            'priority' => 9,
            'onclick' => $image_onclick
        ));

        /* unread marker */
        if ( $unread ) {
            $left_col_rows[] = $this->getImage('red-dot.png',array('width' => '10', 'margin' => '5 10 0 0', 'float' => 'right', 'floating' => 1));
        }
    
        $role = isset($vars['role']) ? $vars['role'] : '';
        $role = isset($vars['subrole']) && !empty($vars['subrole']) ? $vars['subrole'] : $role;

        $nameparams = $textparams;
        unset($nameparams['style']);
        $nameparams['font-ios'] = 'Roboto-Italic';
        $nameparams['font-android'] = 'RobotoItalic';

        $right_col_rows[] = $this->getRow(array(
            $this->getText($name . ' - ', $nameparams),
            $this->getText('{#' .$role .'#}',array('color' => '#999999'))
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
            , array( 'width' => '75%', 'vertical-align' => 'middle','onclick' => $onclick ));

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

        $this->data->{$append_location}[] = $this->getText('',array('height' => '1','background-color' => '#DADADA','margin' => '0 0 10 100'));
    }

    public function setFooter()
    {
        $likes = $this->mobilematchingobj->getMyInbox();
        $likes_count = ($likes ? count($likes) : 0);

        $matches = $this->mobilematchingobj->getMyMatches();

        $filtered_matches = $this->getMatchesWithMessages($matches);

        $matches_count = ($filtered_matches ? count($filtered_matches) : 0);

        $click_id_messages = $this->getConfigParam('my_matches_messages');
        $click_id_invites = $this->getConfigParam('my_matches_invites');
    }

    public function notFound()
    {
        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '150';
        $params['margin'] = '50 0 0 0';
        $params['priority'] = 9;

        $img = $this->getSavedVariable('profilepic');

        if ($img) {
            $tit[] = $this->getImage($img, $params);
            $row[] = $this->getColumn($tit, array('width' => '100%', 'text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');
        $row[] = $this->getText('{#there_are_no_properties_matching_your_criteria#}', array('text-align' => 'center', 'color' => '#000000'));

        $this->data->scroll[] = $this->getColumn($row);
    }

    public function triggerConfirmationPush($play_id)
    {
        $name = $this->getLastName($this->varcontent);
        $text = 'New Olive match';
        $description = $name . ' accepted your invitation. You can chat now. on tap it should open the chat with this user.';
        Aenotification::addUserNotification($play_id, $text, $description, '0', $this->gid);
    }

    public function getChatSeparatorStyles()
    {
        return array(
            'margin' => '8 0 4 0',
            'background-color' => '#e5e5e5',
            'height' => '3',
        );
    }

    public function getLastName( $vars ){

        if ( isset($vars['screen_name']) ) {
            return $vars['screen_name'];
        }

        if ( !isset($vars['real_name']) OR empty($vars['real_name']) ) {
            return '{#anonymous#}';
        }

        $name = $vars['real_name'];
        
        $pieces = explode(' ', trim($vars['real_name']));

        if ( isset($pieces[1]) ) {
            return ucfirst($pieces[1]);
        }

    }

}