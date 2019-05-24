<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMmessaging\themes\uikit\Controllers;

use packages\actionMmessaging\themes\uikit\Views\Chat as ArticleView;
use packages\actionMmessaging\themes\uikit\Models\Model as ArticleModel;

class Chat extends \packages\actionMmessaging\Controllers\Chat {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public $current_chat_id;
    public $chat_content = [];
    public $chat_context = 'action';

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){
        $data = [];

        $this->model->rewriteActionField( 'keep_scroll_in_bottom', 1 );
        $this->model->rewriteActionField( 'poll_update_view', 'all' );
        $this->model->rewriteActionConfigField( 'poll_interval', '1' );
        
        if ( preg_match('~chat~', $this->getMenuId()) ) {
            $this->model->initChat($this->chat_context, $this->getMenuId(), $this->model->getBuddyID());
        }

        if ( $buddy_id = $this->model->sessionGet('current_buddy_id') ) {
            $userData = $this->model->foreignVariablesGet($buddy_id);

            if(isset($userData['username'])){
                $data['name'] = $userData['username'];
            } elseif(isset($userData['nickname'])){
                $data['name'] = $userData['nickname'];
            } else {
                $data['name'] = '{#anonymous#}';
            }
        }

        $this->chat_content = $this->chatContent();

        $data['chat_content'] = $this->chat_content;

        return ['Chat', $data];
    }

    public function actionSaveMessage() {
        $this->model->saveChatMessage();
        return $this->actionDefault();
    }

    private function chatContent() {

        $messages = $this->model->populateChatContent();

        if ( empty($messages) ) {
            return false;
        }

        foreach ($messages as $i => $message) {
            $messages[$i]['name'] =  $this->setUserName( $message );
            $messages[$i]['user_is_owner'] =  $this->userIsOwner( $message );
            $messages[$i]['msg_is_read'] =  $this->model->readChecker( $message, 'msg_is_read' );
            $messages[$i]['msg_read_time'] =  $this->model->readChecker( $message, 'msg_read_time' );
        }

        return $messages;
    }

    private function setUserName( $message ) {

        $name_mode = $this->model->getConfigParam('name_mode');

        if ( $name_mode == 'default' ) {
            return $message['name'];
        }

        switch($name_mode){
            case 'invisible';
            case 'hidden';
                return '';
                break;

            case 'firstname';
            case 'first_name';
                return $this->getChatName( $message['name'] );
                break;

            case 'last_name';
                return $this->getChatName( $message['name'], 'last' );
                break;

            default:
                return $message['name'];
                break;
        }

    }

    public function getChatName( $name, $type = 'first' ){

        if ( empty($name) ) {
            return '{#anonymous#}';
        }

        if ( !strstr($name, ' ') ) {
            return $name;
        }

        $name_pieces = explode(' ', trim($name));

        if ( $type == 'first' ) {
            return $name_pieces[0];
        } else if ( $type == 'last' AND isset($name_pieces[1]) ) {
            return $name_pieces[1];
        }

    }

    private function userIsOwner( $message ) {

        if ( is_array($message) ) {
            $message = (object) $message;
        }

        if ( $message->user == $this->playid ) {
            return true;
        }

        return false;
    }

}