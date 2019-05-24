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

Yii::import('application.modules.aelogic.packages.actionMobilegallery.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobilegallery.models.*');

class mgSubmitController extends MobilegalleryController {

    public $mainobj;
    public $footer;

    public function getData($submit){

        $varname = 'submit_comment_temp';

        /* case where user has submitted the first step */
        if(isset($this->mainobj->submitvariables[$varname]) AND $this->mainobj->submitvariables[$varname]){
            return $output = $this->step2($submit);
        }

        $this->tabs = $this->mainobj->tabs;

        if(!isset($this->mainobj->submit['menuid'])){
            $output = $this->step1();
        } elseif($this->mainobj->submit['menuid'] == 2933 ) {
            $output = $this->step2($submit);
        } else {
            $menuid = $this->mainobj->submit['menuid'];


            if(isset($this->mainobj->tabs[$menuid])){
                $pointer = $this->mainobj->tabs[$menuid];

                switch($pointer){
                    case 'save_gallery_button':
                        $output = $this->step2($submit);
                        break;
                    case 'mobileprofile_tab2':
                        $output = $this->step1();
                        break;
                    case 'yes_lets_doit':
                        $output = $this->step3();
                        break;
                    case 'not_this_time':
                        $output = $this->step4();
                        break;
                    default:
                        $output = $this->step1();
                        break;
                }
            } else {
                $output = $this->step1();
            }
        }

        $data = new StdClass();
        $data->scroll = $output;
        if(is_array($this->footer)){ $data->footer = $this->footer; }
        return $data;
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


    public function step2($submit){

        /* saving variables */
        $pointer = $this->mainobj->vars['submit_comment_temp'];
        $pointer2 = $this->mainobj->vars['submit_subject_temp'];
        $vars = array('submit_comment_temp' => $this->mainobj->submitvariables[$pointer], 'submit_subject_temp' => $this->mainobj->submitvariables[$pointer2]);
        AeplayVariable::saveVariablesArray($vars,$this->mainobj->playid,$this->mainobj->gid,'normal');

        /* handle file upload and finalise creation of the action */
/*      $call = 'apps/galleryuploadAsync?actionid=' .$this->mainobj->actionid .'&playid=' .$this->mainobj->playid .'&user=' .$this->mainobj->userid;
        Controller::asyncAppzioApiCall($call,$this->mainobj->gid);*/

        $pic = $this->saveVars($this->mainobj->actionid,$this->mainobj->playid,$this->mainobj->gid,$this->mainobj->userid);
        sleep(2);
        $output[] = $this->mainobj->getImage($pic);

        $obj = new StdClass;
        $obj->type = 'msg-html';
        $obj->content = 'Thank you for your submission!';
        $obj->style = 'gallery_html';
        $output[] = $obj;

        /* $output[] = $this->mainobj->getMenu('turn_to_recipe',array('style'=>'gallery_menu'));*/

/*        $output[] = $this->getImagebutton(
            'save-button.png',29292292,false,
            array('action' => 'open-action','config' => $this->getConfigParam('mainmenu_id'),'sync_open' => 1));*/

        $options['id'] = '29292292';
        $footer[] = $this->mainobj->getFooterButton('                   To Menu',$options);
        $this->footer = $footer;

        $this->scroll = $output;
        $this->footerdata = $footer;

        $this->mainobj->flushCacheAction($this->mainobj->action_id);
        $this->mainobj->flushCacheAction($this->getConfigParam('gallery_id'));
        $this->mainobj->flushCacheAction($this->getConfigParam('mainmenu_id'));

        return $output;
    }

    public function step1(){
        // print_r($this->)

        $output[] = $this->topbar();

        $this->vars = $this->mainobj->vars;
        $var = $this->mainobj->getVariableId('submit_pic_temp');
        $output[] = $this->mainobj->getImage('filmstrip.png',array( 'style' => 'chat-divider','variable' => $var));

        $params['variable'] = $var;
        $params['margin'] = '-50 0 15 0';
        $params['action'] = 'upload-image';
        $params['max_dimensions'] = '900';
        $params['sync_upload'] = true;
        $params['allow_delete'] = true;
        $params['type'] = 'image';
        $params['iamage'] = 'add-photo-button-icon.png';

        $output[] = $this->mainobj->getImagebutton( 'add-photo-button-icon.png','969698',false,$params );

/*        $output[] = $this->getImagebutton( 'photobutton.png','969698',false,
            array( 'width' => '13%','margin'=>'0 5 0 7', 'action' => 'upload-image',
                'sync_upload' => true, 'allow_delete' => true,
                'variable' => $this->factoryobj->vars['chat_upload_temp']  ) );*/

        $obj = new StdClass;
        $obj->type = 'field-text';
        $obj->variable = $this->mainobj->getVariableId('submit_subject_temp');
        $obj->hint = 'Subject';
        $obj->style = 'register_field';
        $output[] = $obj;

        $obj = new StdClass;
        $obj->type = 'field-textview';
        $obj->variable = $this->mainobj->getVariableId('submit_comment_temp');
        $obj->hint = 'Comment';
        $obj->style = 'register_field';
        $output[] = $obj;

/*        $output[] = $this->getImagebutton('save-button.png',2933,'',array('sync_upload' => '1'));*/

        $obj = new StdClass;
        $obj->type = 'msg-html';
        $obj->content = $this->mainobj->configobj->msg;
        $obj->style = 'gallery_html';
        $output[] = $obj;

        $options['id'] = '2933';
        $options['sync_upload'] = 1;
        $footer[] = $this->mainobj->getFooterButton('                     Save',$options);

        $this->footer = $footer;


        return $output;
    }


    public static function saveVars($actionid,$playid,$gid,$user)
    {

        $varvalue = false;
        $count = 0;
        $pic = false;

        while ($pic == false AND $count < 10) {
            $vars = AeplayVariable::model()->with('variable')->findAllByAttributes(array('play_id' => $playid));

            foreach ($vars as $var) {
                $name = $var->variable->name;
                $value = $var->value;
                $allvars[$name] = $value;
            }

            if (isset($allvars) AND isset($allvars['submit_pic_temp']) AND $allvars['submit_pic_temp']) {
                $pic = $allvars['submit_pic_temp'];
               // $comment = $allvars['submit_comment_temp'];
            } elseif (isset($allvars) AND isset($allvars['submit_comment_temp']) AND $allvars['submit_comment_temp']) {
              //  $comment = $allvars['submit_comment_temp'];
            } else {
                sleep(2);
            }

            $count++;
        }


        $playaction = AeplayAction::model()->findByPk($actionid);
        $action = Aeaction::model()->findByPk($playaction->action_id);
        $config = json_decode($action->config);

        $sourcebranch = $config->source_branch;
        $type = Aeactiontypes::model()->findByAttributes(array('shortname' => 'mobilegallery'));

        $count = Aeaction::model()->countByAttributes(array('branch_id' => $sourcebranch));
        $count++;


        $uservars = AeplayVariable::getArrayOfPlayvariables($playid);
        $trigger = Aetrigger::model()->findByAttributes(array('shortname'=>'branchactive'));

        if(isset($uservars['submit_subject_temp']) AND $uservars['submit_subject_temp'] AND $pic) {

            $source = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/documents/games/' .$gid .'/user_original_images/';
            $target = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/documents/games/' .$gid .'/original_images/';
            copy($source .basename($pic), $target .basename($pic));

            $comment = isset($uservars['submit_comment_temp']) ? $uservars['submit_comment_temp']: '';
            $params['width'] = 640;
            $params['height'] = 640;

            $img = new ImagesController('submitcontroller');
            $img->gid = $gid;
            $imagefile = $img->getAsset(basename($pic));

            AeplayVariable::updateWithName($playid,'featured_image',$pic,$gid);

            $action = new Aeaction();
            $action->branch_id = $sourcebranch;
            $action->points = 0;
            $action->type_id = $type->id;
            $action->active = 1;
            $action->name = $uservars['submit_subject_temp'];
            $action->order = $count;
            $action->trigger_id = $trigger->id;

            $conf = new StdClass();
            $conf->subject = $uservars['submit_subject_temp'];
            $conf->msg = $comment;
            $conf->comment = $comment;
            $conf->mode = 'entry';
            $conf->image_portrait = basename($pic);
            $conf->image_menu = basename($pic);
            $conf->backarrow = 1;
            $conf->dynamic = 1;
            $conf->source_branch = $sourcebranch;
            $conf->date = date("r");
            $conf->likes = 1;
            $conf->chat_content = '';

            $conf->user = $user;
            $action->config = json_encode($conf);
            $action->insert();

            AeplayVariable::deleteWithName($playid,'submit_comment_temp',$gid);
            AeplayVariable::deleteWithName($playid,'submit_pic_temp',$gid);
            Appcaching::removeActionCache($playid,$actionid);

        }

        return $pic;

    }

}