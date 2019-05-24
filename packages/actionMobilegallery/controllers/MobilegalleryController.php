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
Yii::import('application.modules.aelogic.packages.actionMobilegallery.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilegallery.controllers.*');
Yii::import('application.modules.aetask.models.*');

class MobilegalleryController extends ArticleController {

    public $maindata;
    public $module;

    public $alchemykey = '8d8f959a59f7bb71035f8f6dcda97d9d992a2ea5';
    public $alchemyendpoint = 'https://gateway-a.watsonplatform.net/calls';

    public $headerdata = array();
    public $scrolldata = array();
    public $footerdata = array();

    public $mainobj;
    public $tabsmenu_images;
    public $updatebranches = false;

    public $mode;


    public function init(){
        $this->mode = $this->requireConfigParam('mode');

        if($this->mode != 'entry'){
            $this->tabsmenu_images = array(
                '1' => array('tab-grid.png','33%'),
                '2' => array('tab-bookmarks.png','33%'),
                '3' => array('tab-photo.png','34%')
            );
        }
    }

    public function tab1(){

        if($this->mode == 'entry'){
            $this->module = 'Entry';
            $obj = new mgEntryModel($this);
            $obj->mainobj = $this;
            return $obj->getData();
        }

        $this->showGallery();

        $data = new StdClass();
        $data->header = $this->headerdata;
        $data->scroll = $this->scrolldata;
        $data->footer = $this->footerdata;
        $data = $this->checkupdate($data);

        return $data;
    }


    /*
     * this will check whether there has been an upload
     * */
    private function checkupdate($data){

        $update = $this->getSavedVariable('gallery_update');

        if($this->menuid == '29292292'){
            $this->rewriteActionConfigField('poll_interval',1);
        } elseif($update == 1){
            $this->rewriteActionConfigField('poll_interval',1);
        } elseif($update > 1){
            $menu = new StdClass();
            $menu->action = 'list-branches';
            $menu2 = new StdClass();
            $menu2->action = 'submit-form-content';
            $data->onload = array($menu,$menu2);
            $this->rewriteActionConfigField('poll_interval','');
            $this->saveVariable('gallery_update',0);
        }

        return $data;
    }

    public function tab2(){
        $data = new StdClass();
        $this->showGallery(true);

        $data->header = $this->headerdata;
        $data->scroll = $this->scrolldata;
        $data->footer = $this->footerdata;
        $data = $this->checkupdate($data);


        return $data;
    }

    public function tab3(){

        $update = $this->getSavedVariable('gallery_update');

        if($update == 1){
            $params1['style'] = 'gallery_notice';
            $params1['onclick'] = new StdClass();
            $params1['onclick']->action = 'submit-form-content';
            $this->scrolldata[] = $this->getText('Please wait for the upload to continue before adding another picture', $params1);
        }elseif($this->menuid == '2933'){
            $this->step2();
        } else {
            $this->step1();
        }

        $data = new StdClass();
        $data->header = $this->headerdata;
        $data->scroll = $this->scrolldata;
        $data->footer = $this->footerdata;
        $data = $this->checkupdate($data);

        return $data;
    }


    /* this is the main controller part */
    private function setMode($submit=false){

        if($this->mode == 'entry'){
            $this->module = 'Entry';
            $obj = new mgEntryModel($this);
            $obj->mainobj = $this;
            $data = $obj->getData();

            $this->scrolldata = $data->scroll;
            $this->footerdata = $data->footer;
            return true;
        }

/*        switch($this->menuid){
            case '1001':
                $this->step1();
                return true;
                break;

            case '2933':
                $this->step2();
                return true;
                break;

            case '29292292':
                $this->showGallery();
                return true;
                break;

            default:
                $this->showGallery();
                return true;
                break;
        }*/
    }


    public function topbar($id=false,$add=true){

        if($id){
            $params['left'] = array('image' => 'topbaricon-back.png','id' => 29292292,'action' => 'open-action','config' => $id);
        } else {
            $params['left'] = array('image' => 'topbaricon-back.png', 'id' => 29292292, 'action' => 'submit-form');
        }

        if($add) {
            $params['right'] = array('image' => 'topbaricon-plus.png', 'id' => '1001', 'action' => 'submit-form');
        }

        return $this->getCustomTopBar('Gallery',$params);
    }


    public function showGallery($bookmarksonly=false){
        //$this->headerdata[] = $this->topbar($this->getConfigParam('mainmenu_id'));
        $images = $this->getImages($bookmarksonly);

        if(empty($images)){
            $output[] = $this->getText( 'No pictures at the moment. Check back soon or submit your own!', array( 'style' => 'recipe_chat_nocomments' ) );
        }

        $exclude = false;

        //$output[] = $this->getText(serialize($this->varcontent));

        if(!$bookmarksonly) {
            if (isset($this->varcontent['gallery_update'])) {
                if ($this->varcontent['gallery_update'] == 1) {
                    $params1['style'] = 'gallery_notice';
                    $params1['onclick'] = new StdClass();
                    $params1['onclick']->action = 'submit-form-content';
                    $output[] = $this->getText('Uploading your image...', $params1);
                    $exclude = 1;
                } elseif ($this->varcontent['gallery_update'] > 1) {
                    $exclude = $this->varcontent['gallery_update'];
                }
            }
        }

        $gallery_items = $this->moduleGallery( array(
            'images'    => json_encode($images),
            'viewer_id' => 66,
            'dir'       => 'images',
            'debug'     => false,
            'open_action' => true,
            'grid_spacing' => true,
            'open_in_popup' => false,
            'exclude' => $exclude
        ) );

        foreach ($gallery_items as $item) {
            $output[] = $item;
        }

        if(!isset($output)){
            if($bookmarksonly){
                $output[] = $this->getText( "This is a place for your bookmarked inspiration items. You haven't bookmarked anything yet. Go ahead, show some love to the images you find from the gallery!", array( 'style' => 'gallery_html' ) );
            } else {
                $output[] = $this->getText( 'No pictures at the moment. Check back soon or submit your own!', array( 'style' => 'gallery_html' ) );
            }
        }

        $this->scrolldata = $output;

        return true;

    }

    public function step3(){
        $obj = new StdClass;
        $obj->type = 'msg-html';
        $obj->content = 'To recipe it is';
        $obj->style = 'gallery_html';
        $output[] = $obj;
        return $output;
    }

    public function step4(){
        $obj = new StdClass;
        $obj->type = 'msg-html';
        $obj->content = 'Just the keywords';
        $obj->style = 'gallery_html';
        $output[] = $obj;
        return $output;
    }


    public function step2(){
        /* saving variables */

        $subject = $this->getSubmittedVariableByName('submit_subject_temp');
        $comment = $this->getSubmittedVariableByName('submit_comment_temp');

        if ($subject) {
            $vars = array(
                'submit_comment_temp' => $comment,
                'submit_subject_temp' => $subject,
                'gallery_update' => 1
            );

            AeplayVariable::saveVariablesArray($vars, $this->playid, $this->gid, 'normal');
            $gallery = $this->requireConfigParam('gallery_id');
            $params = "$this->actionid,$this->playid,$this->gid,$this->userid,$gallery";
            Aetask::registerTask($this->playid, 'mobilegallery:savevars', $params, 'refreshaction');
            AeplayVariable::updateWithName($this->playid,'gallery_update',1,$this->gid);
        }

        $output[] = $this->getText('Thank you for your submission! It might take a little while for it to show up as we are uploading it on the background.',array('style' => 'gallery_notice'));

        $options['id'] = '29292292';
        $options['action'] = 'open-tab';
        $options['config'] = 1;
        $options['sync_open'] = 1;
        $footer[] = $this->getTextbutton('OK!',$options);

        $this->scrolldata = $output;
        $this->footerdata = $footer;

        return $output;
    }

    public function step1(){
        //$this->headerdata[] = $this->topbar();

        $var = $this->getVariableId('submit_pic_temp');

        $imgparams['variable'] = $var;
        $imgparams['margin'] = '4 4 4 4';
        $imgparams['crop'] = 'yes';
        $imgparams['width'] = $this->screen_width - 8;
        $imgparams['height'] = round($this->screen_width / 1.5,0);

        $output[] = $this->getImage('filmstrip.png',$imgparams);

        $params['variable'] = $var;
        $params['margin'] = '-50 0 15 0';
        $params['action'] = 'upload-image';
        $params['max_dimensions'] = 900;
        $params['sync_upload'] = true;
        $params['allow_delete'] = true;
        $params['type'] = 'image';
        $params['iamage'] = 'add-photo-button-icon.png';

        $output[] = $this->getImagebutton( 'add-photo-button-icon.png','969698',false,$params );

        /*        $output[] = $this->getImagebutton( 'photobutton.png','969698',false,
                    array( 'width' => '13%','margin'=>'0 5 0 7', 'action' => 'upload-image',
                        'sync_upload' => true, 'allow_delete' => true,
                        'variable' => $this->factoryobj->vars['chat_upload_temp']  ) );*/

        $obj = new StdClass;
        $obj->type = 'field-text';
        $obj->variable = $this->getVariableId('submit_subject_temp');
        $obj->hint = 'Subject (required)';
        $obj->style = 'register_field';
        $output[] = $obj;

        $obj = new StdClass;
        $obj->type = 'field-textview';
        $obj->variable = $this->getVariableId('submit_comment_temp');
        $obj->hint = 'Comment (required)';
        $obj->style = 'register_field';
        $output[] = $obj;

        /*        $output[] = $this->getImagebutton('save-button.png',2933,'',array('sync_upload' => '1'));*/

        $obj = new StdClass;
        $obj->type = 'msg-html';
        $obj->content = $this->configobj->msg;
        $obj->style = 'gallery_html';
        $output[] = $obj;

        $options['id'] = '2933';
        $options['sync_upload'] = 0;
        $options['action_config'] = '2933';
        $footer[] = $this->getTextbutton('Upload',$options);

        $this->scrolldata = $output;
        $this->footerdata = $footer;

        return true;
    }


    private function getImages($bookmarksonly=false){
        if(
            isset($this->configobj->source_branch) AND
            isset($this->configobj->gallery_item_id)
        ) {

            $source = $this->configobj->source_branch;

            /* bookmarks are saved with playaction id's, not action_id's,
            so they need to be converted here. Not the most efficient with a
            large number of bookmarks admittedly ... */

            if($bookmarksonly){
                $bookmarks = @json_decode($this->getVariable('bookmarks'));

                if(is_object($bookmarks)){
                    $bookmarks = (array)$bookmarks;
                    foreach($bookmarks as $key=>$value){
                        $playaction = AeplayAction::model()->findByAttributes(array('id' => $key,'play_id' => $this->playid));
                        if(isset($playaction->action_id)){
                            $newarr[$playaction->action_id] = true;
                        }
                    }
                }
            }

            $actions = Aeaction::model()->findAllByAttributes(array('branch_id' => $source));

            foreach ($actions as $action) {

                if(isset($newarr[$action->id]) OR $bookmarksonly == false) {

                    $config = json_decode($action['config']);

                    if (isset($config->comment) AND $config->comment) {
                        $item['comment'] = $config->comment;
                    } else {
                        $item['comment'] = $config->subject;
                    }

                    $item['user'] = $this->getConfigParam('user');

                    if (isset($config->image_portrait)) {
                        $item['pic'] = $config->image_portrait;
                    }

                    if (isset($config->chat_content)) {
                        $item['chat_content'] = $config->chat_content;
                    } else {
                        $item['chat_content'] = '';
                    }

                    $item['action_id'] = $action->id;

                    if (isset($config->date)) {
                        $item['date'] = $config->date;
                    } else {
                        $item['date'] = date('r');
                    }

                    if (isset($config->likes)) {
                        $item['like_count'] = $config->likes;
                    } else {
                        $item['like_count'] = 2;
                    }

                    if ($item['pic']) {
                        $imagesjson[] = $item;
                    }

                    unset($item);
                }
            }
        }

        if(isset($imagesjson)){
            return array_reverse($imagesjson);
        } else {
            return array();
        }

    }

}