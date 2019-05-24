<?php

class imateMobileregister2SubController extends Mobileregister2Controller {

    public function imatePhase1(){

        $this->saveVariable('reg_phase',1);
        $this->data->scroll[] = $this->getSpacer('30');

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('reg-logo.png') ) {
            $image_file = 'reg-logo.png';
        }

        $this->data->scroll[] = $this->getImage( $image_file );

        $this->data->scroll[] = $this->getSpacer('5');

        if($this->fblogin === false) {
            if ($this->getConfigParam('login_branch')) {
                $this->data->scroll[] = $this->getTextbutton('‹ Back to login', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));
            }
        }

        $this->data->scroll[] = $this->getSpacer('20');
        $regfields = $this->setRegFields();

        if($regfields === true){
            $this->data->scroll = array();
            return $this->imatePhase2();
        } else {
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getTextbutton('Register',array('submit_menu_id' => 'saver','style' => 'general_button_style_footer','id' => 'mobilereg_do_registration'));
        }

        return true;
    }


    public function imatePhase2(){
        if(isset($this->menuid) AND $this->menuid == 'continue_to_3'){
            $this->imatePhase3();
            return true;
        }

        $this->saveRegData();
        $this->closeLogin();
        $this->setProfilePic();
        $this->setSex();
        $this->setProfileComment();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');

        if($location AND !$lat){
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getTextbutton('Continue',array('submit_menu_id' => 'saver','style' => 'general_button_style_footer','id' => 'continue_to_3'));
        } else {
            $this->generalComplete();
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->scroll[] = $this->getTextbutton('All set, complete registration', array('submit_menu_id' => 'saver','action' => 'complete-action', 'type' => 'image', 'variable' => 'profilepic', 'style' => 'general_button_style_footer' ,'id' => 'submitter'));
        }
        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache( $this->playid .'-' .'registration',true);
        return true;
    }


    public function imatePhase3(){
        $this->saveVariable('reg_phase',3);
        $lat = $this->getSavedVariable('lon');
        $this->saveVariables();

        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1,$btnaction2);
        $buttonparams['style'] = 'general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        
        $this->data->scroll[] = $this->getSpacer(200);

        if(!$lat){
            if(isset($this->menuid) AND $this->menuid == 'dolocate'){
                $this->data->scroll[] = $this->getText('Something went wrong with the location.', array( 'style' => 'register-text-step-2'));
            } else {
                $this->data->scroll[] = $this->getText('Registration requires your location information. Otherwise the app won\'t work.', array( 'style' => 'register-text-step-2'));
            }
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getText('Locate me now to continue', $buttonparams);
        } else {
            /* we do this only if the matching action is present within the app */
            $actiontypes = Aegame::getAllActionTypes($this->gid);

            foreach($actiontypes as $actiontype){
                if($actiontype['actiontype'] == 'mobilematching'){
                    $this->initMobileMatching();
                    $this->mobilematchingobj->turnUserToItem(false,__FILE__);
                }
            }

            $this->data->scroll[] = $this->getText('How wonderful, all required information is now set up. Please be respectful.', array( 'style' => 'register-text-step-2'));
            $this->generalComplete();
        }
    }

}