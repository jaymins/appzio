<?php

Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aechat.models.*');

class ArticleChatFooter extends ArticleComponent
{

    // Local Vars
    public $submitvariables;
    public $configobj;
    public $imagesobj;
    public $vars;
    public $varcontent;

    private $chatid;
    private $chat_info;
    private $disable_chat;
    private $other_user_play_id;
    private $hide_pic_button;

    public function template(){
        
        $this->chatid = $this->addParam('chatid',$this->options,false);
        $this->chat_info = $this->addParam('chat_info',$this->options,false);
        $this->disable_chat = $this->addParam('disable_chat',$this->options,false);
        $this->other_user_play_id = $this->addParam('other_user_play_id',$this->options,false);
        $this->hide_pic_button = $this->addParam('hide_pic_button',$this->options,false);

        if(isset($this->chat_info->blocked) AND $this->chat_info->blocked == 1){
            $output = array();
            $output[] = $this->factoryobj->getText('{#this_chat_has_ended#}',array('style' => 'chat-msg-text-centered'));
            $output[] = $this->factoryobj->getSpacer(10);
            return $output;
        }

        if($this->disable_chat === true){
            $output = array();
            $output[] = $this->factoryobj->getText('{#sorry_message_limit_reached#}',array('style' => 'chat-msg-text-centered'));
            return $output;
        }

        $output = array();

        $name = $this->getUsername();

        if ( empty($name) ) {
            $output[] = $this->factoryobj->getText( '{#please_finish_up_your_registration#}', array(
                'style' => 'chat-error'
            ));
            return $output;
        }

        $output[] = $this->factoryobj->getImage('desee-chat-line.png', array(
            'width' => '100%'
        ));

        $columns[] = $this->getPhotoUploadButton();
        $columns[] = $this->factoryobj->getVerticalSpacer('5');

        $columns[] = $this->factoryobj->getColumn(array(
            $this->factoryobj->getFieldtextarea( '', array(
                'submit_menu_id' => 'submit-msg',
                'hint' => '{#type_to_send#} ...',
                'variable' => '66666660',
                'value' => '',
                'activation' => 'keep-open',
                'background-color' => '#ffffff',
                'font-size' => '13',
                'font-style' => 'italic',
                'color' => '#474747',
                'padding' => '10 4 10 4',
                'vertical-align' => 'middle',
            )),
        ), array(
            'width' => '70%',
            'vertical-align' => 'middle'
        ));

        $columns[] = $this->factoryobj->getImage('desee-invisible-divider.png', array(
            'width' => '25',
            'max-height' => '25',
            'margin' => '0 0 0 5',
            'variable' => $this->factoryobj->getVariableId('chat_upload_temp')
        ));

        $columns[] = $this->getSubmitButton();

        $output[] = $this->factoryobj->getRow($columns, array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'vertical-align' => 'middle',
        ));

        return $output;
    }

    private function getPhotoUploadButton(){

        $onclick = new stdClass();
        $onclick->action = 'upload-image';
        $onclick->sync_upload = 1;
        $onclick->viewport = 'bottom';
        $onclick->max_dimensions = '600';
        $onclick->allow_delete = true;
        $onclick->variable = $this->factoryobj->getVariableId('chat_upload_temp');


        if($this->factoryobj->getConfigParam('actionimage5')){
            return $this->getBtn( $this->factoryobj->getConfigParam('actionimage5'), $onclick);
        } else {
            return $this->getBtn( 'desee-icon-send-image.png', $onclick);
        }

    }

    private function getSubmitButton(){
        $onclick = new stdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'submit-msg';
        $onclick->sync_upload = 1;
        $onclick->viewport = 'bottom';

        if($this->factoryobj->getConfigParam('actionimage4')){
            $btn = $this->factoryobj->getConfigParam('actionimage4');
            $button_obj = $this->getBtn($btn,$onclick);

        } else {
            $btn = 'desee-icon-send-msg.png';
            $button_obj = $this->getBtn($btn,$onclick);
        }

        return $this->factoryobj->getColumn(array(
            $button_obj
        ), array(
            'floating' => 1,
            'float' => 'right',
            'vertical-align' => 'middle',
        ));
    }

    private function getBtn($icon, $onclick){
        return $this->factoryobj->getImage($icon, array(
            'onclick' => $onclick,
            'width' => '25',
            'height' => '25',
        ));
    }

    private function getUsername() {

        $options = array(
            'real_name', 'name', 'screen_name', 'surname'
        );

        foreach ($options as $option) {
            if ( isset($this->varcontent[$option]) AND !empty($this->varcontent[$option]) ) {
                return $this->varcontent[$option];
            }
        }

        return false;
    }

}