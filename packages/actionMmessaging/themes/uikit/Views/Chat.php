<?php

namespace packages\actionMmessaging\themes\uikit\Views;

use packages\actionMmessaging\Views\Chat as BootstrapView;
use packages\actionMmessaging\themes\uikit\Components\Components;

class Chat extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();

        $chat_content = $this->getData( 'chat_content', 'mixed' );

        if ( empty($chat_content) ) {
            $this->layout->scroll[] = $this->getComponentText('{#no_comments_yet#}', [
                'style' => 'uikit-chat-no-comments'
            ]);
        } else {

            $this->layout->scroll[] = $this->getComponentSpacer(10);

            foreach ($chat_content as $i => $item) {
                
                if ( $item['user_is_owner'] ) {
                    $this->layout->scroll[] = $this->uiKitChatMessageOwner( $item );

                    if ( count($chat_content) == ($i+1) ) {
                        $this->layout->scroll[] = $this->getMessageMeta( $item );
                    }

                } else {
                    $this->layout->scroll[] = $this->uiKitChatMessageUser( $item );
                }

                $this->layout->scroll[] = $this->getComponentSpacer(10);
            }

        }

        $this->getChatFooter();

        return $this->layout;
    }

    private function getChatFooter() {
        $this->layout->footer[] = $this->getComponentDivider();
        $this->layout->footer[] = $this->uiKitChatFooter();
    }

    private function getMessageMeta($item) {

        if ( $item['msg_is_read'] ) {
            $text = '{#seen#}';
        } else {
            $text = date('H:i', $item['date']);
        }

        return $this->uiKitChatMeta($text);
    }

}