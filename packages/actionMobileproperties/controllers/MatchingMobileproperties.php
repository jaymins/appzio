<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.*.models.*');

class MatchingMobileproperties extends MobilepropertiesController {

    public $enable_advertising = false;

    public $current_item_id;

    // maximum items per load
    public $max_items = 30;
    public $showing_search_results;
    public $propertyModel;
    public $settings;


    public function tab1()
    {
        $this->data = new stdClass();
        $this->initMobileMatching();
        $this->askPermissions();
        $this->matches();
        $this->getHeader(1);
        $this->showMatches('agent');
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
        $this->data = new stdClass();
        $this->initMobileMatching();
        $this->askPermissions();
        $this->matches();
        $this->getHeader(2);
        $this->showMatches('landlord');
        return $this->data;
    }


    public function tab3(){

        $this->data = new stdClass();
        $this->initMobileMatching();
        $this->initModel();
        $this->getHeader(3);
        $this->settings = MobilepropertiesSettingModel::findOrFail($this->playid, $this->gid);
        $properties = MobilepropertiesBookmarkModel::getBookmarkedProperties($this->playid, $this->gid);
        $this->listProperties($properties);
        return $this->data;
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

    }

    public function doMatch($id = false)
    {
        $property = MobilepropertiesModel::model()->findByPk($id);
        MobilepropertiesBookmarkModel::like($property->id, $this->playid, $this->gid);
        //$this->no_output = true;
        return true;
    }

    public function skip($id = false)
    {
        $property = MobilepropertiesModel::model()->findByPk($id);
        $userId = $property->play_id;
        MobilepropertiesBookmarkModel::skip($property->id, $this->playid, $this->gid);
        //$this->no_output = true;
        return true;
    }


    public function showMatches($from='agent')
    {
        $skipfirst = false;
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $interests_varname = $this->showing_search_results ? 'temp_interests' : 'interests';

        $strict = $this->showing_search_results ? true : false;
        $users = $this->mobilematchingobj->getUsersNearby($search_dist, $interests_varname, $this->getVariableId('interests'), $strict);

        $settings = MobilepropertiesSettingModel::findOrFail($this->playid, $this->gid);
        $this->settings = $settings;

        $recent = $this->getSavedVariable('temp_user_search_term');
        $properties = MobilepropertiesModel::findUnmatchedProperties($this->playid, $settings, $this->gid, $from, $recent);

        if ($this->mobilematchingobj->debug) {
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        if (empty($properties)) {
            $this->notFound();
            return false;
        } else {

            /* if its a search view, we are injecting variable values for gender selection */
            if ($this->showing_search_results) {
                $genders = json_decode($this->getSavedVariable('temp_gendersearch'), true);

                if (!empty($genders)) {
                    $this->varcontent['men'] = $genders['men'];
                    $this->varcontent['women'] = $genders['women'];
                } else {
                    $this->varcontent['men'] = 1;
                    $this->varcontent['women'] = 1;
                }
            }

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
                // 'height' => $height - 56
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

                if ($this->settings && $this->settings->filter_price_per_week == 'price_per_week') {
                    $vars['price'] = $property['price_per_week'];
                }

                $vars['area'] = $property->square_ft;
                if ($this->settings && $this->settings->filter_sq_ft == 'sq_meter') {
                    $vars['area'] = $property->square_meters;
                }

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
            $this->getImage('icon-dislike.png', array('priority' => '1', 'margin' => '0 0 0 40', 'onclick' => $skipMenu, 'send_ids' => 1,'width' => '100'))
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
            $this->getImage('icon-like.png', array('margin' => '0 40 0 0', 'priority' => '1', 'onclick' => $likeMenu,'width' => '100')),
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

        if (!$i) {
            $onclick->context = $this->getPropertyContext( 'property-id-' . $id );
        }

        $options['onclick'] = $onclick;
        $options['imgwidth'] = 800;
        $options['imgheight'] = 800;
        $options['imgcrop'] = 'yes';
        $options['margin'] = '5 5 0 10';
        $options['border-radius'] = '3';
        $options['priority'] = '9';
        $options['crop'] = 'yes';
        $options['width'] = $this->screen_width - 30;

        if ($this->screen_width / $this->screen_height < 0.6) {
            $options['height'] = $this->screen_width - 40;
        } else {
            $options['height'] = round($this->screen_width / 1.3, 0);
        }

        if ($profilepic) {
            $profilepic = $this->getImage($profilepic, $options);
        } else {
            $profilepic = $this->getImage('anonymous2.png', $options);
        }

        //$page[] = $this->getText($this->screen_width / $this->screen_height);

        $page[] = $profilepic;

        $city = isset($one['city']) ? $one['city'] . ', ' : '';

        $areaText = 'ft2';
        if ($this->settings && $this->settings->filter_sq_ft == 'sq_meter') {
            $areaText = 'm2';
        }

        $toolbar[] = $this->getImage('icon-bed-blue.png', array(
            'width' => '17',
            'vertical-align' => 'middle',
            'margin' => '0 7 0 5'
        ));
        $toolbar[] = $this->getText($one['bedrooms'], array('style' => 'property-line-text'));

        if ( $one['area'] ) {
	        $toolbar[] = $this->getVerticalSpacer(30);
	        $toolbar[] = $this->getImage('icon-house-blue.png', array(
		        'width' => '17',
		        'vertical-align' => 'middle',
		        'margin' => '0 7 0 5'
	        ));
	        $toolbar[] = $this->getText($one['area'] . ' ' . $areaText, array('style' => 'property-line-text'));
        }

	    $priceText = 'p/m';
        if ($this->settings && $this->settings->filter_price_per_week == 'price_per_week') {
            $priceText = 'p/w';
        }

        $toolbar[] = $this->getRow(array(
            $this->getText('£ ' . $one['price'] . ' ' . $priceText, array('style' => 'property-line-text-right-green')),
        ), array(
            'width' => '30%',
            'text-align' => 'right',
            'floating' => 1,
            'float' => 'right',
        ));

        $page[] = $this->getRow($toolbar, array(
            'height' => '40',
            'padding' => '0 10 0 10',
            'vertical-align' => 'middle',
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

    public function getHeader($active = 1)
    {
        $this->setFiltering();

        $content = array(
            'tab1' => '{#by_agents#}',
            'tab2' => '{#by_landlords#}',
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
            'background' => 'black',
        );

        $this->data->header[] = $this->getRoundedTabs($content, $params);

    }

    /* this will replace the appropriate filtering menu to top */
    public function setFiltering(){
        $settings = MobilepropertiesSettingModel::getSettings($this->playid, $this->gid);

        if(isset($this->menus['filtering']) AND isset($this->menus['clear_filter'])) {
            if($settings->play_id){
                $this->rewriteActionConfigField('menu_id',$this->menus['clear_filter']);
            } else {
                $this->rewriteActionConfigField('menu_id',$this->menus['filtering']);
            }

        }
    }

    public function setFooter()
    {

        if ($this->showing_search_results) {
            $this->searchFooter();
            return true;
        }

        $likes = $this->mobilematchingobj->getMyInbox();
        $likes_count = ($likes ? count($likes) : 0);

        $matches = $this->mobilematchingobj->getMyMatches();
        $filtered_matches = $this->getMatchesWithMessages($matches);

    }


    public function notFound()
    {
        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '150';
        $params['margin'] = '50 0 0 0';

        $img = $this->getSavedVariable('profilepic');

        if ($img) {
            $tit[] = $this->getImage($img, $params);
            $row[] = $this->getColumn($tit, array('width' => '100%', 'text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');


        if ($this->showing_search_results) {
            $row[] = $this->getText('{#change_your_search_parameters_to_find_people#}', array('style' => 'register-text-step-2'));
            $row[] = $this->getText('{#you_need_to_select_at_least_one_interest#}', array('style' => 'register-text-step-2'));

            $row[] = $this->getTextbutton('{#change_search#}', array('style' => 'olive-submit-button', 'id' => '1234',
                'action' => 'open-action',
                'open_popup' => true,
                'config' => $this->getConfigParam('action_id_categorysearch')));
        } else {
            $row[] = $this->getText('{#there_are_no_properties_matching_your_criteria#}', array('text-align' => 'center', 'color' => '#000000'));
        }

        $this->data->scroll[] = $this->getColumn($row);
    }


}
