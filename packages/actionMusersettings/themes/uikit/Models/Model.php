<?php


namespace packages\actionMusersettings\themes\uikit\Models;

use packages\actionMitems\themes\uikit\Models\MeterModel;
use packages\actionMmenus\themes\queerbff\Models\Profileprogress;
use packages\actionMquiz\Models\QuizSetModel;
use packages\actionMusersettings\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{

    use Profileprogress;

    protected $description;
    protected $birth_year;

    public function saveCheckboxes($nameList, $prefix = '')
    {

        $list = explode(',', $this->getGlobalVariableByName($nameList));
        $saved = [];
        foreach ($list as $item) {

            if ($this->getSubmittedVariableByName($prefix . $nameList . '-' . trim($item, '{#}'))) {
                $saved[] = trim($item, '{#}');
            }
        }

        if ($nameList == 'level' && $this->getSubmittedVariableByName($prefix . $nameList)) {
            $saved[] = $this->getSubmittedVariableByName($prefix . $nameList);

        }

        if (empty($saved)) {
            $this->validation_errors[$prefix . $nameList] = '{#you_must_select_at_least_one#}';
        }

        if (!$this->validation_errors) {
            $variables[$prefix . $nameList] = implode(',', $saved);
            $this->saveNamedVariables($variables);
        }

        if ($prefix && empty($saved)) {
            $variables[$prefix . $nameList] = ' ';
            $this->saveNamedVariables($variables);
        }
    }

    public function validateSettings()
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

                case 'email':
                    $this->email = strtolower($var);

                    if (!$this->validateEmail($this->email)) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_email#}';
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

    /* save to variables */
    public function saveSettings()
    {

        $vars['email'] = $this->email;
        $vars['firstname'] = $this->firstname;
        $vars['lastname'] = $this->lastname;
        $vars['real_name'] = $this->firstname . ' ' . $this->lastname;

        $vars['name'] = $this->firstname;
        $vars['surname'] = $this->lastname;
        $vars['screen_name'] = $this->firstname;

        $vars['receive_notification'] = $this->getSubmittedVariableByName('receive_notification', 0);
        $vars['receive_sms'] = $this->getSubmittedVariableByName('receive_sms', 0);
        $vars['receive_email'] = $this->getSubmittedVariableByName('receive_email', 0);
        $vars['nickname'] = $this->getSubmittedVariableByName('nickname', 0);

        $vars['peak_price'] = $this->getSubmittedVariableByName('peak_price', 0);
        $vars['day_price'] = $this->getSubmittedVariableByName('day_price', 0);
        $vars['night_price'] = $this->getSubmittedVariableByName('night_price', 0);

        $vars['facebook_profile'] = $this->getSubmittedVariableByName('facebook_profile');
        $vars['instagram_profile'] = $this->getSubmittedVariableByName('instagram_profile');
        $vars['twitter_profile'] = $this->getSubmittedVariableByName('twitter_profile');
        
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

        $vars['touch_id'] = $this->getSubmittedVariableByName('touch_id', 0);

        if ($this->getSubmittedVariableByName('profile_comment')) {
            $vars['profile_comment'] = $this->getSubmittedVariableByName('profile_comment');
        }

        if ($this->getSubmittedVariableByName('street')) {
            $vars['street'] = $this->getSubmittedVariableByName('street');
        }
        if ($this->getSubmittedVariableByName('street_address2')) {
            $vars['street_address2'] = $this->getSubmittedVariableByName('street_address2');
        }
        if ($this->getSubmittedVariableByName('city')) {
            $vars['city'] = $this->getSubmittedVariableByName('city');
        }

        /*'show_like_count','hide_my_profile','receive_notification'*/
        $vars['hide_like_count'] = $this->getSubmittedVariableByName('hide_like_count');
        $vars['hide_my_profile'] = $this->getSubmittedVariableByName('hide_my_profile');
        $vars['receive_notification'] = $this->getSubmittedVariableByName('receive_notification');

        $this->saveNamedVariables($vars);
    }

    public function getMyMeters()
    {
        $meters = MeterModel::model()
            ->with('appliances')
            ->findAllByAttributes(
                array('play_id' => $this->playid)
            );

        return $meters;
    }

    public function instagramLoadActive()
    {
        if ($this->sessionGet('insta_loader')) {
            $cache = $this->sessionGet('insta_loader');

            if ($cache + 360 > time()) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    public function instagramLoadButtonActive()
    {

        if ($this->getSavedVariable('instagram_token') AND !$this->getSavedVariable('instagram_media')) {
            return true;
        }

        return false;
    }

    public function getFieldSet($id)
    {
        return @QuizSetModel::model()->with('question')->findAllByAttributes(['quiz_id' => $id]);

    }

    public function getFieldList()
    {
        $params = $this->getAllConfigParams();

        $output = array();

        foreach ($params as $key => $item) {
            if($item != 0){
                $output[] = $key;
            }
        }

        return $output;
    }

}
