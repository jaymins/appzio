<?php

class hairdresserMobileregister2SubController extends Mobileregister2Controller {

    public function hairdresserPhase1(){
        $this->saveVariable('reg_phase',1);

        $this->data->scroll[] = $this->getImage('profile-heading.png', array( 'style' => 'profile-header', 'variable' => 'profilepic' ));

        // $this->data->scroll[] = $this->getText('Please register and set up an account with us so we can confirm', array( 'style' => 'profile-header-text-top' ));
        // $this->data->scroll[] = $this->getText('if there are changes, and make direct bookings in the future.', array( 'style' => 'profile-header-text' ));
        // $this->data->scroll[] = $this->getText('You can register with your phone number or any of the following', array( 'style' => 'profile-header-text' ));
        // $this->data->scroll[] = $this->getText('social media accounts:', array( 'style' => 'profile-header-text' ));

        $this->data->scroll[] = $this->getTextbutton('Facebook', array('submit_menu_id' => 'saver','style' => 'facebook-button','id' => 'fb-login', 'action' => 'fb-login'));

        $this->data->scroll[] = $this->getImage('profile-or-divider.png', array( 'style' => 'image-profile-spacer' ));

        $regfields = $this->setHairdresserFields();
        // $regfields = false;

        if($regfields === true){
            $this->data->scroll = array();
            return $this->hairdresserPhase2();
        } else {
            $this->data->footer[] = $this->getTextbutton('Sign Up', array('submit_menu_id' => 'saver','style' => 'submit-button','id' => 'mobilereg_do_registration'));
        }

        return true;
    }


    public function hairdresserPhase2(){
        $this->saveRegData();

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
            $txt = 'Change the photo';

            if(isset($this->varcontent['real_name'])){
                AeplayVariable::updateWithId($this->playid,$this->vars['name'],$this->varcontent['real_name']);
            }
        } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
            $txt = 'Change the photo';
        } else {
            $pic = 'photo-placeholder.png';
            $txt = 'Add a photo';
        }

        $this->data->scroll[] = $this->getImage($pic, array( 'variable' => $this->vars['profilepic'] ));
        $this->data->scroll[] = $this->getText('Please finish your profile setup', array( 'style' => 'register-text-step-2' ));
        $this->data->scroll[] = $this->getFieldupload($txt, array( 'type' => 'image', 'variable' => 'profilepic', 'style' => 'register_button' ));
        $this->data->scroll[] = $this->getFieldtextarea('', array( 'variable' => 'profile_comment', 'hint' => 'Comment (optional):', 'style' => 'comment-field','submit_menu_id' => 'continue_to_3' ));

        $this->data->footer[] = $this->getTextbutton( 'Sign Up', array('id' => 'mobilereg_finish_registration', 'style' => 'submit-button', 'action' => 'complete-action' ) );
    }


    public function setHairdresserFields() {
        $error = false;

        if ($this->getConfigParam('collect_name',1)) {
            $realname = $this->getVariable('real_name');
            $name = $this->getVariable('name');

            if ($realname AND !$name) {
                $this->saveVariable('name',$realname);
            }

            $error = $this->checkForError('real_name','Please input your first and last names');
            $this->getFieldWithValidation($this->vars['real_name'],'Name',$error);
        }

        if ($this->getConfigParam('collect_phone')) {
            $error = $this->checkForError('phone','Include the country code with +');
            $this->getFieldWithValidation($this->vars['phone'],'Phone',$error);
        }

        if ( !$error AND $this->menuid == 'mobilereg_do_registration' ) {
            $this->saveVariable('reg_phase',2);
            return true;
        }

        return false;
    }

}