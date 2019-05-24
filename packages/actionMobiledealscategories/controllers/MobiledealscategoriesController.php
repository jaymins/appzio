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

class MobiledealscategoriesController extends ArticleController {

    public $configobj;
    public $theme;

    public $gamevariables;

    public $action;
    public $branch_id;
    public $action_id;

    public $actions;

    public function init() {
    }


    public function getData(){

        $this->saveIPAddress();
        
        $ask_permissions = $this->askPermissions();

        $this->actions = $this->getActions();

        $data = new StdClass();

        if ( $ask_permissions ) {
            $data->onload[] = $ask_permissions;
        }

        if ( isset($_GET['get-info-manually']) ) {
            $this->getDebugInfo();
        }

        if ( isset($this->configobj->list_random_items) AND $this->configobj->list_random_items ) {

            $scroll = $this->getTopDeals();
            $scroll = array_merge( $scroll, $this->getCategoriesHeader() );
            $scroll = array_merge( $scroll, $this->getCategories() );

            $data->scroll = $scroll;
        } else {
            $data->header = $this->getCategoriesHeader();
            $data->scroll = $this->getCategories();
        }

        return $data;
    }

    public function askPermissions() {
        
        if ( $this->userid != $this->getSavedVariable('push_permission_checker') ) {

            $onload = new StdClass();
            $onload->id = 'push-permission';
            $onload->action = 'push-permission';

            $this->saveVariable('push_permission_checker', $this->userid);

            return $onload;
        } else {
            return false;
        }

    }

    private function getCategoriesHeader() {
        $output = array();

        $output[] = $this->getText('{#all_categories#}', array( 'style' => 'categories-heading' ));
        $output[] = $this->getText('', array( 'style' => 'category-divider' ));

        $boobmarks_title = $this->getColumn( array(
            $this->getText('{#bookmarks#}', array( 'style' => 'category-title-bold' )),
        ), array( 'style' => 'category-cell-left' ) );

        $bookmarks_button = $this->getColumn( array(
            $this->getImage('category-arrow-btn.png', array( 'style' => 'category-arrow')),
        ), array( 'style' => 'category-cell-right' ) );

        $onclick_options = new StdClass();
        $onclick_options->id = $this->configobj->deals_bookmarks_action;
        $onclick_options->action = 'open-action';
        $onclick_options->action_config = $this->configobj->deals_bookmarks_action;
        // $onclick_options->context = 'dealcat-' . $this->configobj->deals_bookmarks_action;
        $onclick_options->sync_open = 1;
        $onclick_options->sync_close = 1;
        $onclick_options->back_button = 1;

        $output[] = $this->getRow(array(
            $boobmarks_title, $bookmarks_button
        ), array( 'style' => 'category-row', 'onclick' => $onclick_options ));

        $output[] = $this->getText('', array( 'style' => 'category-divider' ));

        return $output;
    }


    private function getCategories() {
        $output = array();

        foreach ($this->actions as $action) {

            $action_type = Aeactiontypes::model()->findByPk( $action->type_id );

            if ( $action_type->shortname == 'mobiledealscategories' ) {
                continue;
            }

            $category_title = $this->getColumn( array(
                $this->getText(htmlspecialchars_decode($action->name), array( 'style' => 'category-title' )),
            ), array( 'style' => 'category-cell-left' ) );

            $category_button = $this->getColumn( array(
                $this->getImage('category-arrow-btn.png', array( 'style' => 'category-arrow')),
            ), array( 'style' => 'category-cell-right' ) );

            $onclick_options = new StdClass();
            $onclick_options->id = $action->id;
            $onclick_options->action = 'open-action';
            $onclick_options->action_config = $action->id;
            // $onclick_options->context = 'dealcat-' . $action->id;
            $onclick_options->sync_open = 1;
            $onclick_options->sync_close = 0;
            $onclick_options->back_button = 1;
            
            $output[] = $this->getRow(array(
                $category_title, $category_button
            ), array( 'style' => 'category-row', 'onclick' => $onclick_options ));

            $output[] = $this->getText('', array( 'style' => 'category-divider' ));

        }

        return $output;
    }


    private function getActions() {
        // $actions = CHtml::listData(Aeaction::getAllActionsByType($this->gid, 'mobiledealviewer'),'id','name');

        $cache_name = 'cached_category_actions';

        if ( $cached_data = Appcaching::getActionCache($this->actionid, $this->playid, $this->gid, $cache_name) ) {
            return $cached_data;
        }

        $branch_id = $this->branch_id;
        $actions = Aeaction::model()->findAllByAttributes(array('branch_id' => $branch_id),array('order' => '`order`'));
        Appcaching::setActionCache($this->actionid, $this->gid, $cache_name, $actions);

        return $actions;
    }


    private function getTopDeals() {
        Yii::import('application.modules.aelogic.packages.actionMobiledeals.controllers.*');
        
        $deals = new MobiledealsController( $this );
        $deals->actions = $this->getRandomActions();

        $output = array();

        $deals = $deals->getDeals();

        foreach ($deals as $deal) {
            $output[] = $deal;
        }

        return $output;
    }


    private function getRandomActions() {
        $model = new Aeaction();
        $model->limit_from = 0;
        $model->limit_to = 12;

        $action_type = Aeactiontypes::model()->findByAttributes( array('shortname' => 'mobiledealviewer') );

        $actions = $model->getRandomActionsData( $action_type->id );
        
        // Randomize the order of the array
        shuffle( $actions );

        $actions = array_slice($actions, 0, 2);

        return $actions;
    }

    public function saveIPAddress() {

        if ( !isset($_SERVER['REMOTE_ADDR']) OR empty($_SERVER['REMOTE_ADDR']) ) {
            return false;
        }

        $this->saveVariable( 'user_ip_address', $_SERVER['REMOTE_ADDR'] );

    }

    public function getDebugInfo() {

        $actionModel = new Aeaction;

        $q = new CDbCriteria( array(
            'condition' => "config LIKE :tracking",
            'params'    => array(
                ':tracking' => '%tracking%'
            )
        ) );
         
        $actions = $actionModel->findAllByAttributes( array(), $q );

        $users_data = array();

        foreach ($actions as $action) {
            if ( !isset($action->config) OR empty($action->config) ) {
                continue;
            }

            $config = @json_decode( $action->config );

            if ( empty($config) OR !isset($config->tracking) ) {
                continue;
            }

            $count = 0;
            $vars = array();
            foreach ($config->tracking as $stamp => $play_id) {

                if ( !$count ) {
                    $vars = AeplayVariable::getArrayOfPlayvariables( $play_id );
                    $users_data[$play_id]['name'] = ( isset($vars['real_name']) ? $vars['real_name'] : 'N/A' );
                    $users_data[$play_id]['email'] = ( isset($vars['email']) ? $vars['email'] : 'N/A' );
                }

                $users_data[$play_id]['activities'][$stamp] = array(
                    'action_name' => ( isset($action->name) ? $action->name : 'N/A' ),
                    'real_time' => date( 'F j, Y, g:i a', $stamp ),
                );

                $count++;
            }

        }

        // echo '<pre>';
        // print_r( $users_data );
        // echo '</pre>';

        $users_many_times = array();
        $users_one_time = array();

        foreach ($users_data as $user_play_id => $data) {
            $activities = $data['activities'];
            $stamps = array_keys($activities);
            array_multisort($stamps, SORT_ASC);

            $first_stamp = $stamps[0];
            $last_stamp = end($stamps);

            // More than a day
            if ( $last_stamp - $first_stamp > 86400 ) {
                $users_many_times[] = $user_play_id;
            } else {
                $users_one_time[] = $user_play_id;
            }
        }

        echo '<pre>';
        print_r( 'More than one time:' . count( $users_many_times ) );
        echo '<br />';
        print_r( 'One time:' . count( $users_one_time ) );
        echo '</pre>';
        exit;

        die( 'Completed!' );
    }

}