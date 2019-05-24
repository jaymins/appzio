<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

Trait Authentication {

    public function loginUser(){
        $this->email = $this->getSubmittedVariableByName('email');
        $password = $this->getSubmittedVariableByName('password');
        $this->password = $this->encryptPassword($password);
        $token = $this->ApiGetUserToken();

        if($token){
            $account = $this->findPlayFromVariable('email',  $this->email);

            if($account){
                $this->switchPlay($account,['nexudus_token' => $token,'password' => $this->password]);
            } else {
                $this->saveVariable('nexudus_token', $token);
                $this->saveVariable('email', $this->email);
                $this->saveVariable('password', $this->password);
                $this->saveVariable('logged_in', 1);
            }

            return true;
        }

        $this->validation_errors['password'] = '{#incorrect_password_or_user_not_found#}';
    }


    public function switchPlay($to_playid, $finish_login = true, $extravars = array())
    {

        \Aeplay::updateOwnership($to_playid, $this->userid);

        /* update ownership */
        $usr = \UserGroupsUseradmin::model()->findByPk($this->userid);
        $original_play_id = $usr->play_id;
        $usr->play_id = $to_playid;
        $usr->update();

        // important: any manipulation from here on after,
        // must apply to logged in play

        $this->playid = $to_playid;

        $original_vars = \AeplayVariable::getArrayOfPlayvariables($original_play_id);

        $copyvars = array('system_push_id',
            'user_language', 'system_source', 'onesignal_deviceid',
            'system_push_plattform', 'perm_push', 'lat', 'lon', 'device',
            'doorflow_not_authenticated','screen_height', 'screen_width');

        $newvars = $extravars;

        foreach ($copyvars as $var) {
            if (isset($original_vars[$var]) AND $original_vars[$var]) {
                $newvars[$var] = $original_vars[$var];
            }
        }

        $this->saveNamedVariables($newvars);

        /* delete the old play if its considered "temporary", ie. the registration is not complete */
        if ($original_play_id AND $to_playid AND $original_play_id != $to_playid) {
                \Aeplay::model()->deleteByPk($original_play_id);
        }

        return true;

    }

    public function closeLogin(){
        //\AeplayBranch::closeBranch($this->actionobj->levelid, $this->playid);
        $this->saveVariable('logged_in', 1);
    }

    public function validateRegistration()
    {
        $firstname = $this->getSubmittedVariableByName('firstname');
        $lastname = $this->getSubmittedVariableByName('lastname');
        $email = $this->getSubmittedVariableByName('email');
        $phone = $this->getSubmittedVariableByName('phone');
        $password = $this->getSubmittedVariableByName('password');
        $repeat_password = $this->getSubmittedVariableByName('repeat_password');
        $terms = $this->getSubmittedVariableByName('terms');
        $over16 = $this->getSubmittedVariableByName('over16');
        $promo = $this->getSubmittedVariableByName('promo');

        if(!$firstname){
            $this->validation_errors['firstname'] = '{#this_is_a_required_field#}';
        }
        if(!$lastname){
            $this->validation_errors['lastname'] = '{#this_is_a_required_field#}';
        }
        if(!$email){
            $this->validation_errors['email'] = '{#this_is_a_required_field#}';
        }
        if(!$phone){
            $this->validation_errors['phone'] = '{#this_is_a_required_field#}';
        }
        if(!$password){
            $this->validation_errors['password'] = '{#this_is_a_required_field#}';
        }
        if(!$repeat_password){
            $this->validation_errors['repeat_password'] = '{#this_is_a_required_field#}';
        }
        if(!$terms){
            $this->validation_errors['terms'] = '{#you_need_to_accept_the_terms_to_continue#}';
        }
        if(!$over16){
            $this->validation_errors['over16'] = '{#you_need_to_be_at_least_sixteen_to_register#}';
        }

        if(!$this->validatePassword($password,true)){
            $this->validation_errors['password'] = '{#your_password_should_contain_big_and_small_letters_and_numbers#}';
        }

        if($password != $repeat_password){
            $this->validation_errors['repeat_password'] = '{#passwords_dont_match#}';
        }

        if(!$this->validateEmail($email)){
            $this->validation_errors['email'] = '{#please_enter_a_valid_email#}';
        }

        if(!$this->validation_errors){
            if(!$this->ApiValidateNexudusEmail($email)){
                $this->validation_errors['email'] = '{#this_email_is_already_in_use#}';
            }
        }

        if(!$this->validation_errors){
            $this->email = $email;
            $this->password = $password;
            $this->phone = $phone;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $create = $this->ApiCreateUser();

            if($create === true){
                $id = $this->ApiGetUserId($email);
                $this->ApiActivateAccount($id);

                $this->saveVariable('firstname', $firstname);
                $this->saveVariable('lastname', $lastname);
                $this->saveVariable('email', $email);
                $this->saveVariable('password', $this->encryptPassword($password));
                $this->saveVariable('phone', $phone);
                $this->saveVariable('logged_in', 1);
                $this->saveVariable('notifications', 1);
                $this->saveVariable('nexudus_userid', $id);
                return true;
            } else {
                $this->validation_errors['firstname'] = json_encode($create);
            }
        }

        return false;
    }





}
