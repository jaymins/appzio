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

class MobilenewsController extends ArticleController {

    public $configobj;
    public $theme;
    public $has_available_hours = true;

    public $gamevariables;

    public $action;
    public $branch_id;
    public $action_id;

    
    public function adminHook() {

        if ( !isset($_GET['update-rss-news']) AND !isset($_GET['update-api-news']) ) {
            return false;
        }

        $this->getCurrentAction();

        $config = $this->action->config;

        if ( empty($config) ) {
            return false;
        }

        $config = json_decode( $config );

        if ( isset($_GET['update-rss-news']) ) {
            $config = $this->updateRSSData( $config );
        } else if ( isset($_GET['update-api-news']) ) {
            $config = $this->updateAPIData( $config );
        }

        // Save the news
        $config = json_encode($config);
        $this->action->config = $config;
        $this->action->update();
    }


    private function updateRSSData( $config ) {
        $rss_link = ( isset($config->rss_link) ? $config->rss_link : '' );

        if ( empty($rss_link) ) {
            return false;
        }

        $rss = new DOMDocument();
        $rss->load( $rss_link );

        $feed = array();
        foreach ($rss->getElementsByTagName('item') as $node) {
            $text = $node->getElementsByTagName('description')->item(0)->nodeValue;
            $title = $node->getElementsByTagName('title')->item(0)->nodeValue;

            $feed[] = array (
                'title' => mb_convert_encoding($title,'HTML-ENTITIES','UTF-8'),
                'text'  => mb_convert_encoding($text,'HTML-ENTITIES','UTF-8'),
                'link'  => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date'  => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );
        }

        $config->news = $feed;

        // foreach ($feed as $item) {
        //     $title = str_replace(' & ', ' &amp; ', $item['title']);
        //     $link = $item['link'];
        //     $description = $item['text'];
        //     $date = date('l F d, Y', strtotime($item['date']));

        //     echo '<h3><a style="text-decoration: none;" href="'.$link.'" title="'.$title.'">'.$title.'</a></h3>';
        //     echo '<p><em>Posted on '.$date.'</em></small></p>';
        //     echo '<p>'.$description.'</p>';
        //     echo '<hr style="border-bottom: 1px solid #ddd">';
        // }

        return $config;
    }


    private function updateAPIData( $config ) {
        $api_link = ( isset($config->api_link) ? $config->api_link : '' );

        if ( empty($api_link) ) {
            return false;
        }

        $contents = file_get_contents( $api_link );

        if ( empty($contents) ) {
            return false;
        }

        $contents = json_decode( $contents );

        $feed = array();

        foreach ($contents->results as $item) {
            $feed[] = array (
                'title' => $item->title,
                'text'  => $item->abstract,
                'link'  => $item->url,
                'date'  => $item->created_date,
                'images' => $item->multimedia,
            );
        }

        $config->news = $feed;

        return $config;
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

        $bla[] = array();
        $data->scroll = $bla;

        // $step = $this->varcontent[$trigger];

        return $data;
    }


    private function handleSubmissions() {

    }


}