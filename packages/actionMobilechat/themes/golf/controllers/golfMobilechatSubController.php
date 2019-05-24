<?php

class golfMobilechatSubController extends MobilechatController {

    public function init(){
        parent::init();
    }

    public function getAdditionalArguments($args){
        $args = parent::getAdditionalArguments($args);

        $args['top_button'] = $this->getText('+ {#invite_to_play#}',array(
            'style' => 'chat-top-right-button', 'onclick' => $this->getChatInviteButton()));

        return $args;
    }

    /* note, this is mostly copy pasted from golfMobilematchingSubController, its a match */
    public function getChatInviteButton(){
        if($this->getSavedVariable('otherapp_installed')){
            $url = 'golfizz://open?action_id=' .$this->getConfigParam('action_id_golfizzmain') .'&menuid=' .'newround-otherapp';

            $onclick2 = new stdClass();
            $onclick2->id = 'link';
            $onclick2->action = 'open-url';
            $onclick2->sync_open = 1;
            $onclick2->action_config = $url;

            return $onclick2;
        } else {

            if($this->getSavedVariable('system_source') == 'client_iphone') {
                $openurl = 'itms://itunes.apple.com/us/app/apple-store/id1153433528?mt=8';
            } else {
                $openurl = 'market://details?id=com.appzio.golfizz';
            }

            return $this->getOnclick('url', false,$openurl);

        }

    }

}