<?php

namespace packages\actionMregister\themes\uikit\Models;

use packages\actionMregister\Models\Model as BootstrapModel;

class Model extends BootstrapModel {

    public function closeLogin($dologin = true)
    {
        if ($this->getConfigParam('require_login') == 1) {
            return true;
        }

        $this->saveVariable('reg_phase', 'complete');

        if ($this->getSavedVariable('fb_universal_login')) {
            $this->saveVariable('fb_id', $this->getSavedVariable('fb_universal_login'));
        }

        $branch = $this->getConfigParam('login_branch');

        if ($dologin) {
            $this->saveVariable('logged_in', 1);
        }

        if (!$branch) {
            return false;
        }

        $introductionBranchId = $this->getConfigParam('intro_branch');

        $this->rewriteActionField('activate_branch_id', $introductionBranchId);

        \AeplayBranch::closeBranch($branch, $this->playid);
        return true;
    }

    public function validatePage1(){
        $vars = $this->getAllSubmittedVariablesByName();

        foreach($vars as $key=>$var){

            switch ($key){
                case 'firstname':
                    $this->firstname = strtolower($var);
                    $this->firstname = ucwords($this->firstname);
                    if(strlen($var) < 2){
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'lastname':
                    $this->lastname = strtolower($var);
                    $this->lastname = ucwords($this->lastname);
                    if(strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'email':
                    $this->email = strtolower($var);

                    if(!$this->validateEmail($this->email)){
                        $this->validation_errors[$key] = '{#please_input_a_valid_email#}';
                    }

                    if ( $whiteList = $this->getConfigParam( 'mreg_valid_domains' ) ) {
                        if(!$this->validateEmailDomain($this->email, $whiteList)) {
                            $this->validation_errors[$key] = '{#you_are_not_authorized_to_register_in_this_application#}';
                        }
                    }

                    $exists = $this->findPlayFromVariables('email', 'reg_phase', $this->email, 'complete');

                    if($exists){
                        $this->validation_errors['email'] = '{#looks_like_this_email_already_exists#}';
                        $this->validation_errors['email_exists'] = true;
                    }

                    break;

                case 'password':
                    $this->password = $var;

                    if(!$this->validatePassword($var)){
                        $this->validation_errors[$key] = '{#password_needs_to_be_at_least_four_characters#}';
                    }

                    break;

                case 'password_again':
                    if($this->getSubmittedVariableByName('password') != $var){
                        $this->validation_errors[$key] = '{#passwords_don\'t_match#}';
                    }
                    break;

                case 'phone':
                    $this->phone = str_replace(' ', '', $var);
                    if(!is_numeric($this->phone)){
                        $this->validation_errors[$key] = '{#please_only_input_numbers#}';
                    }

                    if(strlen($this->phone) < 5){
                        $this->validation_errors[$key] = '{#please_enter_a_valid_phone_number#}';
                    }

                    break;

            }
        }
    }

}