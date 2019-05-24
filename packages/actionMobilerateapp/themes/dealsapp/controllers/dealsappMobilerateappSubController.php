<?php

class dealsappMobilerateappSubController extends MobilerateappController {

    public function dealsapp(){

        $data = new StdClass();

        $output[] = $this->getText('Rate this app', array( 'style' => 'survey-text' ));
        $output[] = $this->getImage('rate-image.png', array( 'style' => 'rate-image' ));

        $vars = $this->getPlayVariables( $this->playid );

        $source = isset($vars['system_source']) ? $vars['system_source'] : false;
        $url = false;

        if ( $source == 'client_android' ) {
            $url = $this->getConfigParam( 'rate_url_android' );
        } else {
            $url = $this->getConfigParam( 'rate_url_ios' );
        }

        if ( empty($url) ) {
            $output[] = $this->getText('Missing Rate URL', array( 'style' => 'survey-text' ));
        } else {
            $onclick = new StdClass();
            $onclick->state = 'active';
            $onclick->action = 'open-url';
            $onclick->id = 'open-url';
            $onclick->action_config = $url;

            $data->footer[] = $this->getText('RATE US', array( 'style' => 'rate-button', 'onclick' => $onclick ));
        }

        $data->scroll = $output;
        $this->data = $data;

        return true;
    }

}