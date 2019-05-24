<?php


namespace packages\actionMusersettings\Models;

use Bootstrap\Models\BootstrapModel;
use function is_numeric;
use function str_replace;
use function stristr;
use function strtolower;
use function ucwords;

class Model extends BootstrapModel
{

    public $validation_errors;

    protected $password;
    protected $phone;
    protected $email;
    protected $website;
    protected $firstname;
    protected $lastname;

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

        if ($this->getSubmittedVariableByName('country_selected')) {
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
    public function saveSettings()
    {
        if($this->password){
            $vars['password'] = sha1(strtolower(trim($this->password)));
        }

        $vars['email'] = $this->email;
        $vars['phone'] = $this->phone;
        $vars['firstname'] = $this->firstname;
        $vars['lastname'] = $this->lastname;
        $vars['real_name'] = $this->firstname . ' ' . $this->lastname;

        if ($this->getSubmittedVariableByName('street')) {
            $vars['street'] = $this->getSubmittedVariableByName('street');
        }
        if ($this->getSubmittedVariableByName('street_address2')) {
            $vars['street_address2'] = $this->getSubmittedVariableByName('street_address2');
        }
        if ($this->getSubmittedVariableByName('city')) {
            $vars['city'] = $this->getSubmittedVariableByName('city');
        }
        if ($this->getSubmittedVariableByName('zip')) {
            $vars['zip'] = $this->getSubmittedVariableByName('zip');
        }
        if ($this->getSubmittedVariableByName('county')) {
            $vars['county'] = $this->getSubmittedVariableByName('county');
        }
        if ($this->getSubmittedVariableByName('country')) {
            $vars['country'] = $this->getSubmittedVariableByName('country');
        }

        if ($this->getSubmittedVariableByName('age')) {
            $vars['country'] = $this->getSubmittedVariableByName('age');
        }

        if ($this->getSubmittedVariableByName('profilepic')) {
            $vars['profilepic'] = $this->getSubmittedVariableByName('profilepic');
        }
        if ($this->getSubmittedVariableByName('profilepic2')) {
            $vars['profilepic2'] = $this->getSubmittedVariableByName('profilepic2');
        }
        if ($this->getSubmittedVariableByName('profilepic3')) {
            $vars['profilepic3'] = $this->getSubmittedVariableByName('profilepic3');
        }
        if ($this->getSubmittedVariableByName('profilepic4')) {
            $vars['profilepic4'] = $this->getSubmittedVariableByName('profilepic4');
        }
        if ($this->getSubmittedVariableByName('profilepic5')) {
            $vars['profilepic5'] = $this->getSubmittedVariableByName('profilepic5');
        }

        if ($this->getSubmittedVariableByName('profile_comment')) {
            $vars['profile_comment'] = $this->getSubmittedVariableByName('profile_comment');
        }

        if($this->getSavedVariable('birth_day') AND
            $this->getSavedVariable('birth_year') AND
            $this->getSavedVariable('birth_month')
        ){
            $vars['age'] = $this->convertBirthDateToAge(
                $this->getSavedVariable('birth_year') ,
                $this->getSavedVariable('birth_month'),
                $this->getSavedVariable('birth_day')
            );
        }

        /*'show_like_count','hide_my_profile','receive_notification'*/
        if ($this->getSubmittedVariableByName('hide_like_count')) {
            $vars['hide_like_count'] = $this->getSubmittedVariableByName('hide_like_count');
        }
        if ($this->getSubmittedVariableByName('hide_my_profile')) {
            $vars['hide_my_profile'] = $this->getSubmittedVariableByName('hide_my_profile');
        }
        if ($this->getSubmittedVariableByName('receive_notification')) {
            $vars['receive_notification'] = $this->getSubmittedVariableByName('receive_notification');
        }

        $this->saveNamedVariables($vars);
    }

    /* will save all validated variables and add rest to error array */
    public function validateSettings()
    {
        $vars = $this->getAllSubmittedVariablesByName();

        foreach ($vars as $key => $var) {

            switch ($key) {
                case 'firstname':
                    $this->firstname = strtolower($var);
                    $this->firstname = ucwords($this->firstname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_first_name#}';
                    }
                    break;

                case 'lastname':
                    $this->lastname = strtolower($var);
                    $this->lastname = ucwords($this->lastname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_last_name#}';
                    }
                    break;

                case 'email':
                    $this->email = strtolower($var);

                    if (!$this->validateEmail($this->email)) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_email#}';
                    }
                    break;

                case 'change_password':
                    if(!$this->validatePassword($var)){
                        $this->validation_errors[$key] = '{#password_needs_to_be_at_least_four_characters#}';
                    }

                    break;

                case 'change_password_again':
                    if ($this->getSubmittedVariableByName('change_password') != $var) {
                        $this->validation_errors[$key] = '{#passwords_dont_match#}';
                    } elseif(!$this->validation_errors['change_password']){
                        $this->password = $var;
                    }
                    break;

                case 'password':

                    if ($this->getSubmittedVariableByName('password')) {
                        $this->password = trim($var);
                    }

                    if ($var && !$this->validatePassword($var)) {
                        $this->validation_errors[$key] = '{#password_needs_to_be_at_least_four_characters#}';
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

    /* adds required variables and closes the login */
    public function closeLogin($dologin = true)
    {
        if ($this->getConfigParam('require_login') == 1) {
            return true;
        }

        $this->saveVariable('reg_phase', 'complete');

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

    public function getInstaImages()
    {
        
        if(!$this->getSavedVariable('instagram_token') OR $this->getSavedVariable('instagram_media')){

            if($this->getSavedVariable('instagram_media')){

                if($this->getSavedVariable('profilepic')){
                    $media[] = $this->getSavedVariable('profilepic');
                }

                if($this->getSavedVariable('instagram_profilepic') AND $this->getSavedVariable('instagram_profilepic') != $this->getSavedVariable('profilepic')){
                    $media[] = $this->getSavedVariable('instagram_profilepic');
                }

                $instaimages = json_decode($this->getSavedVariable('instagram_media'),true);

                if(isset($media)){
                    $out = array_merge($media,$instaimages);
                    return array_unique($out);
                } elseif(is_array($instaimages) AND $instaimages) {
                    return array_unique($instaimages);
                } else {
                    return array();
                }
            } else {
                return array();
            }
        }
        
    }

    public function setInstaImages(){
        $apikey = \Aemobile::getConfigParam($this->appid, 'instagram_apikey');
        $apisecret = \Aemobile::getConfigParam($this->appid, 'instagram_secretkey');
        $token = $this->getSavedVariable('instagram_temp_token') ? $this->getSavedVariable('instagram_temp_token') : $this->getSavedVariable('instagram_token');
        $insta = new \InstagramConnector($apikey, $apisecret, $token);
        $media = $insta->get('users/self/media/recent');

        if (isset($media->data) AND $media->data) {
            foreach ($media->data as $item) {
                if (isset($item->link)) {
                    $output[] = $item->link.'?__a=1';
                }
            }
        }

        foreach($output as $link){
            $json = @json_decode(file_get_contents($link));

            /* simple retry */
            if(!$json){
                $json = @json_decode(file_get_contents($link));
            }

            if(isset($json->graphql->shortcode_media->display_url)){
                $highres[] = $json->graphql->shortcode_media->display_url;
            }
        }

        $path = \Controller::getDocumentsPath($this->appid);
        if (!is_dir($path . 'instagram')) {
            mkdir($path . 'instagram', 0777);
        }

        if (isset($highres) AND $highres) {
            foreach ($highres as $item) {
                $img = @file_get_contents($item);

                /* simple retry */
                if(!$img){
                    $img = @file_get_contents($item);
                }

                $imagename = basename($item);
                $explosion = explode('?',$imagename);
                $imagename = $explosion[0];
                $filepath = $path . 'instagram/' . $imagename;

                if ($img) {
                    $images[] = $imagename;
                    file_put_contents($filepath, $img);
                }
            }
        }

        if (isset($images) AND $images) {
            $this->saveVariable('instagram_media', json_encode($images));
        }

        $this->sessionUnset('insta_loader');
        return $images;
    }


}
