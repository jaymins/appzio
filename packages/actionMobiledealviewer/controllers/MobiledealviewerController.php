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

class MobiledealviewerController extends ArticleController {

    public $configobj;
    public $theme;

    public $action;
    public $branch_id;
    public $action_id;

    // Used internally
    public $current_link;
    public $config;

    public $local_image;
    public $cache_name;
    public $dealid;


    public function init() {
        if ( get_class(Yii::app()->getComponent('cache')) == 'CDummyCache' ) {
            Yii::app()->setComponent('cache', new CFileCache());
        }

        $this->cache_name = 'current-deal-action-' . $this->playid;
    }


    /*
    * Sets or Gets the currently requested action based on the YII's core caching mechanism
    */
    public function handleCurrentAction( $use_cache = false ) {        

        if ( $use_cache ) {
            $action = Appcaching::getGlobalCache( $this->cache_name );
            $this->action = $action;
            return true;
        }

        $action = Aeaction::model()->findByPk( $this->dealid );

        if ( empty($action) ) {
            return false;
        }

        // Cache the current deal for a specific Play ID
        Appcaching::setGlobalCache( $this->cache_name, $action );

        $this->action = $action;
           
    }


    public function getData(){

        $cachename = $this->playid .'-currentdealview';

        if ( !empty($this->menuid) ) {
            $this->dealid = $this->menuid;
            Appcaching::setGlobalCache($cachename, $this->menuid);
        } else {
            $this->dealid = Appcaching::getGlobalCache($cachename);
        }


        $data = new StdClass();

        $this->handleSubmissions();
        
        $data->scroll = $this->getDeal();
        $data->footer = $this->getDealFooter();

        // Set a counter
        $count_cache = $this->playid .'-openeddealscount';
        $current_count = Appcaching::getGlobalCache($count_cache);

        if ( empty($current_count) ) {
            $current_count = 0;
        }

        $current_count++;

        if (
            $current_count == 3 AND
            (isset($this->configobj->popup_action_id) AND !empty($this->configobj->popup_action_id)) AND
            !isset($_REQUEST['id'])
        ) {
            $data->onload['action'] = 'open-action';
            $data->onload['action_config'] = $this->configobj->popup_action_id;
            $data->onload['open_popup'] = 1;
        }

        if (
            ( $current_count == 6 OR ($current_count % 10 == 0) ) AND
            $this->getConfigParam( 'show_interstitials' )
        ) {
            $data->onload['action'] = 'open-interstitial';
        }

        Appcaching::setGlobalCache($count_cache, $current_count);

        return $data;
    }


    private function getDeal() {
        $output = array();

        if ( empty($this->dealid) ) {
            return $output;
        }

        // $this->action_id = $this->menuid;
        $this->handleCurrentAction();

        // Add tracking
        $this->saveStatus();

        if(!isset($this->action->name) OR !isset($this->action->config)){
            return $output;
        }

        $name = $this->action->name;
        $config = $this->action->config;

        if ( empty($config) ) {
            $output[] = $this->getError( '{#configuration_error#}', array( 'style' => 'deals-notification' ) );
        }

        $this->config = json_decode( $config );

        $this->current_link = $this->config->link;

        $time = ( $this->config->date ? $this->config->date : $this->config->date );
        $timestamp = strtotime( $time );
        $diff = TimeHelpers::getTimeDiff( $timestamp, time() );
        
        $output[] = $this->getDealImage();
        $output[] = $this->getText( $this->getCleanName($name), array( 'style' => 'deal-title' ) );

        $output[] = $this->getText( '', array('style' => 'deal-separator') );

        $posted_icon = $this->getColumn( array(
            $this->getImage( 'icon-clock-main.png', array( 'style' => 'hcr-icon' ) ),
        ), array( 'style' => 'heading-cell-left' ) );

        $posted_time = $this->getColumn( array(
            $this->getText( '{#posted#} ' . $diff, array( 'style' => 'hcr-text' ) ),
        ), array( 'style' => 'heading-cell-right' ) );

        $output[] = $this->getRow(
            array( $posted_icon, $posted_time ),
            array( 'style' => 'time-row' )
        );

        $output[] = $this->getText( '', array('style' => 'deal-separator') );
        
        // Buttons
        $output[] = $this->getActionButtons();

        $output[] = $this->getText( '', array('style' => 'deal-separator') );

        // Heading
        $details = $this->getColumn( array(
            $this->getText( '{#details#}', array( 'style' => 'hc-text' ) ),
        ), array( 'style' => 'heading-cell' ) );

        $output[] = $this->getRow(
            array( $details ),
            array( 'style' => 'main-row' )
        );
            
        if ( isset($this->config->description) ) {
            $output[] = $this->getText( htmlspecialchars_decode($this->config->description), array( 'style' => 'deal-description' ) );
        }

        if ( $this->getConfigParam( 'show_interstitials' ) ) {
            $output[] = $this->getBanner(false, array( 'style' => 'deal-banner' ));
        }

        // Tracking
        // $this->callRemoteImage();

        $vars = $this->getPlayVariables();
        $source = $vars['system_source'];

        if ( $source == 'client_android' ) {
            $share_url = 'https://play.google.com/store/apps/details?id=com.appzio.myfreebieshop';
        } else {
            $share_url = 'https://itunes.apple.com/us/app/freebie-shop/id1125945309?mt=8';
        }

        // Fake the config
        $this->rewriteActionConfigField('mobile_sharing', 1);
        $this->rewriteActionConfigField('share_description', $share_url);
        $this->rewriteActionConfigField('share_title', $name);

        /*
        $this->rewriteActionConfigField('share_description', $this->config->description);
        if ( isset($this->config->has_valid_image) AND $this->config->has_valid_image ) {
            $this->rewriteActionConfigField('share_image', $this->local_image);
        }        
        $this->rewriteActionConfigField('share_url', $this->current_link);
        */

        return $output;
    }


    private function getDealImage() {
        if ( isset($this->config->has_valid_image) AND $this->config->has_valid_image ) {
            $image = $this->config->listing_image;
        } else {
            $image = 'deal-placeholder.png';
        }

        // $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 800, 'imgcrop' => 'no'));
        // $this->local_image = $listing_image;

        // $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/documents/games/' . $this->gid . '/original_images/';
        // $file = $path . $image;

        list($width, $height) = @getimagesize($image); 
        $dim = array(
            'height' => $height,
            'width' => $width
        );

        $dim_height = ( $dim['height'] ? $dim['height'] : '200' );
        $dim_width = ( $dim['width'] ? $dim['width'] : '500' );

        $image_height_dp = $this->screen_width * $dim_height / $dim_width;
        $image_height_dp = round( $image_height_dp );

        $img_margin = ( $dim_width > 600 ? '10 10 10 10' : '10 50 10 50' );

        $onclick = new stdClass();
        $onclick->id = 'get-deal';
        $onclick->action = 'open-url';
        $onclick->action_config = $this->current_link;

        $options = array(
            'use_filename' => true,
            'margin' => $img_margin,
            'height' => $image_height_dp,
            'priority' => 9,
            'onclick' => $onclick
        );

        $output = $this->getImage( $image, $options );

        return $output;
    }


    private function callRemoteImage() {

        /*
        ignore_user_abort(true);

        //-------------get curl contents----------------

        $ch = curl_init( $this->config->listing_image );
        curl_setopt_array($ch, array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOSIGNAL => 1, //to timeout immediately if the value is < 1000 ms
            CURLOPT_TIMEOUT_MS => 50, //The maximum number of mseconds to allow cURL functions to execute
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1
        ));
        $out = curl_exec($ch);

        //-------------parse curl contents----------------

        //$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        //$header = substr($out, 0, $header_size);
        //$body = substr($out, $header_size);

        curl_close($ch);

        return true;
        */

        $options = array(
            CURLOPT_RETURNTRANSFER => true,         // return web page
            CURLOPT_HEADER         => false,        // do not return headers
            CURLOPT_FOLLOWLOCATION => true,         // follow redirects
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 2,            // timeout on connect (in seconds)
            CURLOPT_TIMEOUT        => 2,            // timeout on response (in seconds)
            CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,        // SSL verification not required
            CURLOPT_SSL_VERIFYHOST => false,        // SSL verification not required
        );

        if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
            $options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
        }

        $ch = curl_init( $this->config->listing_image );
        curl_setopt_array( $ch, $options );
        curl_exec( $ch );

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return true;
    }


    private function getActionButtons() {

        $votes_count = 0;
        $voteup_icon = 'thumbs-up-inner.png';
        $votedown_icon = 'dislike-thumb.png';

        $user_voted_up = false;
        $user_voted_down = false;

        if ( isset($this->config->upvotes) ) {
            $upvotes = (array) $this->config->upvotes;
            $votes_count = count( $upvotes );
            if ( in_array($this->playid, $upvotes) ) {
                $user_voted_up = true;
                $voteup_icon = 'thumbs-up_liked.png';
            }
        }

        if ( isset($this->config->downvotes) ) {
            $downvotes = (array) $this->config->downvotes;
            if ( in_array($this->playid, $downvotes) ) {
                $user_voted_down = true;
                $votedown_icon = 'dislike-thumb_disliked.png';
            }
        }

        $button_up = $this->getImagebutton( $voteup_icon, 'btn-vote-up', false, array( 'style' => 'tools-image', 'sync_open' => 1 ) );
        $text_up = $this->getTextButton( '{#vote_up#} ('. $votes_count .')', array( 'style' => 'tools-text', 'id' => 'btn-vote-up' ));

        $button_down = $this->getImagebutton( $votedown_icon, 'btn-vote-down', false, array( 'style' => 'tools-image' ) );
        $text_down = $this->getTextButton( '{#vote_down#}', array( 'style' => 'tools-text', 'id' => 'btn-vote-down' ));

        // if ( $user_voted_down ) {
        //     $button_up = $this->getImage( $voteup_icon, array( 'style' => 'tools-image' ) );
        //     $text_up = $this->getText( 'Vote Up ('. $votes_count .')', array( 'style' => 'tools-text' ) );
        // }

        // if ( $user_voted_up ) {
        //     $button_down = $this->getImage( $votedown_icon, array( 'style' => 'tools-image' ) );
        //     $text_down = $this->getText( 'Vote Down', array( 'style' => 'tools-text' ) );
        // }

        $star_image = 'bookmark-star.png';

        if ( $this->isBookmarked() ) {
            $star_image = 'bookmark-star_bookmarked.png';
        }

        $item_1 = $this->getColumn( array(
            $button_up,
            $text_up,
        ), array( 'style' => 'row-cell' ) );
        
        $item_2 = $this->getColumn( array(
            $button_down,
            $text_down,
        ), array( 'style' => 'row-cell' ) );

        $item_3 = $this->getColumn( array(
            $this->getImagebutton( $star_image, 'btn-bookmark', false, array( 'style' => 'tools-image' ) ),
            $this->getTextButton( '{#save#}', array( 'style' => 'tools-text', 'id' => 'btn-bookmark' )),
        ), array( 'style' => 'row-cell' ) );

        $item_4 = $this->getColumn( array(
            $this->getImagebutton( 'share-connection-sing.png', 'btn-share', false, array( 'action' => 'share', 'style' => 'tools-image' ) ),
            $this->getTextButton( '{#share#}', array( 'style' => 'tools-text', 'action' => 'share', 'id' => 'btn-share' )),
        ), array( 'style' => 'row-cell' ) );

        $output = $this->getRow(
            array( $item_1, $item_2, $item_3, $item_4 ),
            array( 'style' => 'row-icons' )
        );

        return $output;
    }


    private function handleSubmissions() {

        // Not a submission
        if ( empty($this->menuid) ) {
            return false;
        }

        $this->handleCurrentAction( $use_cache = true );

        // Cached action doesn't exists
        if ( empty($this->action) ) {
            return false;
        }

        $config = $this->action->config;

        // Fail self
        if ( empty($config) ) {
            $config = '{}';
        }

        $config = json_decode( $config, true );

        if ( !isset($config['upvotes']) OR empty($config['upvotes']) ) {
            $config['upvotes'] = array();
        }

        if ( !isset($config['downvotes']) OR empty($config['downvotes']) ) {
            $config['downvotes'] = array();
        }

        $config['upvotes'] = (array)$config['upvotes'];
        $config['downvotes'] = (array)$config['downvotes'];

        $options = array(
            'btn-vote-up'   => 'upvotes',
            'btn-vote-down' => 'downvotes',
        );

        $update_config = false;
        $current_vote = false;

        foreach ($options as $button => $db_value) {
            if ( $this->menuid == $button ) {
                    
                if ( in_array($this->playid, $config['upvotes']) ) {
                    $key = array_search($this->playid, $config['upvotes']);
                    unset($config['upvotes'][$key]);
                    $current_vote = 'upvotes';
                }

                if ( in_array($this->playid, $config['downvotes']) ) {
                    $key = array_search($this->playid, $config['downvotes']);
                    unset($config['downvotes'][$key]);
                    $current_vote = 'downvotes';
                }

                if ( $db_value != $current_vote ) {
                    $config[$db_value][] = $this->playid;
                }

                $update_config = true;
            }
        }

        if ( $update_config ) {
            $config = (object) $config;
            $this->action->config = json_encode($config);
            $this->action->update();
        }        

        if ( $this->menuid == 'btn-bookmark' ) {
            $action = 'save';
            
            if ( $this->isBookmarked() ) {
                $action = 'remove';
            }

            $this->moduleBookmarking($action, array( 'actionid' => $this->action->id, 'updatenotifications' => true ));
        }

        // "Flush" the cache
        Yii::app()->cache->set( $this->cache_name, $this->action );
    }


    private function saveStatus() {

        if ( empty($this->action) ) {
            return false;
        }

        $config = $this->action->config;

        // Fail self
        if ( empty($config) ) {
            $config = '{}';
        }

        $config = json_decode( $config, true );

        $time = time();
        $config['tracking'][$time] = $this->playid;

        $config = (object) $config;

        $this->action->config = json_encode($config);
        $this->action->update();

        // "Flush" the cache
        Yii::app()->cache->set( $this->cache_name, $this->action );
    }


    private function getDealFooter() {
        $output = array();
        
        $output[] = $this->getImagebutton( 'deal-button.png', 'get-deal', false, array( 'action' => 'open-url', 'config' => $this->current_link, 'style' => 'deal-heading' ) );

        return $output;
    }


    private function isBookmarked() {

        $this->loadVariables();
        $this->loadVariableContent();

        if ( !isset($this->varcontent['bookmarks']) OR empty($this->varcontent['bookmarks']) ) {
            return false;
        }

        $bookmarks = json_decode($this->varcontent['bookmarks'], true);

        if ( array_key_exists($this->action->id, $bookmarks) ) {
            return true;
        }
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

}