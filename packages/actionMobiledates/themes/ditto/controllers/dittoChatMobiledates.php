<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

class dittoChatMobiledates extends dittoMobiledatesSubController {

    public $date_type;
    public $requestsobj;
    public $mobilematchingobj;

    public function tab1(){
        
        $this->updateScreen();

        $this->data = new stdClass();

        $this->setChatMenus();

        $this->date_type = $this->getSavedVariable( 'date_preferences' );

        if ( $this->date_type == 'requestor' ) {
            $this->subViewRequester();
        } else {
            $this->subViewAccepter();            
        }

        return $this->data;
    }

    public function subViewRequester() {
        $matches = array();
        $all_matches = $this->getMatchesArray();

        if ( $all_matches ) {
            $sorted_matches = $this->getPendingAccepters( $all_matches );
            $matches = $sorted_matches['accepters_with_chat'];
        }

        if ( empty($matches) ) {
            $matches = $this->getActiveDateUsers();
        }

        if ( empty($matches) ) {
            $this->data->scroll[] = $this->getText( 'You don\'t have any matches yet', array('style' => 'listing-heading'));
            return $this->data;
        }

        foreach ($matches as $user) {
            $this->renderMatchItem( $user['play_id'], 'step-3' );
        }
    }

    public function subViewAccepter() {
        $rq_data = $this->getSortedRequesters();

        if ( empty($rq_data['requesters_with_application']) AND empty($rq_data['matched_users']) ) {
            $this->data->scroll[] = $this->getText( 'You don\'t have any plan requests', array('style' => 'listing-heading'));
            return $this->data;
        }

        $users = ( $rq_data['requesters_with_application'] ? $rq_data['requesters_with_application'] : $rq_data['matched_users'] );

        $users = $this->getUsersSortedByChatTime( $users );

        foreach ($users as $user) {
            $this->renderMatchItem( $user['play_id'], 'step-2' );
        }
    }

    public function renderMatchItem( $user_id, $to_profile_step ) {
        $user_data = $this->getPlayVariables( $user_id );

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $to_profile_step . '|' . $user_id;
        $onclick->action_config = $this->getActionidByPermaname( 'datemanager' );
        $onclick->back_button = 1;
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;

        $image = 'image-placeholder.png';
        if ( isset($user_data['profilepic']) AND !empty($user_data['profilepic']) ) {
            $image = $user_data['profilepic'];
        }

        $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes', 'priority' => '9',));
        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes'));
        $age = ( isset($user_data['age']) ? ', ' . $user_data['age'] : '' );

        $col_left = $this->getColumn(array(
            $this->getImage( 'overlay.png', array( 'crop' => 'round', 'floating' => '1' ) ),
            $this->getImage( $listing_image, array( 'crop' => 'round', 'margin' => '1 1 1 1', 'priority' => '9', 'image_fallback' => $fallback_image_path ) ),
        ), array( 'style' => 'listing-cell-left', 'onclick' => $onclick ) );

        $user_center_text[] = $this->getText($this->getFirstName( $user_data['name'] ) . $age, array( 'style' => 'profile-right-cell-heading' ));

        $message = $this->getLatestMessage( $user_id );

        $msg_class = 'profile-right-cell-description';
        if ( $message AND
            ( !$message->chat_message_is_read AND ( $message->author_play_id != $this->playid ) )
        ) {
            $msg_class = 'profile-right-cell-description-bold';
        }

        if ( $message ) {
            $chat_message = $message->chat_message_text;
            $user_center_text[] = $this->getText( 'Message: ' . Helper::truncateWords( $chat_message, 2 ), array( 'style' => $msg_class ));
        } else if ( isset($user_data['activity_idea']) ) {
            $user_center_text[] = $this->getText($user_data['activity_idea'], array( 'style' => 'profile-right-cell-description' ));
        } else {
            $user_center_text[] = $this->getText('', array( 'style' => 'profile-right-cell-description' ));
        }

        $col_center = $this->getColumn($user_center_text, array( 'style' => 'ditto-chat-middle-cell', 'onclick' => $this->getChatClick( $user_id ) ));

        if ( $message ) {
            $stamp = $message->chat_message_timestamp;
            $col_right = $this->getText(date('G:i', strtotime($stamp)), array( 'style' => 'profile-right-cell-description' ));
        } else {
            $col_right = $this->getText( '' );
        }

        $this->data->scroll[] = $this->getRow(
            array( $col_left, $col_center, $col_right ),
            array( 'style' => 'listing-main-row-transparent', )
        );

        $this->data->scroll[] = $this->getHairline('#734065', array(
            'margin' => '0 0 20 0'
        ));
    }

    public function getUsersSortedByChatTime( $users ) {

        $users_sorted = array();

        foreach ($users as $i => $user) {
            $user_id = $user['play_id'];
            $message = $this->getLatestMessage( $user_id );

            if ( $message ) {
                $stamp = strtotime($message->chat_message_timestamp);
            } else {
                $stamp = '000000000' . $i;
            }

            $users_sorted[$stamp] = $user;
        }

        krsort( $users_sorted, SORT_NUMERIC );

        return $users_sorted;
    }

    public function getActiveDateUsers() {
        $db_matches = $this->playkeyvaluestorage->get( 'twoway_matches' );

        if ( empty($db_matches) ) {
            return false;
        }

        $matches = array();

        foreach ($db_matches as $match) {
            $matches[] = array(
                'play_id' => $match
            );
        }

        return $matches;
    }

    public function getChatClick( $remote_id ) {
        $chat_room_id = $this->getTwoWayChatId( $remote_id );

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $chat_room_id;
        $onclick->action_config = $this->getActionidByPermaname( 'chat' );
        $onclick->back_button = 1;
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;
        $onclick->viewport = 'bottom';

        return $onclick;
    }

}