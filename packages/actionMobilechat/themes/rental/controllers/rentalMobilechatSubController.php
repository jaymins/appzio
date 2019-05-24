<?php

class rentalMobilechatSubController extends MobilechatController {

    public function init(){
        parent::init();
    }

    public function getAdditionalArguments($args){
        $args = parent::getAdditionalArguments($args);

        $data = explode('-', $args['context_key']);
        $id = $data[2];
        $variables = $this->getPlayVariables($id);

        if ($variables['role'] == 'tenant' || isset($variables['subrole'])) {
            $args['name_mode'] = 'first_name';
            $args['chat_mode'] = 'full_name';
        } else if ($variables['role'] == 'agent') {
            $args['name_mode'] = 'agency_name';
            $args['chat_mode'] = 'agency_name';
        }

        $args['name_suffix'] = '\'s Profile';

        if ( !isset($this->varcontent['date_preferences']) OR $this->varcontent['date_preferences'] != 'requestor' ) {
            return $args;
        }

        $configs = $this->getChatAcceptButton( $args );

        if ( empty($configs) ) {
            return $args;
        }

        $args['top_button'] = $this->getImage('check-icon.png', array(
            'width' => '40',
            'onclick' => $this->getChatAcceptButton( $args )
        ));
        return $args;
    }

    public function getChatAcceptButton( $args ){

        $otheruser = explode('-chat-', $args['context_key'] );

        if ( count($otheruser) != 2 ) {
            return false;
        }

        foreach($otheruser as $user){
            if ( $user != $this->playid ) {
                if ( is_numeric($user) ) {
                    $remote_play_id = $user;
                }
            }
        }

        $accept_id = 'step-4|' . $remote_play_id . '|accept';

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $accept_id;
        $onclick->sync_open = 1;
        $onclick->action_config = $this->getActionidByPermaname( 'datemanager' );

        return $onclick;
    }

}