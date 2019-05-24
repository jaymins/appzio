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
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class MobileregisterController extends ArticleController {

    public $footer;
    public function tab1(){
        $data = new StdClass();


        if(isset($this->varcontent['real_name']) AND $this->varcontent['real_name'] AND strlen($this->varcontent['real_name']) > 3){
            $data->scroll = $this->finaliseRegistration(array());
        } elseif($this->menuid == '6060'){
            $data->scroll = $this->getMainScroll(array(),true);
        } else {
            $data->scroll = $this->getMainScroll(array(),false);
        }

        if($this->footer){
            $data->footer = $this->footer;
        }

        return $data;
    }

    private function finaliseRegistration($output){

        $this->loadVariables();

        if(isset($this->submitvariables[66666661])){
            /* save the data from previous form */
            $name = $this->submitvariables[66666661];
            $email = $this->submitvariables[66666662];
            $phone = $this->submitvariables[66666663];
            $arr = array('name' => $name,'email' => $email,'phone' => $phone);
            AeplayVariable::saveVariablesArray($arr,$this->playid,$this->gid,'normal');
        }

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
            $txt = 'Click to change the photo';

            if(isset($this->varcontent['real_name'])){
                AeplayVariable::updateWithId($this->playid,$this->vars['name'],$this->varcontent['real_name']);
            }

        } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
            $txt = 'Click to change the photo';
        } else {
            $pic = 'photo-placeholder.jpg';
            $txt = 'Add a photo';
        }

        $output[] = $this->getImage($pic,array( 'style' => 'chat-divider','variable' => $this->vars['profilepic']));

        $btn['style'] = 'register_button_textbutton';
        $btn['onclick'] = new StdClass();
        $btn['onclick']->action = 'upload-image';
        $btn['onclick']->max_dimensions = '900';
        $btn['onclick']->allow_delete = 1;
        $btn['onclick']->variable = $this->vars['profilepic'];
        $btn['onclick']->sync_upload = 1;
        $btn['onclick']->type = 'image';

        $output[] = $this->getText('Finish your profile setup',array('style' => 'register_title'));
        $output[] = $this->getText('Add a photo',$btn);

        $obj = new StdClass;
        $obj->type = 'field-textview';
        $obj->variable = $this->vars['comment_temp'];
        $obj->hint = 'Comment (optional):';
        $obj->style = 'register_field';
        $output[] = $obj;

        $this->footer[] = $this->getTextbutton('Finish Registration',array('id' => '7070','action' => 'complete-action'));

        return $output;
    }

    private function getFieldReg($output,$id,$hint){
        $obj = new StdClass;
        $obj->type = 'field-text';
        $obj->variable = $id;

        $obj->content = $this->getVariable($id);

        $obj->hint = $hint;
        $obj->style = 'register_field';
        $output[] = $obj;
        return $output;
    }

    private function getErrorField($output,$id,$msg){
        $obj = new StdClass;
        $obj->type = 'field-text';
        $obj->variable = $id;
        $obj->content = $this->getVariable($id);
        $obj->style = 'register_field_error';
        $obj->hint = '';
        $output[] = $obj;

        $obj = new StdClass;
        $obj->type = 'msg-plain';
        $obj->content = $msg;
        $obj->style = 'register_field_error_text';
        $output[] = $obj;
        return $output;
    }

    private function getMainScroll($output,$validate=false){

        $output[] = $this->getMenu('create_profile_why',array('style'=>'register_menu','sync_close' => 1));
        $output[] = $this->getMenu('mobilereg_connect_facebook',array('style'=>'register_menu'));
        $output[] = $this->getImage('connect-or.png', array( 'style' => 'chat-divider'));

        if($validate == true){
            $valid = true;
            $out2 = $output;

            if(str_word_count($this->submitvariables['66666661']) > 1){
                $out2 = $this->getFieldReg($out2,'66666661','Name');
            } else {
                $out2 = $this->getErrorField($out2,'66666661','Please input first name and last name');
                $valid = false;
            }

            $validator = new CEmailValidator;
            $validator->checkMX = true;
            if(!$validator->validateValue($this->submitvariables['66666662'])){
                $out2 = $this->getErrorField($out2,'66666662','Please input a proper email');
                $valid = false;
            } else {
                $out2 = $this->getFieldReg($out2,'66666662','Email');
            }

            if(!strstr($this->submitvariables['66666663'],'+')){
                $out2 = $this->getErrorField($out2,'66666663','Include the country code with +');
                $valid = false;
            } else {
                $out2 = $this->getFieldReg($out2,'66666663','Phone');
            }

            if($valid == false){
                $output = $output+$out2;
            } else {
                return $this->finaliseRegistration(array());
            }

        } else {
            /* default state is here */
            $output = $this->getFieldReg($output,'66666661','Full name');
            $output = $this->getFieldReg($output,'66666662','Email');
            $output = $this->getFieldReg($output,'66666663','Phone (+359123456789)');
        }


        $this->footer[] = $this->getTextbutton('Register',array('id' => '6060'));
        return $output;
    }

}