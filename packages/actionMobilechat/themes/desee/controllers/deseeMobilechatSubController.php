<?php

Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class deseeMobilechatSubController extends MobilechatController {

    private $metas;
    private $chat_limit_time = 43200;

    public function init(){
        parent::init();
    }

    public function getAdditionalArguments($args){

        $args = parent::getAdditionalArguments($args);

        $this->metas = new MobilematchingmetaModel();
        $this->metas->current_playid = $this->playid;

        // To Do: Test how this would behave in reality when used in the chat
        $this->handleExtrasPayments();

        $args['hide_pic_button'] = ( $this->metas->checkMeta( 'send-media' ) ? 0 : 1 );

        $remote_user_play_id = $this->getRemotePlayID( $args );

        if ( empty($remote_user_play_id) ) {
            return $args;
        }

        $args['current_user_unmatched'] = $this->userUnmatched( $remote_user_play_id );

        $this->setEnteredTimestamp( $args );

        Appcaching::removeGlobalCache($this->metas->current_playid . '-matchid');

        return $args;
    }

    public function setEnteredTimestamp( $args ) {

        // Update only if "menuid" is present in the request
        if ( !isset($this->submit['menuid']) ) {
            return false;
        }

        if ( isset($args['context_key']) AND $args['context_key'] ) {

            $value = array(
                $args['context_key'] => time()
            );

            $this->addToVariable('entered_chat_timestamp', $value, true);
        }

    }

    public function getRemotePlayID( $args ) {

        $chat_users = explode('-chat-',$args['context_key']);

        if ( count($chat_users) != 2 ) {
            return false;
        }

        foreach($chat_users as $user){
            if($user != $this->playid){
                if(is_numeric($user)){
                    return $user;
                }
            }
        }


    }

    public function userUnmatched( $play_id ) {

        $unmatched_array = json_decode($this->getVariable('unmatched_me'), true);
        $blocked_array = json_decode($this->getVariable('blocked_users'), true);

        if ( empty($unmatched_array) ) {
            $unmatched_array = array();
        }

        if ( empty($blocked_array) ) {
            $blocked_array = array();
        }

        $unmatched_list = array_merge( $unmatched_array, $blocked_array );

        if ( empty($unmatched_list) ) {
            return false;
        }

        foreach ($unmatched_list as $user) {
            $id = ( isset($user[0]) ? $user[0] : 0 );
            $stamp = ( isset($user[1]) ? $user[1] : 0 );

            if ( $id == $play_id ) {
                if (
                    $this->metas->checkMeta('chat-with-blocked') AND
                    ($stamp + $this->chat_limit_time < time())
                ) {
                    return true;
                }
            }

        }

        return false;
    }

    public function getChatDivs() {
        return $this->registerProductDiv( 'buy-send-media', 'm_share_media.png', '{#attach_photos#}', 'Attach photos on all your chats for next 30 days', 'attach_photos.01', 'attach_photos.001', true );
    }

    public function handleExtrasPayments() {

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->metas->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $this->metas->play_id = $this->playid;
        $this->metas->meta_key = $card_config['trigger'];
        $this->metas->meta_value = ( $card_config['measurement'] == 'time' ? time() : $card_config['amount'] );
        $this->metas->meta_limit = $card_config['measurement'];
        $this->metas->saveMeta();

        return true;
    }

}