<?php


namespace packages\actionMregister\Models;

use Bootstrap\Models\BootstrapModel;
use function is_numeric;
use function str_replace;
use function stristr;
use function strtolower;
use function ucwords;

class Model extends BootstrapModel
{

    public $validation_errors;

    public $password;
    public $phone;
    public $email;
    public $firstname;
    public $lastname;
    public $username;
    public $gender;
    public $use_strict_password = false;

    /* gets a list of configuration fields, these are used by the view */
    public function getFieldList()
    {
        $params = $this->getAllConfigParams();
        $output = array();

        foreach ($params as $key => $item) {
            if (stristr($key, 'mreg') AND $item) {
                $output[] = $key;
            }
        }

        return $output;
    }

    /* active selected country for the phone number field */
    public function getCountry()
    {

        if ($this->getSavedVariable('country')) {
            $country = $this->getCountryCode();
        } elseif ($this->getSubmittedVariableByName('country_selected')) {
            $country = $this->getSubmittedVariableByName('country_selected');
            $this->sessionSet('selected_country', $country);
        } elseif ($this->sessionGet('country')) {
            $country = $this->sessionGet('selected_country');
        } else {
            $country = '+44';
        }

        return $country;
    }

    /* save to variables */
    public function savePage1()
    {
        $vars['password'] = sha1($this->password);
        $vars['email'] = $this->email;
        $vars['phone'] = $this->phone;
        $vars['firstname'] = $this->firstname;
        $vars['lastname'] = $this->lastname;
        $vars['gender'] = $this->gender;
        $vars['nickname'] = $this->getSubmittedVariableByName('nickname');
        $vars['age'] = $this->getSubmittedVariableByName('age');
        $vars['profile_comment'] = $this->getSubmittedVariableByName('profile_comment');
        $vars['username'] = $this->getSubmittedVariableByName('username');
        $vars['name'] = $this->getSubmittedVariableByName('name');
        $vars['real_name'] = $this->firstname . ' ' . $this->lastname;

        $vars['facebook_profile'] = $this->getSubmittedVariableByName('facebook_profile');
        $vars['instagram_profile'] = $this->getSubmittedVariableByName('instagram_profile');
        $vars['twitter_profile'] = $this->getSubmittedVariableByName('twitter_profile');

        $this->saveNamedVariables($vars);
    }

    public function savePage2()
    {
        $vars = $this->getAllSubmittedVariablesByName();
        $this->saveNamedVariables($vars);
    }

    public function validatePage2()
    {
        $vars = $this->getAllSubmittedVariablesByName();
        foreach ($vars as $key => $var) {

            switch ($key) {
                case 'city':
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#this_is_a_mandatory_field#}';
                    }
                    break;

                case 'country':
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#this_is_a_mandatory_field#}';
                    }
                    break;

                case 'profile_comment':
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#please_add_short_info_about_yourself#}';
                    }
                    break;
            }
        }

    }

    /* will save all validated variables and add rest to error array */
    public function validatePage1()
    {
        $vars = $this->getAllSubmittedVariablesByName();

        foreach ($vars as $key => $var) {

            switch ($key) {
                case 'firstname':
                    $this->firstname = strtolower($var);
                    $this->firstname = ucwords($this->firstname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'lastname':
                    $this->lastname = strtolower($var);
                    $this->lastname = ucwords($this->lastname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'username':

                    $this->username = $var;
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_username_should_have_at_least_two_characters#}';
                    }

                    break;

                case 'email':
                    $this->email = strtolower($var);

                    if (!$this->validateEmail($this->email)) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_email#}';
                    }

                    $exists = $this->findPlayFromVariables('email', 'reg_phase', $this->email, 'complete');

                    if ($exists) {
                        $this->validation_errors['email'] = '{#looks_like_this_email_already_exists#}';
                        $this->validation_errors['email_exists'] = true;
                    }

                    break;

                case 'password':
                    $this->password = $var;

                    if (!$this->validatePassword($var, $this->use_strict_password)) {
                        $this->validation_errors[$key] = '{#please_enter_a_valid_password#}';
                    }

                    break;

                case 'password_again':
                    if ($this->getSubmittedVariableByName('password') != $var) {
                        $this->validation_errors[$key] = '{#passwords_dont_match#}';
                    }
                    break;

                case 'phone':
                    $this->phone = str_replace(' ', '', $var);
                    if (!is_numeric($this->phone)) {
                        $this->validation_errors[$key] = '{#please_only_input_numbers#}';
                    }

                    if (strlen($this->phone) < 5) {
                        $this->validation_errors[$key] = '{#please_enter_a_valid_phone_number#}';
                    }

                    break;

            }
        }
    }

    public function setFirstNameLastName()
    {
        if($this->getSavedVariable('real_name') AND !$this->getSavedVariable('firstname')){
            $names = explode(' ', $this->getSavedVariable('real_name'));
            $this->saveVariable('firstname', $names[0]);
            $this->saveVariable('lastname', $names[1]);
        } elseif($this->getSavedVariable('name') AND !$this->getSavedVariable('firstname')){
            $names = explode(' ', $this->getSavedVariable('name'));
            $this->saveVariable('firstname', $names[0]);
            $this->saveVariable('lastname', $names[1]);
        }
    }

    protected function validateEmailDomain($email, $domains)
    {
        if (empty($email)) {
            return false;
        }

        $is_valid = false;
        $splitEmail = explode('@', $email, 2);

        if (!isset($splitEmail[1])) {
            return false;
        }

        $email_tld = $splitEmail[1];
        
        $valid_domains = explode(',', $domains);

        foreach ($valid_domains as $valid_domain) {
            $domain = trim( $valid_domain );

            if ( preg_match("~{$domain}~", $email_tld) ) {
                $is_valid = true;
            }
        }

        return $is_valid;
    }

    /* adds required variables and closes the login */
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

        \AeplayBranch::closeBranch($branch, $this->playid);
        return true;
    }

    public function loginWithFacebook($thirdpartyid)
    {

        $pointer = 'email';

        if (empty($thirdpartyid) OR empty($this->appid)) {
            return false;
        }

        $rows = $this->getThirdParty($pointer, $thirdpartyid);

        foreach ($rows as $row) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */

            if(isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete'){
                $play = $row['play_id'];

                if ($this->playid != $play) {
                    $this->switchPlay($play, false);
                }

                $this->playid = $play;
                return $play;
            }
        }

        return false;
    }

    public function getThirdParty($pointer, $thirdPartyID)
    {
        $sql = "SELECT ae_game_play_variable.id, ae_game_play_variable.value, ae_game_variable.id, ae_game_variable.name, ae_game_play_variable.play_id FROM ae_game_play_variable
                LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id

                WHERE `value` = '$thirdPartyID'
                AND ae_game_variable.`name` = '$pointer'
                AND ae_game_variable.game_id = $this->appid
                
                ORDER BY play_id ASC
                ";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array())
            ->queryAll();

        return $rows;
    }

    public function switchPlay($to_playid, $finish_login = true, $extravars = array())
    {
        \Aeplay::updateOwnership($to_playid, $this->userid);

        /* update ownership */
        $usr = \UserGroupsUseradmin::model()->findByPk($this->userid);
        $original_play_id = $usr->play_id;
        $usr->play_id = $to_playid;
        $usr->update();

        $original_vars = \AeplayVariable::getArrayOfPlayvariables($original_play_id);
        $copyvars = array('system_push_id',
            'user_language', 'system_source', 'onesignal_deviceid',
            'system_push_plattform', 'perm_push', 'lat', 'lon', 'device',
            'screen_height', 'screen_width');
        $newvars = $extravars;

        foreach ($copyvars as $var) {
            if (isset($original_vars[$var]) AND $original_vars[$var]) {
                $newvars[$var] = $original_vars[$var];
            }
        }

        $newvars['logged_in'] = 0;

        foreach ($newvars as $key => $val) {
            \AeplayVariable::updateWithName($to_playid, $key, $val, $this->appid);
        }

        /* delete the old play if its considered "temporary", ie. the registration is not complete */
        if ($original_play_id AND $to_playid AND $original_play_id != $to_playid) {
            if (!isset($original_vars['reg_phase']) OR $original_vars['reg_phase'] != 'complete') {
                \Aeplay::model()->deleteByPk($original_play_id);
            }
        }

        if ($finish_login) {
            \AeplayVariable::updateWithName($to_playid, 'logged_in', '1', $this->appid);
            \AeplayBranch::closeBranch($this->actionobj->levelid, $to_playid);
        }
    }

}