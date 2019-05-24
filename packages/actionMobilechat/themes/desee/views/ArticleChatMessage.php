<?php

Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aechat.models.*');

class ArticleChatMessage extends ArticleComponent
{

    private $current_msg;
    private $user_is_owner;
    private $context_key;
    private $hide_time;
    private $total_msgs;
    private $current_msg_index;

    public function template(){

        $this->current_msg = $this->addParam('current_msg',$this->options,false);
        $this->user_is_owner = $this->addParam('user_is_owner',$this->options,false);
        $this->context_key = $this->addParam('context_key',$this->options,false);
        $this->hide_time = $this->addParam('hide_time',$this->options,false);

        $this->total_msgs = $this->addParam('total_msgs',$this->options,false);
        $this->current_msg_index = $this->addParam('current_msg_index',$this->options,false);

        // $userinfo = $this->addParam('userinfo',$this->options,false);

        if ( $this->user_is_owner ) {
            return $this->factoryobj->getRow(array(
                $this->factoryobj->getColumn(array(
                    $this->getMyMessage(),
                    $this->getMsgDate( 'my-chat-date' ),
                ), array(
                    'width' => '100%'
                )),
            ));
        } else {
            return $this->factoryobj->getRow(array(
                $this->factoryobj->getColumn(array(
                    $this->getUserMessage(),
                    $this->getMsgDate( 'user-chat-date' ),
                ), array(
                    'width' => '100%'
                )),
            ));
        }

    }

    public function getMessageAttachment() {

        $use_blur = false;

        if ( !$this->user_is_owner ) {
            $time_to_read = 300;
            $enter_time = $this->getChatEnterTime();

            $seconds_left = $time_to_read - ( time() - $enter_time );
            // $message_visible_until = $enter_time + $time_to_read;

            // This is the difference between the actual time, when the message was originally sent
            // and the time when the person entered the chat section
            $msg_diff = $enter_time - $this->current_msg['date'];
            if ( $seconds_left < $msg_diff ) {
                $use_blur = true;
            }
        }

        $big_img_params = array(
            'imgwidth' => '900',
            'imgheight' => '900',
            'priority' => 9,
        );

        if ( $use_blur ) {
            $big_img_params['blur'] = 1;
        }

        $image = $this->factoryobj->getImage($this->current_msg['attachment'], $big_img_params);

        $img_params = array(
            'imgwidth' => 300,
            'imgheight' => 300,
            'border-radius' => 8,
            'margin' => '4 4 4 4',
            'priority' => '9',
            'tap_to_open' => 1,
            'tap_image' => '',
        );

        if ( isset($image->content) ) {
            $bigimage = $image->content;
            $img_params['tap_image'] = $bigimage;
        }

        if ( $use_blur ) {
            $img_params['blur'] = 1;
        }

        return $this->factoryobj->getImage($this->current_msg['attachment'], $img_params);
    }

    private function getChatEnterTime() {
        $db_times = $this->factoryobj->getSavedVariable( 'entered_chat_timestamp' );

        if ( empty($db_times) ) {
            return 0;
        }

        $times_array = json_decode( $db_times, true );

        if ( isset($times_array[$this->context_key]) ) {
            return $times_array[$this->context_key];
        }

        return 0;
    }

    private function getMyMessage() {
        return $this->factoryobj->getRow(array(
            $this->factoryobj->getColumn($this->getMsgInfo(), array(
                'width' => $this->getMsgWidth(),
                'vertical-align' => 'middle',
                'text-align' => 'right',
                'background-color' => '#ffc204',
                'border-radius' => '8',
                'color' => '#ffffff',
            )),
            $this->getRightArrow(),
        ), array(
            'style' => 'chat-row-msg-mine'
        ));
    }

    private function getUserMessage() {
        return $this->factoryobj->getRow(array(
            $this->factoryobj->getColumn($this->getMsgInfo(), array(
                'width' => $this->getMsgWidth(),
                'vertical-align' => 'middle',
                'text-align' => 'left',
                'background-color' => '#FFFFFF',
                'border-width' => '1',
                'border-color' => 'e5e5e6',
                'border-radius' => '8',
                'margin' => '0 0 0 12',
            )),
            $this->getLeftArrow(),
        ), array(
            'style' => 'chat-row-msg'
        ));
    }

    private function getMsgDate( $class ) {

        $output[] = $this->factoryobj->getText(date('H:iA', $this->current_msg['date']), array(
            'style' => 'chat-date'
        ));

        if (
            ( $this->total_msgs == ($this->current_msg_index+1) ) AND
            $this->user_is_owner AND
            is_object($this->checkIfSeen()) )
        {
            $output[] = $this->checkIfSeen();
        }

        return $this->factoryobj->getRow($output, array(
            'style' => 'row-' . $class
        ));
    }

    private function getMsgInfo() {
        $colitems = [];

        if ( !empty($this->current_msg['msg']) ) {
            $colitems[] = $this->factoryobj->getText($this->current_msg['msg'], array(
                'style' => 'chat-msg-text'
            ));
        }

        if ( isset($this->current_msg['attachment']) ) {
            $colitems[] = $this->getMessageAttachment();
        }

        return $colitems;
    }

    private function getMsgWidth() {

        if ( isset($this->current_msg['attachment']) AND empty($this->current_msg['msg']) ) {
            return 'default';
        }

        if ( $this->current_msg['msg'] ) {
            $lenght = strlen($this->current_msg['msg']);

            if ( $lenght < 10 ) {
                return '40%';
            } else if ( $lenght > 100 ) {
                return '95%';
            }

        }

    }

    private function getLeftArrow() {
        return $this->factoryobj->getColumn(array(
            $this->factoryobj->getImage('desee-arrow-left.png')
        ), array(
            'width' => '13',
            'vertical-align' => 'middle',
            'floating' => '1',
            'float' => 'left',
            'margin' => '0 13 0 0',
        ));
    }

    private function getRightArrow() {
        return $this->factoryobj->getColumn(array(
            $this->factoryobj->getImage('desee-arrow-right.png'),
        ), array(
            'style' => 'chat-message-arrow'
        ));
    }

    private function checkIfSeen() {

        if(!isset($this->current_msg['id'])){
            return false;
        }

        $is_seen = $this->factoryobj->mobilechatobj->checkMessageStatus( $this->current_msg['id'] );

        if ( $is_seen ) {
            return $this->factoryobj->getImage('icon-seen.png', array(
                'width' => '10',
                'margin' => '0 0 0 5',
            ));
        }

        return false;
    }

}