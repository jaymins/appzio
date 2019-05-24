<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMofficernd\Models;

/**
 * This is left here as an example.
 * Todo: refactor with new API methods
 * Trait Authentication
 * @package packages\actionMofficernd\Models
 */

Trait Authentication {

    public function loginUser(){
        $this->email = $this->getSubmittedVariableByName('email');
        $password = $this->getSubmittedVariableByName('password');
        $this->password = $this->encryptPassword($password);
        $token = $this->ApiGetUserToken();

        if($token){
            $this->saveVariable('officernd_token', $token);
            $this->saveVariable('email', $this->email);
            $this->saveVariable('password', $this->password);
            $this->saveVariable('logged_in', 1);
            return true;
        }

        $this->validation_errors['password'] = '{#incorrect_password_or_user_not_found#}';
    }

    public function closeLogin(){
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
                $this->saveVariable('officernd_userid', $id);
                return true;
            } else {
                $this->validation_errors['firstname'] = json_encode($create);
            }
        }

        return false;
    }





}
