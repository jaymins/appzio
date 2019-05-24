<?php

class oliveMobilechatSubController extends MobilechatController {

    public function groupChatListing(){
        $this->data = new stdClass();

        $this->getHeader( 1 );

        $params['separator_styles'] = $this->getChatSeparatorStyles();
        $params['show_users_count'] = true;
        $params['show_chat_tags'] = true;

        $params['mode'] = 'my_owned_chats';
        $this->data->scroll[] = $this->getSpacer( 15 );
        // $this->data->scroll[] = $this->getSettingsTitlesh('{#chats_i_host#}', false, false);
        $this->data->scroll[] = $this->moduleGroupChatList($params);

        // $params['mode'] = 'public_chats';
        // $this->data->scroll[] = $this->getSettingsTitle('{#public_chats#}', false, false);
        // $this->data->scroll[] = $this->moduleGroupChatList($params);

        $this->data->footer[] = $this->getTextbutton('{#add_a_new_chat#}', array(
            'style' => 'olive-submit-button',
            'id' => 'new-group-chat',
            'submit_menu_id' => 'new-group-chat',
        ));

        return $this->data;
    }

    public function tab4() {
        $this->data = new stdClass();

        $this->getHeader( 4 );
        
        $this->data->scroll[] = $this->getSpacer( 15 );
        // $this->data->scroll[] = $this->getSettingsTitle('{#chats_im_member_of#}', false, false);
        $this->data->scroll[] = $this->moduleGroupChatList(array(
            'mode' => 'my_chats',
            'show_users_count' => true,
            'show_chat_tags' => true,
            'separator_styles' => $this->getChatSeparatorStyles(),
        ));

        return $this->data;
    }

    public function getHeader( $active ) {
        $content = array(
            'tab1' => strtoupper('{#chats_i_host#}'),
            'tab4' => strtoupper('{#chats_i_participate#}')
        );

        $params = array(
            'active' => $active,
            'color_topbar' => '#1f72b6',
            'color_topbar_hilite' => '#2f8fcb',
            'btn_padding' => '12 10 12 10',
            'indicator_mode' => 'fulltab',
            'divider' => true,
        );

        $this->data->header[] = $this->getTabs($content, $params);
    }

    public function getChatSeparatorStyles() {
        return array(
            'margin' => '8 0 4 0',
            'background-color' => '#e5e5e5',
            'height' => '3',
        );
    }

    public function getHeading( $heading ) {
        return $this->getSettingsTitle( $heading, false, false );   
    }

}