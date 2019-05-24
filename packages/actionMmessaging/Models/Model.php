<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMmessaging\Models;

use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {

    public $storage;
    public $query_key;
    public $chatobj;

    public function setChatObj() {
        $this->chatobj = new \Aechat();
        $this->chatobj->play_id = $this->playid;
        $this->chatobj->gid = $this->appid;
        $this->chatobj->game_id = $this->appid;
    }

    public function getMatches() {

        $match_ids = $this->getMatchIDs();

        if ( empty($match_ids) ) {
            return false;
        }

        $items = [];

        foreach ($match_ids as $match_id) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($match_id);

            if ( empty($vars) ) {
                continue;
            }

            $vars['chat_data'] = $this->getChatData( $match_id );
            $vars['user_play_id'] = $match_id;

            $items[] = $vars;
        }

        return $items;
    }

    public function getMatchIDs() {

        $this->storage = new \AeplayKeyvaluestorage();

        $items = $this->storage->findAllByAttributes([
            'play_id' => $this->playid,
            'key' => $this->query_key,
        ]);

        if ( empty($items) ) {
            return false;
        }

        $item_ids = [];

        foreach ($items as $item) {
            $item_ids[] = $item->value;
        }

        return $item_ids;
    }

    public function getChatData( $match_id ) {
        $contextkey = $this->getTwoWayChatId($this->playid, $match_id);

        $chat_user = \Aechatusers::model()->findByAttributes(array(
            'context_key' => $contextkey,
            'chat_user_play_id' => $this->playid
        ));

        if ( empty($chat_user) OR !isset($chat_user->chat_id) ) {
            return false;
        }

        $chat_id = $chat_user->chat_id;
        $time = '';

        $message = \Aechatmessages::model()->findByAttributes([
            'chat_id' => $chat_id,
            'author_play_id' => $match_id,
        ], [
            'order'=>'id DESC'
        ]);

        if ( empty($message) ) {
            return [
                'context_key' => $contextkey
            ];
        }

        if ( $message->chat_message_timestamp ) {
            $timestamp = strtotime($message->chat_message_timestamp);
            $time = \Controller::humanTiming($timestamp);
        }

        return [
            'message' => $message->chat_message_text,
            'is_message_read' => $message->chat_message_is_read,
            'timestamp' => $message->chat_message_timestamp,
            'timestamp_readable' => $time,
            'context_key' => $contextkey,
        ];
    }

    public function initChat( $context, $context_key, $otheruserid = false, $chat_id = 0 ) {

        if ( isset($this->chatobj->current_chat_id) AND $this->chatobj->current_chat_id ) {
            return true;
        }

        $this->setChatObj();

        $this->chatobj->context = $context;
        $this->chatobj->context_key = $context_key;
        $this->chatobj->chat_other_user_play_id = $otheruserid;
        $this->chatobj->uservars = $this->varcontent;

        // If Chat ID is provided, users would be able to only preview the chat
        if ( $chat_id ) {
            $this->chatobj->current_chat_id = $chat_id;
        } else {

            $this->chatobj->initChat();

            // We shore the current chat ID in the session
            // so it could be used for future operations
            $this->sessionSet('current_chat_id', $this->chatobj->current_chat_id);
        }

        $cache_name = $this->playid . '-' . $this->userid .'-chattemp2';

        // "chat_id" is used in the ArticleFactory for checking the md5 hash of the current chat
        \Appcaching::setGlobalCache($cache_name, [
            'chat_id' => $this->chatobj->current_chat_id
        ]);

        return true;
    }

    public function populateChatContent() {

        $current_chat_id = $this->sessionGet('current_chat_id');

        if ( empty($current_chat_id) ) {
            return false;
        }

        $chatobj = new \Aechat();
        $chatobj->current_chat_id = $current_chat_id;

        return $chatobj->getChatContent();
    }

    public function getBuddyID() {

        $user_ids = explode('-chat-',$this->getMenuId());

        if ( count($user_ids) < 2 ) {
            return false;
        }

        $buddy_id = false;

        foreach ($user_ids as $user_id) {

            if ( $user_id != $this->playid AND is_numeric($user_id) ) {
                $buddy_id = $user_id;
            }

        }

        $this->sessionSet('current_buddy_id', $buddy_id);

        return $buddy_id;
    }

    public function stripUrls($msg){
        $msg = preg_replace('|https?://www\.[a-z\.0-9]+|i', '{#url_removed#}', $msg);
        $msg = preg_replace('|http?://www\.[a-z\.0-9]+|i', '{#url_removed#}', $msg);
        $msg = preg_replace('|http?://[a-z\.0-9]+|i', '{#url_removed#}', $msg);
        $msg = preg_replace('|https?://[a-z\.0-9]+|i', '{#url_removed#}', $msg);
        $msg = preg_replace('|www\.[a-z\.0-9]+|i', '{#url_removed#}', $msg);
        return $msg;
    }

    public function getUsername() {

        $options = array(
            'real_name', 'name', 'screen_name', 'surname'
        );

        foreach ($options as $option) {
            if ( $name = $this->getSavedVariable($option) ) {
                return $name;
            }
        }

        return false;
    }

    /*
     * Save a chat message
     * Note: we support saving of blank messages
     * as an attachment must be associated with a message
     */
    public function saveChatMessage() {

        $current_chat_id = $this->sessionGet('current_chat_id');

        if ( empty($current_chat_id) ) {
            return false;
        }

        $use_server_time = false;
        if ( $this->getConfigParam('use_server_time') ) {
            $use_server_time = true;
        }

        $current_time = ( $use_server_time ? time() : \Helper::getCurrentTime() );
        $time = date('Y-m-d H:i:s', $current_time);

        $message_text = $this->getSubmittedVariableByName('tmp-chat-message');
        $attachment = $this->getSavedVariable('chat_upload_temp');

        // Nothing to save
        if ( empty($message_text) AND empty($attachment) ) {
            return false;
        }

        $chatobj = new \Aechat();
        $chatobj->play_id = $this->playid;
        $chatobj->current_chat_id = $current_chat_id;

        $message_id = $chatobj->addMessage( $message_text, $time );

        if ( $attachment ) {
            // $new['attachment'] = $var['chat_upload_temp'];
            $chatobj->addAttachment( $message_id, $attachment );
            \AeplayVariable::deleteWithName($this->playid,'chat_upload_temp', $this->appid);
        }

        return $message_id;
    }

    /*
     * Mark a message as read and return the new message data
     * Supported field types: msg_is_read | msg_read_time
     */
    public function readChecker( $message, $field ) {

        if ( !isset($message[$field]) ) {
            return false;
        }

        if ( !isset($_REQUEST['poll']) OR ($message['user'] == $this->playid) ) {
            return $message[$field];
        }

        if ( !isset($message['msg_is_read']) OR $message['msg_is_read'] == 1 ) {
            return $message[$field];
        }

        $chatobj = new \Aechat();
        $message = $chatobj->updateMessageStatus( $message['id'] );

        if ( $field == 'msg_is_read' ) {
            return $message->chat_message_is_read;
        } else if ( $field == 'msg_read_time' ) {
            return $message->chat_message_timestamp;
        }

        return $message[$field];
    }

    /*
     * this is a special function, which will check whether
     * the action should be updated or if the cache is still valid.
     * this will make any refresh calls faster, as we don't have to do the
     * expensive initialisation
    */
    public static function checksumChecker($args){
        
        if ( !isset($args['checksum']) ) {
            return false;
        }

        $md5 = self::checkQuery($args['chat_id']);

        if($md5 == $args['checksum']){
            return true;
        }

        return false;
    }

    public static function SetChecksumChecker($playid,$userid){
        $name = $playid . '-' . $userid .'-chattemp2';
        $cache = \Appcaching::getGlobalCache($name);

        if ( !isset($cache['chat_id']) ) {
            return false;
        }

        $out['includepath'] = 'application.modules.aelogic.packages.actionMmessaging.models.Model';
        $out['class'] = 'Model';
        $out['method'] = 'checksumChecker';
        $out['chat_id'] = $cache['chat_id'];
        $out['checksum'] = self::checkQuery($cache['chat_id']);

        return $out;
    }

    public static function checkQuery($id){
        $sql = "SELECT message.*, attachment.chat_attachment_path
                FROM ae_chat_messages as message
                LEFT OUTER JOIN ae_chat_attachments as attachment
                ON message.id = attachment.chat_message_id
                WHERE message.chat_id = :chat_id
                GROUP BY message.id
                ORDER BY `id` ASC";


        $messages = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues( array(':chat_id' => $id) )
            ->queryAll();

        return md5(serialize($messages));
    }

}