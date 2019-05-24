<?php

class dealsappMobileregister2SubController extends Mobileregister2Controller {

    public $fontstyle = array(
        'width' => '70%'
    );

    public function dealsappPhase1(){

        $this->saveVariable('reg_phase',1);

        $this->data->scroll[] = $this->getText( 'Please enter your profile details in order to continue.', array( 'style' => 'reg-heading-text' ) );

        $regfields = $this->setRegFields();

        if ($regfields === true) {
            $this->saveRegData();

            $this->saveVariable( 'notify', 1 );

            $complete = new StdClass();
            $complete->action = 'complete-action';
            $this->data->onload[] = $complete;
        }
        
        $this->data->footer[] = $this->getTextbutton('Sign Up', array( 'id' => 'mobilereg_do_registration', 'style' => 'register-button' ));
    }

}