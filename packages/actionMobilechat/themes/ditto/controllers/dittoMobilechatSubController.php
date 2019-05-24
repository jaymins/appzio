<?php

class dittoMobilechatSubController extends MobilechatController {

    public function init(){
        parent::init();
    }

    public function getAdditionalArguments($args){
        $args = parent::getAdditionalArguments($args);

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

        // Ditto related only
        // Note: WIP - this was originally placed in the ArticleChat itself
        /*if ( isset($this->varcontent['active_date_id']) AND !empty($this->varcontent['active_date_id']) ) {
            Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');
            $requestsobj = new MobiledatesModel();
            $request_id = $this->varcontent['active_date_id'];

            $request = $requestsobj->findByPk( $request_id );

            if ( $request ) {
                $request->row_last_updated = time();
                $request->update();
            }
        }*/

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