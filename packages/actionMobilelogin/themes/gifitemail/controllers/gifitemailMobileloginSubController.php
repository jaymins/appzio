<?php

class gifitemailMobileloginSubController extends MobileloginController {


    public function setHeader($padding=false){
        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('login-logo.png') ) {
            $image_file = 'login-logo.png';
        }
        
        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }

        $this->data->scroll[] = $this->getSpacer('20');
    }

    public function loggingInHeader(){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getText('{#logging_in#}',array('style' => 'gifit-titletext-header'));
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
        $this->data->scroll[] = $this->getFullPageLoader();
    }

    public function logoutHeader(){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getText('{#logging_out#}',array('style' => 'gifit-titletext-header'));
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
        $this->data->scroll[] = $this->getFullPageLoader();
    }


}