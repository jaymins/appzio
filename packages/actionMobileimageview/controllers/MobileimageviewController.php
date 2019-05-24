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
Yii::import('application.modules.aelogic.packages.actionMobileimageview.models.*');
Yii::import('application.modules.aelogic.packages.actionRecipe.models.*');

class MobileimageviewController extends ArticleController {

    public $factoryobj;

    /* this is a custom function which is called in case action is invisible */

    public function tab1(){

        /* turn bookmark off */
        if($this->menuid == 200){
            $this->moduleBookmarking('remove');
            $this->flushCacheTab(1);
        }

        /* turn bookmark on */
        if($this->menuid == 201){
            $this->moduleBookmarking('save',array('updatenotifications' => true,'notification_action' => 'liked'));
            $this->flushCacheTab(1);
        }

        $data = new StdClass();
        $data->header = $this->tabsmenu_json;
        $data->scroll = $this->getScroll();

        $output = $data;

        return $output;
    }

    private function getScroll($data=false,$ref_id=false){

        if(get_class(Yii::app()->getComponent('cache')) == 'CDummyCache'){
            Yii::app()->setComponent('cache', new CFileCache());
        }

        if(isset($_REQUEST['referring_action']) AND isset($_REQUEST['menuid'])){
            $playModel = new AetaskLogic;
            $result = $playModel->findByPk( $_REQUEST['referring_action'] );

            if ( !empty($result) ) {
                $pointer_id = $result->action_id;
            } else {
                $pointer_id = $_REQUEST['referring_action'];
            }

            $cache = Yii::app()->cache->get( $pointer_id .'-' .'galleryitems');
            $menuid = ( isset( $_REQUEST['menuid'] ) ? $_REQUEST['menuid'] : '' );
            $actionid = ( isset( $_REQUEST['actionid'] ) ? $_REQUEST['actionid'] : '' );

            if($cache AND isset($cache[$menuid])){
                $data = $cache[$menuid];

                $tmp_cached_data = array();
                $tmp_cached_data['data'] = $data;
                $tmp_cached_data['menuid'] = $menuid;

                Yii::app()->cache->set( $actionid .'-' .'tempcache-content',$tmp_cached_data);
                Yii::app()->cache->set( 'currentgalleryaction-'.$this->playid,$tmp_cached_data);
                $ref_id = $actionid . $menuid;

                return $this->scrolldata($data,$ref_id);
            }
        }

        if(isset($_REQUEST['actionid'])){
            $cache = Yii::app()->cache->get( $_REQUEST['actionid'] .'-' .'tempcache-content');
            if($cache){
                return $this->scrolldata($cache['data']);
            }
        }

        $cache = Yii::app()->cache->get( 'currentgalleryaction-' .$this->playid);

        if($cache){
            return $this->scrolldata($cache['data']);
        }

        $output[] = $this->getText($this->playid);
        $output[] = $this->getText( 'Picture not found.', array( 'style' => 'recipe_chat_nocomments' ) );

        return $output;
    }

    private function scrolldata($data){

        $output = array();

        if(isset($data['image'])){
            $output[] = $this->getImage( $data['image'], array( 'style' => 'miv_image' ) );

            if ( isset($data['user']) ) {
                $output[] = $this->getUserInfoWithBookmark($data['user'],false,true);
            }

            $data_values = array(
                'date' => 'miv_date',
                'comment' => 'miv_comment',
            );

            foreach ($data_values as $key => $field_style) {

                if ( isset($data[$key]) ) {
                    $output[] = $this->getText( $data[$key], array( 'style' => $field_style ) );
                }
            }

            if ( isset($data['action_id']) ) {
                $chat = $this->moduleChat(array(
                    'context' => 'action',
                    'context_key' => $data['action_id'],
                    'hint' => 'Write a comment',
                ));

                $output = array_merge($output,$chat->scroll);
                $output = array_merge($output,$chat->footer);
            }

        }

        return $output;
    }


}