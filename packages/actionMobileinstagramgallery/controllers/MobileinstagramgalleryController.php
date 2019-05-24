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
Yii::import('application.modules.aelogic.packages.actionRecipe.models.*');

class MobileinstagramgalleryController extends ArticleController {

    public $data;
    public $instagram_user_info;

    public $branch_id;
    public $action_id;

    public function getCurrentAction() {
        $object = Aeaction::model()->findByPk( $this->action_id );

        if ( empty($object) ) {
            return false;
        }

        return $object;
    }


    public function adminHook(){

        $base_url = Yii::app()->getBaseUrl(true);

        $url = Yii::app()->request->requestUri;

        $url = URLHelpers::remove_query_arg( 'code', $url );
        $url = str_replace('?', '/', $url);
        $url = str_replace(array('&', '='), array( '/', '/' ), $url);

        $return_url = $base_url . $url;

        // Connect to Instagram / Get Access Token
        if ( isset($_GET['code']) ) {
            $code       = $_GET['code'];
            $instagram  = new InstagramConnector(CLIENT_ID, CLIENT_SECRET, null);

            $response = $instagram->getAccessToken($code,  REDIRECT_URI . '?return_uri=' . htmlentities($return_url));
            $token    = $response->access_token;
            $userid   = $response->user->id;

            $this->saveResponse( $response );
        }

        // Get Images
        if ( isset($token) AND !empty($token) ) {
            $this->flushImages( $token, $userid );
            Yii::app()->request->redirect($base_url .'/en/aegameauthor/default/editTask/?lid=' . $this->branch_id . '&tid=' . $this->action_id . '&&tab=level_in' );
        }

        if ( isset($_GET['flush-instagram-images']) ) {
            $action = $this->getCurrentAction();
            $config = json_decode($action->config);
            $token = $config->instagram_access_token;
            $userid = $config->instagram_id;
            $this->flushImages( $token, $userid );
            Yii::app()->request->redirect($base_url .'/en/aegameauthor/default/editTask/?lid=' . $this->branch_id . '&tid=' . $this->action_id . '&&tab=level_in' );
        }

    }


    private function flushImages( $token, $userid ) {

        if ( empty($token) OR empty($userid) ) {
            return false;
        }

        $instagram = new InstagramConnector(CLIENT_ID, CLIENT_SECRET, $token);

        $this->data = array();
        $feed = $instagram->get('users/' . $userid . '/media/recent');

        $info = $instagram->get('users/' . $userid);

        if ( !empty($info) AND $info->meta->code == 200 ) {
            $this->instagram_user_info = $info->data;
        }

        if ( $feed AND $feed->meta->code == 200 AND !empty($feed->data) ) {
            
            foreach ($feed->data as $i => $img) {
                $this->data[$img->created_time . '_' . $i] = array(
                    'image_src' => $img->images->standard_resolution->url,
                    // 'image_url' => $img->link,
                    'comment'   => ( isset($img->caption->text) ? $img->caption->text : '' ),
                    'date'      => date( 'D, j. \of M @ H:i', $img->created_time ),
                );
            }

        }

        // Copy Images to the local directory
        $this->copyInstagramImages();

        // Save the Data to the DB
        $this->saveData();
    }


    private function saveResponse( $response ) {
        
        if ( !isset($response->access_token) ) {
            return;
        }

        $actionobj = $this->getCurrentAction();
        
        $config = $actionobj->config;
        $config = @json_decode($config);

        $config->instagram_access_token = $response->access_token;
        $config->instagram_username     = $response->user->username;
        $config->instagram_bio          = $response->user->bio;
        $config->instagram_website      = $response->user->website;
        $config->instagram_profile_img  = $response->user->profile_picture;
        $config->instagram_full_name    = $response->user->full_name;
        $config->instagram_id           = $response->user->id;

        $actionobj->config = json_encode($config);

        $actionobj->update();
    }


    private function copyInstagramImages() {

        if ( !isset($this->data) OR empty($this->data) ) {
            return;
        }
                
        // Get the session based App ID
        $app_id = Yii::app()->session['gid'];

        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/documents/games/' . $app_id . '/instagram/';

        if ( !is_dir($path) ) {
            mkdir($path,0777);
        }

        foreach ($this->data as $entry) {
            $source = $entry['image_src'];
            $filename = basename($source);
            copy($source, $path . $filename);
        }
    }


    private function saveData() {

        if ( !isset($this->data) OR empty($this->data) ) {
            return;
        }

        $actionobj = $this->getCurrentAction();

        $config = $actionobj->config;
        $config = @json_decode($config);

        $tmp_data = array();
        foreach ($this->data as $key => $values) {
            foreach ($values as $handler => $value) {

                $new_value = $value;
                if ( $handler == 'image_src' ) {
                    $new_value = basename($value);
                }

                $tmp_data[$key][$handler] = $new_value;
            }
        }

        $config->instagramdata = json_encode($tmp_data); // Additional encoding needed ..

        if ( !empty($this->instagram_user_info) ) {
            $config->instagram_full_name = $this->instagram_user_info->full_name;
            $config->instagram_posts_count = $this->instagram_user_info->counts->media;
            $config->instagram_followed_by = $this->instagram_user_info->counts->followed_by;
            $config->instagram_follows = $this->instagram_user_info->counts->follows;
        }

        $actionobj->config = json_encode($config, JSON_FORCE_OBJECT);

        $actionobj->update();
    }


    /* this gets called when doing branchlisting */
    public function getData(){

        $data = new StdClass();
        $data->header = array();
        $data->scroll = $this->getScroll();

        return $data;
    }


    private function getScroll(){

        $output = array();

        $instagram_posts_count = ( !empty($this->configobj->instagram_posts_count) ? number_format($this->configobj->instagram_posts_count) : '0' );
        $instagram_followed_by = ( !empty($this->configobj->instagram_followed_by) ? number_format($this->configobj->instagram_followed_by) : '0' );
        $instagram_follows  = ( !empty($this->configobj->instagram_follows) ? number_format($this->configobj->instagram_follows) : '0' );

        $content_left = $this->getColumn(array(
                $this->getImage('portrait-image.jpg')
            ), array( 'style' => 'portrait_image_left' ));

        $content_right = $this->getColumn(array(
                $this->getText($this->requireConfigParam('instagram_full_name'), array( 'style' => 'row_right_text' )),

            $this->getRow( array(
                    $this->getColumn( array(
                        $this->getText($instagram_posts_count, array( 'style' => 'rric_1' )),
                        $this->getText('posts', array( 'style' => 'rric_2' )),
                    ), array( 'style' => 'row_right_inner_content' ) ),
                    $this->getColumn( array(
                        $this->getText($instagram_followed_by, array( 'style' => 'rric_1' )),
                        $this->getText('followers', array( 'style' => 'rric_2' )),
                    ), array( 'style' => 'row_right_inner_content' ) ),
                    $this->getColumn( array(
                        $this->getText($instagram_follows, array( 'style' => 'rric_1' )),
                        $this->getText('following', array( 'style' => 'rric_2' )),
                    ), array( 'style' => 'row_right_inner_content' ) ),
                ), array( 'style' => 'row_right_content' ) ),
            ), array( 'style' => 'row_right' ));

        $output[] = $this->getRow(
            array( $content_left, $content_right ),
            array( 'style' => 'main_row' )
        );
        
        $gallery_items = $this->moduleGallery( array(
                'images'        => $this->requireConfigParam('instagramdata'),
                'viewer_id'     => $this->requireConfigParam('gallery_item_id'),
                'grid_spacing'  => true,
                'open_in_popup' => true,
                'dir'           => 'instagram'
            ) );

        foreach ($gallery_items as $item) {
            $output[] = $item;
        }

        return $output;
    }


}