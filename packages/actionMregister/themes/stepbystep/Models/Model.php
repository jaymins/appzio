<?php


namespace packages\actionMregister\themes\stepbystep\Models;
use function is_array;
use packages\actionMregister\Models\Model as BootstrapModel;
use function strlen;
use function strtolower;

class Model extends BootstrapModel {

    use TransferData;

    public $used_vars = array(
        'firstname','lastname','email','phone','username','password','birthdate','profilepic','role'
    );

    public function validateName(){

        if(strlen($this->getSubmittedVariableByName('firstname')) < 2){
            $this->validation_errors['firstname'] = '{#name_should_be_at_least_two_characters#}';
        }

        if(strlen($this->getSubmittedVariableByName('lastname')) < 2){
            $this->validation_errors['lastname'] = '{#name_should_be_at_least_two_characters#}';
        }

        if(!($this->getSubmittedVariableByName('firstname'))){
            $this->validation_errors['firstname'] = '{#you_need_to_define_your_name#}';
        }

        if(!($this->getSubmittedVariableByName('lastname'))){
            $this->validation_errors['lastname'] = '{#you_need_to_define_your_last_name#}';
        }


        if(!$this->validation_errors){
            $variables['firstname'] = $this->getSubmittedVariableByName('firstname');
            $variables['lastname'] = $this->getSubmittedVariableByName('lastname');
            $this->saveNamedVariables($variables);
        }
    }

    public function validateMyEmail(){
        $email = trim(strtolower($this->getSubmittedVariableByName('email')));

        if(!$this->validateEmail($email)){
            $this->validation_errors['email'] = '{#please_enter_valid_email#}';
            return true;
        }

        $exists = $this->findPlayFromVariables('email', 'reg_phase', $email, 'complete');

        if($exists){
            $this->validation_errors['email'] = '{#looks_like_this_email_already_exists#}';
            $this->validation_errors['email_exists'] = true;
        }

        if(!$this->validation_errors){
            $this->saveVariable('email', $email);
        }
    }

    public function validatePhone(){

        if(strlen($this->getSubmittedVariableByName('countrycode')) < 2){
            $this->validation_errors['phonenumber'] = '{#please_check_country_code#}';
        }

        if(strlen($this->getSubmittedVariableByName('phonenumber')) < 6){
            $this->validation_errors['phonenumber'] = '{#please_check_the_number_is_correct#}';
        }

        if(!$this->validation_errors){
            $number = $this->getSubmittedVariableByName('countrycode') .$this->getSubmittedVariableByName('phonenumber');
            $this->saveVariable('phone',$number);
        }

    }

    public function validateUsername(){
        if(strlen($this->getSubmittedVariableByName('username')) < 2){
            $this->validation_errors['username'] = '{#username_is_mandatory#}';
        }

        if($this->getSubmittedVariableByName('username')) {
            $varid = $this->getVariableId('username');
            $value = $this->getSubmittedVariableByName('username');
            $ob = \AeplayVariable::model()->findAllByAttributes(array('variable_id' => $varid, 'value' => $value));

            if (is_array($ob) AND count($ob) > 0) {
                foreach ($ob as $nn) {
                    if ($nn->play_id != $this->playid) {
                        $this->validation_errors['username'] = '{#this_name_is_taken#}';
                    }
                }
            }
        }

        if(!$this->validation_errors) {
            $this->saveVariable('username', $this->getSubmittedVariableByName('username'));
        }
    }

    public function validateMyPassword(){

        if($this->getSubmittedVariableByName('pass1') != $this->getSubmittedVariableByName('pass2')){
            $this->validation_errors['pass2'] = '{#passwords_dont_match#}';
        }

        if(strlen($this->getSubmittedVariableByName('pass1')) < 4){
            $this->validation_errors['pass1'] = '{#password_is_too_short#}';
        }

        if(!$this->validation_errors) {
            $pass = sha1(strtolower(trim($this->getSubmittedVariableByName('pass1'))));
            $this->saveVariable('password', $pass);
        }
    }


    public function saveBirthday(){
        $day = $this->getSubmittedVariableByName('birth_day');
        $month = $this->getSubmittedVariableByName('birth_month');
        $year = $this->getSubmittedVariableByName('birth_year');

        if($year > 1997){
            $this->saveVariable('role', 'child');
        } else {
            $this->saveVariable('role', 'parent');
        }

        $this->saveVariable('birthdate', $day.$month.$year);

    }

    public function cleanRegdata(){
        \Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
        $loginmodel = new \MobileloginModel();
        $loginmodel->userid = $this->userid;
        $loginmodel->playid = $this->playid;
        $loginmodel->gid = $this->appid;
        $play = $loginmodel->newPlay();
        $this->playid = $play;
    }

    public function getPhase(){

        if($this->getSavedVariable('username') AND $this->getSavedVariable('reg_phase') == 'complete'){
            return 8;
        }

        if($this->getSavedVariable('username') AND $this->getSavedVariable('logged_in') == 1){
            return 7;
        }

        if($this->getSavedVariable('username')){
            return 6;
        }

        if($this->getSavedVariable('password')){
            return 5;
        }

/*        if($this->getSavedVariable('phone')){
            return 5;
        }*/

        if($this->getSavedVariable('birthdate')){
            return 4;
        }

        if($this->getSavedVariable('email')){
            return 3;
        }

        if($this->getSavedVariable('firstname')){
            return 2;
        }

        return 1;

    }

    public function getMyCountryCode(){
        /* 1. check if user has submitted value for this */
        $code = $this->getSubmittedVariableByName('countrycode');

        /* 2. if not, try if we can fetch the country code */
        if(!$code) {
            $code = $this->getCountryCode();
        }

        /* 3. if still no go, just send empty so that the field type is right */
        if(!is_string($code)){
            $code = '';
        }

        return $code;

    }

    /* adds required variables and closes the login */
    public function closeLogin($dologin=true){

        $this->transferData();

        if ($this->getConfigParam('require_login') == 1) {
            return true;
        }
        if($this->getSavedVariable('fb_universal_login')){
            $this->saveVariable('fb_id',$this->getSavedVariable('fb_universal_login'));
        }

        $this->saveVariable('reg_phase','complete');

        $branch = $this->getConfigParam('login_branch');

        if($dologin){
            $this->saveVariable('logged_in',1);
        }

        if(!$branch){
            return false;
        }

        \AeplayBranch::closeBranch($branch,$this->playid);
        return true;
    }

}