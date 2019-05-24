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
Yii::import('application.modules.aelogic.article.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobiledeals.models.*');

class MobiledealsController extends ArticleController {

    public $configobj;
    public $theme;

    public $gamevariables;

    public $action;
    public $branch_id;
    public $action_id;

    public $app_id;
    public $actions;
    

    public function adminHook() {

        if ( !isset($_GET['update-rss-news']) ) {
            return false;
        }

        $this->getCurrentAction();

        $config = $this->action->config;
        $branch_id = $this->action->branch_id;

        if ( empty($config) ) {
            return false;
        }

        $config = json_decode( $config );

        if ( isset($_GET['update-rss-news']) ) {
            $update_start_time = time();
            $this->updateRSSData( $config, $branch_id, $update_start_time );
        }

    }


    public function updateRSSData( $config, $branch_id, $update_start_time ) {

        $deals_log = new MobiledealsModel();
        $deals_log->app_id = $this->app_id;

        // Either create a new log entry or get an existing one for the current day
        $log_entry = $deals_log->getLogEntry();

        $rss_link = ( isset($config->rss_link) ? $config->rss_link : '' );

        if ( empty($rss_link) ) {
            return false;
        }

        $rss = new DOMDocument();

        @$rss->load( $rss_link );

        $action_type = Aeactiontypes::model()->findByAttributes( array('shortname' => 'mobiledealviewer') );
        
        $model = new Aeaction;

        $q_init = new CDbCriteria( array(
            'condition' => "branch_id LIKE :branch_id AND name NOT LIKE :name",
            'params'    => array(
                ':branch_id' => $this->branch_id,
                ':name' => "%Data Handler%",
            )
        ) );

        $current_actions = $model->findAllByAttributes( array(), $q_init );

        $current_ids = array();
        $current_matches = array();

        $current_deals_ids = array();
        $feed_deals_ids = array();

        foreach ($current_actions as $action) {
            $current_ids[] = $action->id;
            $action_config = json_decode( $action->config );
            if ( isset($action_config->dealid) ) {
                $current_deals_ids[] = $action_config->dealid;
            }
        }

        $new_deals = 0;

        $ids_new_deals = array();
        $ids_deleted_deals = array();

        $feed = array();
        foreach ($rss->getElementsByTagName('item') as $node) {
            $dealid = $node->getElementsByTagName('dealid')->item(0)->nodeValue;
            $title = $node->getElementsByTagName('title')->item(0)->nodeValue;
            $text = $node->getElementsByTagName('description')->item(0)->nodeValue;
            $link = $node->getElementsByTagName('link')->item(0)->nodeValue;
            $image = $node->getElementsByTagName('image')->item(0)->nodeValue;
            $static_image = $node->getElementsByTagName('imagestatic')->item(0)->nodeValue;

            $date = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;
            $date = str_replace( '-0400', '', $date );

            $feed_deals_ids[] = $dealid;

            $feed = array(
                // 'title'         => mb_convert_encoding($title,'HTML-ENTITIES','UTF-8'),
                'dealid'                => $dealid,
                'link'                  => $link,
                'description'           => mb_convert_encoding($text,'HTML-ENTITIES','UTF-8'),
                'listing_description'   => mb_convert_encoding($text,'HTML-ENTITIES','UTF-8'),
                'listing_image'         => $image,
                'heading_image'         => $image,
                'static_image'          => $static_image,
                'date'                  => $date,
                'deal_placeholder'      => 1,
            );

            $q = new CDbCriteria( array(
                'condition' => "name LIKE :name AND branch_id LIKE :branch_id AND config LIKE :dealid",
                'params'    => array(
                    ':name'      => $title,
                    ':branch_id' => $this->branch_id,
                    ':dealid'    => "%$dealid%"
                )
            ) );
             
            $action_obj = $model->findByAttributes( array(), $q );

            // If any matches exists
            if ( isset($action_obj->id) AND !empty($action_obj->id) ) {
                $current_matches[] = $action_obj->id;
            } else {
                $action_id = $model->addTask( $title, $this->branch_id );
                $action_obj = $model->findByPk( $action_id );

                $ids_new_deals[] = $dealid;
                $new_deals++;
            }

            $image_result = $this->validateImage( $static_image );
            $feed['has_valid_image'] = $image_result;

            // Update the Action object with the needed data
            $config = json_encode($feed);

            $action_obj->active = 1;
            $action_obj->type_id = $action_type->id;
            $action_obj->config = $config;
            $action_obj->update();
        }

        $new_deals_in_feed = array_diff($feed_deals_ids, $current_deals_ids);
        $new_deals_in_feed = array_values($new_deals_in_feed);

        // $deleted_deals = array_diff($current_deals_ids, $feed_deals_ids);

        $missing_items = array_diff( $current_ids, $current_matches );

        // Delete all missing actions
        if ( !empty($missing_items) ) {
            foreach ($missing_items as $mi_id) {
                $missing_action = $model->findByPk( $mi_id );

                $mdc = $missing_action->config;
                if ( $mdc ) {
                    $mdc = json_decode( $mdc, true );
                    if ( isset($mdc['dealid']) AND !empty($mdc['dealid']) ) {
                        $ids_deleted_deals[] = $mdc['dealid'];
                    }
                }

                $missing_action->delete();
            }
        }

        $added_batch = $log_entry->added_deals_batch;
        $added_batch = empty($added_batch) ? array() : json_decode( $added_batch, true );
        $added_batch[$update_start_time][$branch_id] = $ids_new_deals;

        $deleted_batch = $log_entry->deleted_deals_batch;
        $deleted_batch = empty($deleted_batch) ? array() : json_decode( $deleted_batch, true );
        $deleted_batch[$update_start_time][$branch_id] = $ids_deleted_deals;

        // This also needs to be cleaned up
        $ndif_batch = $log_entry->deals_ids;
        $ndif_batch = empty($ndif_batch) ? array() : json_decode( $ndif_batch, true );
        $ndif_batch[$update_start_time][$branch_id] = $new_deals_in_feed;

        // We should remove this in a while
        $comp_ids = $log_entry->comp_ids;
        $comp_ids = empty($comp_ids) ? array() : json_decode( $comp_ids, true );
        $comp_ids[$update_start_time][$branch_id] = $feed_deals_ids;

        $current_total = $log_entry->total_deals_added;
        $current_total = $current_total + $new_deals;

        $log_entry->total_deals_added = $current_total;
        $log_entry->added_deals_batch = json_encode( $added_batch );
        $log_entry->deleted_deals_batch = json_encode( $deleted_batch );
        $log_entry->deals_ids = json_encode( $ndif_batch );
        $log_entry->comp_ids = json_encode( $comp_ids );
        $log_entry->update();

        return $new_deals;
    }


    public function validateImage( $image ) {
        if ( !$this->remoteFileExists($image) ) {
            return false;
        }

        return true;
    }


    // Check if the Requested remote image exists
    private function remoteFileExists( $file ) {
        $url = @getimagesize($file);
        
        if ( is_array($url) ) {
            return true;
        }

        return false;
    }


    public function getCurrentAction() {
        $action = Aeaction::model()->findByPk( $this->action_id );

        if ( empty($action) ) {
            return false;
        }

        $this->action = $action;
    }


    public function getData(){

        $this->gamevariables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid),array('order'=>'name ASC')), 'id','name');

        $data = new StdClass();

        $this->handleSubmissions();

        $mode = ( isset($this->configobj->deals_action_mode) ? $this->configobj->deals_action_mode : 'default' );

        switch ($mode) {
            case 'bookmarks_mode':
                $data->scroll = $this->getBookmarkedDeals();
                break;

            case 'latest_deals_mode':
                $data->scroll = $this->getLatestDeals();
                break;
            
            default:
                $next_page_id = 2;

                if ( isset($this->submit['next_page_id']) ) {
                    $next_page_id = $this->submit['next_page_id'] + 1;
                }

                $this->actions = $this->getActions();

                $results = $this->getDeals();

                // Add tracking
                $this->saveStatus();

                if ( !empty($results) ) {
                    $output[] = $this->getInfinitescroll( $results, array( 'next_page_id' => $next_page_id ) );
                } else {
                    $output[] = $this->getText( '{#no_more_deals#}!', array( 'style' => 'deals-notification' ) );
                }

                $data->scroll = $output;
                break;
        }

        return $data;
    }


    public function getDeals() {
        $output = array();

        if ( empty($this->actions) ) {
            $output[] = $this->getText( '{#currently_there_arent_any_deals#}', array( 'style' => 'deals-notification' ) );
            return false;
        }

        foreach ($this->actions as $i => $action) {

            if ( !is_object($action) ) {
                $action = (object) $action;
            }

            if ( empty($action->active) ) {
                continue;
            }
            
            $output[] = $this->renderDeal( $action );
            $output[] = $this->getText( '', array( 'style' => 'row-divider' ) );
        }

        return $output;
    }


    private function getBookmarkedDeals() {

        $this->loadVariables();
        $this->loadVariableContent();

        $output = array();

        if ( !isset($this->varcontent['bookmarks']) OR empty($this->varcontent['bookmarks']) OR $this->varcontent['bookmarks'] == '{}' ) {
            $output[] = $this->getText( '{#currently_you_dont_have_any_bookmarked_deals#}!', array( 'style' => 'deals-notification' ) );
            $output[] = $this->getTextbutton( '{#browse_deals#}', array(
                'id' => 'open-deals',
                'action' => 'open-action',
                'config' => $this->getConfigParam( 'listing_action_id' ),
                'style' => 'back-button-big',
            ));
            return $output;
        }

        $bookmarks = json_decode($this->varcontent['bookmarks'], true);

        $my_actions = array();

        foreach ($bookmarks as $action_id => $status) {
            $action = Aeaction::model()->findByPk($action_id);
            if ( is_object($action) ) {
                $my_actions[] = $action;
            }
        }

        if ( empty($my_actions) ) {
            $output[] = $this->getText( '{#currently_you_dont_have_any_bookmarked_deals#}!', array( 'style' => 'deals-notification' ) );
            $output[] = $this->getTextbutton( '{#browse_deals#}', array(
                'id' => 'open-deals',
                'action' => 'open-action',
                'config' => $this->getConfigParam( 'listing_action_id' ),
                'style' => 'back-button-big',
            ));
            return $output;
        }

        foreach ($my_actions as $active_action) {
            $output[] = $this->renderDeal( $active_action );
            $output[] = $this->getText( '', array( 'style' => 'row-divider' ) );
        }

        return $output;
    }


    private function renderDeal( $action ) {

        if ( !isset($action->config) OR empty($action->config) ) {
            $output = $this->getText( 'Configuration Error' );
            return;
        }

        $config = $action->config;
        $config = json_decode( $config );

        $votes_count = 0;

        if ( isset($config->upvotes) ) {
            $votes_count = count( $config->upvotes );
        }

        $time = ( (isset($config->date) AND !empty($config->date)) ? $config->date : $action->timestamp );
        $timestamp = strtotime( $time );

        $diff = TimeHelpers::getTimeDiff( $timestamp, time() );

        // there are cases where the has_valid_image flag fails - not ideal, but could be fixed adding a fix for a "null" value
        if ( isset($config->has_valid_image) AND ( $config->has_valid_image OR $config->has_valid_image == 'null' ) ) {
            
            if ( isset($config->static_image) AND !empty($config->static_image) ) {
                $image = $config->static_image;
            } else {
                $image_tmp = $config->listing_image;
                $image_path = basename($image_tmp);
                $image = 'http://web.ionsmedia.com/offercrowd/cimages/' . $image_path;
            }

        } else {
            $image = 'deal-placeholder.png';
        }

        // $image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'no'));

        $description = ( isset($config->listing_description) ? $config->listing_description : '' );

        if ( !isset($this->configobj->deal_item_id) ) {
            $this->configobj->deal_item_id = 99999;
        }

        $btn_left = $this->getColumn( array(
            $this->getImage( $image, array( 'width' => '100%', 'height' => 100, 'lazy' => 1, 'priority' => 9 ) ),
            // $this->getImage( $image ),
        ), array( 'style' => 'listing-cell-left' ) );

        $btn_center = $this->getColumn( array(
            $this->getText( $this->getCleanName($action->name), array( 'style' => 'lcc-heading' )),
            $this->getText($this->limitText($description, 8), array( 'style' => 'lcc-description' )),
        ), array( 'style' => 'listing-cell-center' ) );

        $btn_right = $this->getColumn( array(
            $this->getImage( 'thumbs-up.png', array( 'style' => 'lcr-icons-icon' )),
            $this->getText($votes_count, array( 'style' => 'lcr-icons-text' )),
            $this->getImage('wall-clock.png', array( 'style' => 'lcr-icons-icon')),
            $this->getText($diff, array( 'style' => 'lcr-icons-text')),
        ), array( 'style' => 'listing-cell-right' ) );

        $onclick_options = new StdClass();
        $onclick_options->id = $action->id;
        $onclick_options->action = 'open-action';
        $onclick_options->action_config = $this->configobj->deal_item_id;
        // $onclick_options->context = 'deal-' . $this->configobj->deal_item_id;
        $onclick_options->sync_open = 1;
        $onclick_options->sync_close = 0;
        $onclick_options->back_button = 1;

        $output = $this->getRow(
            array( $btn_left, $btn_center, $btn_right ),
            array( 'style' => 'listing-main-row', 'onclick' => $onclick_options )
        );

        return $output;
    }


    private function getActions() {

        $model = new Aeaction();
        $branch_id = $this->configobj->deals_branch_id;
        $model->branch_id = $branch_id;

        // $actions = $model->findAllByAttributes(array('branch_id' => $branch_id),array('order' => '`order`'));

        $cache_name = 'cached_deals';

        if ( $cached_data = Appcaching::getActionCache($this->actionid, $this->playid, $this->gid, $cache_name) ) {
            return $cached_data;
        }

        $page = 1;

        if ( isset($this->submit['next_page_id']) ) {
            $page = $this->submit['next_page_id'];
        }

        $num_rec_per_page = 10;
        $start_from = ($page-1) * $num_rec_per_page;

        $model->limit_from = $start_from;
        $model->limit_to = $num_rec_per_page;

        $actions = $model->getMainActionsData();

        // Appcaching::setGlobalCache( $cache_name . '-' . $this->playid , $actions );
        Appcaching::setActionCache($this->actionid, $this->gid, $cache_name, $actions);

        return $actions;
    }


    private function saveStatus() {
        $model = new Aebranch();
        $branch = $model->findByPk( $this->configobj->deals_branch_id );

        $config = $branch->config;

        // Fail self
        if ( empty($config) ) {
            $config = '{}';
        }

        $config = json_decode( $config, true );

        $time = time();
        $config['tracking'][$time] = $this->playid;

        $config = (object) $config;

        $branch->config = json_encode($config);
        $branch->update();
    }


    private function getRequestedAction() {
        $action_id = $this->menuid;
        $requested_action = '';

        foreach ($this->actions as $action) {
            if ( $action->id == $action_id ) {
                $requested_action = $action;
            }
        }

        return $requested_action;
    }


    private function handleSubmissions() {
    }


    private function limitText($text, $limit) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = mb_substr($text, 0, $pos[$limit]) . '...';
        }

        return htmlspecialchars_decode($text);
    }

    public function getCleanName( $name ) {

        $chars = array(
            '&rsquo;' => '’',
        );

        foreach ($chars as $needle => $replacement) {
            $name = str_replace($needle, $replacement, $name);
        }

        return htmlspecialchars_decode( $name );
    }

    public function getNoDealsOutput() {
        $output[] = $this->getText( '{#no_new_deals_today#}!', array( 'style' => 'deals-notification' ) );
        return $output;
    }

    public function getLatestDeals() {
        $output = array();

        $deals_log = new MobiledealsModel();
        $log = $deals_log->getLatestNtfLog();

        if ( empty($log) ) {
            return $this->getNoDealsOutput();
        }

        $new_deals = $log->updated_deals_data;

        if ( empty($new_deals) ) {
            return $this->getNoDealsOutput();
        }

        $updates = json_decode( $new_deals );

        if ( !isset($updates->added_deals_ids) ) {
            return $this->getNoDealsOutput();
        }

        $resources = $updates->added_deals_ids;
        $resources = array_values( array_unique( $resources ) );

        // $this->rewriteActionField( 'subject', 'Today\'s Deals ('. $count .')' );
        $is_scrollable = false;

        if ( count($resources) > 10 ) {
            $chunks = array_chunk( $resources, 10, true );

            $next_page_id = 1;

            if ( isset($this->submit['next_page_id']) ) {
                $next_page_id = $this->submit['next_page_id'] + 1;
            }

            $deals = ( isset($chunks[$next_page_id-1]) ? $chunks[$next_page_id-1] : array() );
            $is_scrollable = true;
        } else {
            $deals = $resources;
        }

        $results = $this->populateLatestDeals( $deals );

        if ( !empty($results) ) {
            if ( $is_scrollable ) {
                $output[] = $this->getInfinitescroll( $results, array( 'next_page_id' => $next_page_id ) );
            } else {
                $output = $results;
            }
        } else {
            $output[] = $this->getText( '{#no_more_deals#}!', array( 'style' => 'deals-notification' ) );
        }

        return $output;
    }

    public function populateLatestDeals( $deals ) {

        if ( empty($deals) ) {
            return false;
        }

        $branchModel = new Aebranch();
        $actionModel = new Aeaction;

        $output = array();

        foreach ($deals as $deal_id) {
            $q = new CDbCriteria( array(
                'condition' => "config LIKE :dealid",
                'params'    => array(
                    ':dealid' => '%"dealid":"'. $deal_id .'"%'
                )
            ) );
             
            $action_obj = $actionModel->findByAttributes( array(), $q );

            if ( empty($action_obj) ) {
                continue;
            }

            if ( empty($action_obj->active) ) {
                continue;
            }

            $output[] = $this->renderDeal( $action_obj );
            $output[] = $this->getText( '', array( 'style' => 'row-divider' ) );
        }

        return $output;
    }

}