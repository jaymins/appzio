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

class MobilenewsviewerController extends ArticleController {

    public $configobj;
    public $theme;
    public $has_available_hours = true;

    public $gamevariables;

    public $action;
    public $branch_id;
    public $action_id;

    
    public function adminHook() {

        if ( !isset($_REQUEST) AND empty($_REQUEST) ) {
            return false;
        }

        $this->getCurrentAction();

        $config = $this->action->config;

        if ( empty($config) ) {
            return false;
        }

        $config = json_decode( $config );

        if ( !isset($config->news_action_id) OR !isset($config->news_item) ) {
            return;
        }

        $parent_action = Aeaction::model()->findByPk( $config->news_action_id );
        $parent_action_config = json_decode( $parent_action->config );
        $news = $parent_action_config->news;

        $item = isset($news[$config->news_item]) ? $news[$config->news_item] : '';

        if ( empty($item) ) {
            return;
        }

        $config->news_item_full = $item;

        if ( isset($item->images) ) {
            $images_data = $this->handleArticleImages( $item->images );
            $config->images_data = $images_data;
        }

        $config = json_encode($config);
        $this->action->config = $config;
        $this->action->update();
    }


    private function handleArticleImages( $images ) {

        if ( empty($images) ) {
            return false;
        }

        // Get the session based App ID
        $app_id = Yii::app()->session['gid'];

        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/documents/games/' . $app_id . '/news_api_images/';

        if ( !is_dir($path) ) {
            mkdir($path,0777);
        }

        $images_data = array();

        foreach ($images as $entry) {
            $source = $entry->url;

            $filename = basename($source);
            copy($source, $path . $filename);

            $images_data[] = array(
                'url'       => $filename,
                'format'    => $entry->format,
                'caption'   => $entry->caption,
                'copyright' => $entry->copyright,
            );
        }

        return $images_data;
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

        $trigger = 'registration_phase';

        if ( !isset($this->varcontent[$trigger]) ) {
            // $this->saveActionVars(array(), 'booking-1');
        }

        $this->handleSubmissions();

        $data->scroll = $this->getNewsItem();

        // $step = $this->varcontent[$trigger];

        return $data;
    }


    private function getNewsItem() {
        $output = array();

        $news_item = isset($this->configobj->news_item_full) ? $this->configobj->news_item_full : '';

        if ( empty($news_item) ) {
            $output[] = $this->getText( 'News Content Missing!' );
        }

        $required_data = array(
            'title', 'text', 'date'
        );

        foreach ($required_data as $param) {
            if ( !isset($news_item->{$param}) ) {
                $output[] = $this->getText( 'Missing Required Param: ' . $param );
                return $output;
            }
        }

        $output[] = $this->getText( html_entity_decode($news_item->title), array( 'style' => 'news-heading' ) );
        $output[] = $this->getHTML( $news_item->text, array( 'style' => 'news-text' ) );
        $output[] = $this->getText( $news_item->date, array( 'style' => 'news-date' ) );

        $output[] = $this->getText( 'Test Text', array( 'style' => 'article-button' ) );
        $output[] = $this->getButton( 'Test Test', array( 'id' => 541515 ) );

        return $output;
    }


    private function handleSubmissions() {

    }


}