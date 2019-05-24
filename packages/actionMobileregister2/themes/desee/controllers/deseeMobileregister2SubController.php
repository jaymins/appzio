<?php

class deseeMobileregister2SubController extends Mobileregister2Controller
{
    public $facebookId;

    public function deseePhase1()
    {

        $this->setHeader();

//        $token = 'EAAaxr0WXRroBAPui1ZAUjrATw4rVz3fYpP8iT0RLQGmoJtf9wZBT6uIGZB3CXLE6AZAm4kchbkiEPdBG1XH6bGgj8ZA1FlDF4nZC3BtFS9VOVpv1DZBXgcFhWF36UZASdGthWkkcYmKXqw2bLLuCNQYKLIZBhQ8dIMZBT2DBsYLtRdswaa8J7ZCvbEfZCfzNy1HWQyUZD';
//        $this->saveVariable( 'fb_token', $token );
//        return false;

        // We shouldn't really get into this state
        if (!isset($this->varcontent['fb_token'])) {
            // $this->data->scroll[] = $this->getText('Missing Facebook Token', array('style' => 'register-text-step-error'));
            return false;
        }

        $facebookData = ThirdpartyServices::getCompleteFBInfo($this->varcontent['fb_token']);
        $this->facebookId = $facebookData->id;

        $this->setExtraProfileData($facebookData);

        $cache = Appcaching::getGlobalCache('location-asked' . $this->playid);

        if (!$cache AND $this->getConfigParam('ask_for_location')) {
            $buttonparams = new StdClass();
            $buttonparams->action = 'submit-form-content';
            $buttonparams->id = 'save-variables';
            $this->data->onload[] = $buttonparams;

            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache('location-asked' . $this->playid, true);
        }

        $this->setProfilePic();
        $this->setSex();
        $this->setPreferences();
        if (!$this->getVariable('age')) {
            $this->getBirthYearField();
        }
        $this->setProfileComment();
        $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');

        $this->data->footer[] = $this->getHairline('#ffffff');

        if (!$lat AND $cache AND $location) {
            $this->geolocateMe();
            $error = true;
            $locationerror = true;
        }

        if ($this->menuid == 'submit-step-1') {

            $this->saveVariables();

            $terms = $this->getSavedVariable('terms_accepted');
            $profilepic = $this->getSavedVariable('profilepic');
            $profilecomment = $this->getSavedVariable('profile_comment');

            $error = false;

            if (!$terms) {
                $this->data->footer[] = $this->getText('{#please_accept_the_terms#}', array('style' => 'register-text-step-2'));
                $error = true;
            }

            if (!$profilepic) {
                $this->data->footer[] = $this->getText('{#please_add_a_profile_pic#}', array('style' => 'register-text-step-2'));
                $error = true;
            }

            if (!$profilecomment) {
                $this->data->footer[] = $this->getText('{#please_enter_your_description#}', array('style' => 'register-text-step-2'));
                $error = true;
            }

            if ($error == false) {
                $this->data->scroll = array();
                $this->deseePhase2();
                return true;
            }
        }

        /* if there is location error, we will provide a locate button instead of this */
        if (!isset($locationerror)) {
            $this->data->footer[] = $this->getTextbutton('{#continue#}', array('style' => 'desee_general_button_style_footer', 'id' => 'submit-step-1'));
        }

        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache($this->playid . '-' . 'registration', true);

        return true;
    }

    public function deseePhase2()
    {

        if ($this->menuid == 'complete-step-2') {
            $this->saveVariable('hide-from-fb-friends', false);
            $this->rewriteActionConfigField('background_image_portrait', '');
            $this->completeRegistration();
            return true;
        }

        if ($this->menuid == 'set-hidden-friends') {
            $this->saveVariable('hide-from-fb-friends', true);
            $this->rewriteActionConfigField('background_image_portrait', '');
            $this->completeRegistration();
            return true;
        }

        // Get fb friends using the app -> map ids
        $friends = ThirdpartyServices::getUserFbFriends($this->getSavedVariable('fb_token'), $this->appinfo->fb_api_id, $this->appinfo->fb_api_secret);
        $friendIds = array_map(function($friend) {
            return $friend['id'];
        }, $friends);

        $this->saveVariable('facebook-friends-ids', json_encode($friendIds));

        $facebookIds = implode(', ', $friendIds);

        if ( empty($facebookIds) ) {
            $this->saveVariable('hide-from-fb-friends', false);
            $this->rewriteActionConfigField('background_image_portrait', '');
            $this->completeRegistration();
            return true;
        }

        $sql = "
            SELECT
                ae_game_play_variable.id,
                ae_game_play_variable.value,
                ae_game_variable.id,
                ae_game_variable.name,
                ae_game_play_variable.play_id
            FROM ae_game_play_variable
            LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
    
            WHERE `value` IN ($facebookIds)
            AND ae_game_variable.`name` = 'fb_id'
            AND ae_game_variable.game_id = $this->gid
            
            ORDER BY play_id DESC
        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        foreach ($rows as $row) {
            $playId = $row['play_id'];
            $facebookFriendIds = json_decode($this->getPlayVariables($playId)['facebook-friends-ids']);
            $facebookFriendIds[] = $this->facebookId;
            $this->saveRemoteVariable('facebook-friends-ids', json_encode($facebookFriendIds), $playId);
        }

        $this->saveVariable('reg_phase', 2);

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('fb-icon-new.png', array(
                'width' => '50'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '230 0 0 0'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getText('{#do_you_want_us_to_hide_you_from_your_facebook_friends#}', array(
                'color' => '#000000',
                'font-size' => '22',
                'text-align' => 'center',
                'width' => $this->screen_width / 1.2
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '40 0 0 0'
        ));

        $this->data->scroll[] = $this->getColumn(array(
            $this->getTextbutton('Yes', array(
                'id' => 'set-hidden-friends',
                'background-color' => '#FFC204',
                'color' => '#000000',
                'border-radius' => '3',
                'text-align' => 'center'
            )),
            $this->getTextbutton('No, let them see me here on Desee', array(
                'id' => 'complete-step-2',
                'background-color' => 'transparent',
                'text-align' => 'center',
                'color' => '#000000'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '50 30 0 30'
        ));
    }

    public function deseePhase3()
    {
        $this->rewriteActionField('background_image_portrait', '');

        if ($this->menuid == 'complete-registration') {
            $hiddenIds = array_filter($this->submitvariables, function ($item) {
                return $item == 1;
            });

            $hiddenIds = array_keys($hiddenIds);

            $this->saveVariable('hidden_friends', json_encode($hiddenIds));

            if (!$this->getVariable('birth_year')) {
                $this->menuid = '';
                $this->saveVariable('reg_phase', 4);
                $this->deseePhase4();
                return true;
            }

            $this->completeRegistration();
            return true;
        }

        $this->data->header[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getText('{#select_your_friends_from_which_to_be_hidden#}', array(
                    'color' => '#ff6600',
                    'font-size' => '28'
                ))
            ), array(
                'text-align' => 'left',
                'padding' => '40 20 20 20',
                'background-color' => '#ffffff',
                'shadow-color' => '#33000000',
                'shadow-radius' => 3,
                'shadow-offset' => '0 1'
            )),
            $this->getRow(array(
                $this->getImage('search_glass.png', array(
                    'width' => '25',
                    'margin' => '7 15 0 0'
                )),
                $this->getFieldtext('', array(
                    'hint' => '{#search_by_name#}',
                    'submit_on_entry' => '1',
                    'variable' => 'search',
                    'keep-open' => 1
                ))
            ), array(
                'padding' => '10 10 10 10',
            ))
        ), array(
            'background-color' => '#F9FAFB'
        ));

        $friendName = isset($this->submitvariables['search']) ? $this->submitvariables['search'] : '';

        $friends = ThirdpartyServices::getUserFbFriends($this->getSavedVariable('fb_token'), $this->appinfo->fb_api_id, $this->appinfo->fb_api_secret);

        $friends = array_map(function ($friend) use ($friendName) {
            if (!stristr($friend['name'], $friendName) && !empty($friendName)) {
                $friend['filtered'] = 1;
            }

            return $friend;
        }, $friends);

        if (empty($friends)) {
            $this->data->scroll[] = $this->getText('{#no_friends_found#}', array(
                'margin' => '10 10 10 10',
                'text-align' => 'center'
            ));
        }

        foreach ($friends as $friend) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage($friend['picture']['data']['url'], array(
                    'width' => '50',
                    'height' => '50',
                    'crop' => 'yes',
                    'border-radius' => '3',
                )),
                $this->getText($friend['name'], array(
                    'margin' => '0 0 0 10'
                )),
                $this->getFieldonoff(0, array(
                    'margin' => '10 20 0 0',
                    'floating' => true,
                    'float' => 'right',
                    'variable' => $friend['id'],
                    'type' => 'toggle'
                ))
            ), array(
                'padding' => '10 10 10 10',
                'visibility' => isset($friend['filtered']) ? 'hidden' : ''
            ));

            $this->data->scroll[] = $this->getHairline('#dadada', array(
                'visibility' => isset($friend['filtered']) ? 'hidden' : ''
            ));
        }

        $this->data->footer = array();
        $this->data->footer[] = $this->getTextbutton('{#submit#}', array(
            'id' => 'complete-registration',
            'style' => 'desee_general_button_style_footer'
        ));

        return true;
    }

    public function deseePhase4()
    {
        if ($this->menuid == 'complete-registration') {
            $this->saveVariable('birth_year', $this->submitvariables['year']);
            $this->completeRegistration();
            return true;
        }

        $this->data->scroll[] = $this->getBirthYearField();

        $this->data->footer[] = $this->getTextbutton('{#submit#}', array(
            'id' => 'complete-registration',
            'style' => 'desee_general_button_style_footer'
        ));
    }

    public function getBirthYearField()
    {
        $this->data->scroll[] = $this->getRow(array(
            $this->getText('{#please_indicate_which_year_you_were_born#}.', array(
                'width' => '80%',
                'text-align' => 'center',
                'font-size' => '16',
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '10 10 10 10'
        ));

        $year = $this->getSubmittedVariableByName('birth_year');
        $minLegalYear = date('Y') - 18;

        if (!empty($year)) {
            $yearContent = $year;
        } else {
            $yearContent = $minLegalYear;
        }

        $years = '';

        for ($i = $minLegalYear; $i > 1920; $i--) {
            $years .= "$i;$i; ";
        }

        $this->data->scroll[] = $this->getFieldlist($years, array(
            'variable' => 'birth_year',
            'value' => $yearContent,
            'height' => '100',
        ));
    }

    public function setSex()
    {
        if (!$this->getConfigParam('require_sex')) {
            return false;
        }

        $gender = $this->getSavedVariable('gender');

        $this->copyAssetWithoutProcessing('ic_male_on.png');
        $this->copyAssetWithoutProcessing('ic_male_off.png');
        $this->copyAssetWithoutProcessing('ic_female_on.png');
        $this->copyAssetWithoutProcessing('ic_female_off.png');

        $this->data->scroll[] = $this->getText('{#select_your_gender#}', array(
            'text-align' => 'center',
            'font-weight' => '18',
            'margin' => '20 0 5 0',
        ));

        $maleSelectedState = array(
            'style' => 'male_selected_state',
            'variable_value' => 'man',
            'allow_unselect' => 1,
            'animation' => 'fade'
        );

        if ( $gender == 'man' OR $gender == 'male' ) {
            $maleSelectedState['active'] = 1;
        }

        $genders[] = $this->getColumn(array(
            $this->getText('', array('style' => 'male_default_state', 'variable' => 'gender', 'selected_state' => $maleSelectedState))
        ), array('padding' => '5 5 5 5', 'text-align' => 'center'));

        $femaleSelectedState = array(
            'style' => 'female_selected_state',
            'variable_value' => 'woman',
            'allow_unselect' => 1,
            'animation' => 'fade'
        );

        if ( $gender == 'woman' OR $gender == 'female' ) {
            $femaleSelectedState['active'] = 1;
        }

        $genders[] = $this->getColumn(array(
            $this->getText('', array('style' => 'female_default_state', 'variable' => 'gender', 'selected_state' => $femaleSelectedState))
        ), array('padding' => '5 5 5 5', 'text-align' => 'center'));

        $this->data->scroll[] = $this->getRow($genders, array(
            'text-align' => 'center',
            'margin' => '0 0 20 0'
        ));

        return true;
    }

    public function setProfilePic()
    {
        $onclick = new stdClass();
        $onclick->action = 'upload-image';
        $onclick->sync_upload = true;
        $onclick->max_dimensions = '900';
        $onclick->variable = $this->vars['profilepic'];

        if ($this->getConfigParam('require_photo')) {
            if (isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']) {
                $pic = $this->varcontent['fb_image'];
            } elseif (isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
                $pic = $this->varcontent['profilepic'];
            } else {
                $pic = 'filmstrip-placeholder.png';
            }

            //$this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '600','imgcrop'=>'yes'));
            $img[] = $this->getImage($pic, array('variable' => $this->getVariableId('profilepic'), 'crop' => 'round', 'width' => '150', 'text-align' => 'center', 'floating' => "1", 'float' => 'center',
                'border-width' => '5', 'border-color' => '#ffffff', 'border-radius' => '75', 'onclick' => $onclick, 'priority' => 9));
            $img[] = $this->getText(' ');

            $this->data->scroll[] = $this->getColumn($img, array('text-align' => 'center', 'height' => '150', 'margin' => '20 0 8 0', 'floating' => "1", 'float' => 'center'));
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage('ic_upload_pic.png', array(
                    'width' => '30',
                    'margin' => '0 0 0 85',
                    'onclick' => $onclick
                ))
            ), array(
                'text-align' => 'center',
                'margin' => '-40 0 20 0'
            ));
            $this->data->scroll[] = $this->getText($this->getVariable('name'), array('style' => 'desee_register_name'));

            if ($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')) {
                $this->data->scroll[] = $this->getText('{#uploading#} ...', array('style' => 'uploading_text'));
            }
        }
    }

    public function setProfileComment()
    {
        if (!$this->getConfigParam('require_comment')) {
            return false;
        }

        $comment = $this->getSubmittedVariableByName('profile_comment');

        if (!empty($comment)) {
            $commentcontent = $comment;
        } else {
            $commentcontent = '';
        }

        $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array(
            'variable' => $this->getVariableId('profile_comment'),
            'hint' => 'Describe yourself ({#required#})',
            'font-size' => '16',
            'font-style' => 'normal',
            'background-color' => '#ffffff',
            'color' => '#000000',
            'border-radius' => '4',
            'border-width' => '1',
            'border-color' => '#DADADA',
            'padding' => '10 10 10 10',
            'margin' => '5 40 20 40'
        ));
    }

    public function setTerms()
    {
        if (!$this->getConfigParam('require_terms')) {
            return false;
        }

        // $this->registerTermsDiv();

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getConfigParam('terms_popup');
        $onclick->config = $this->getConfigParam('terms_popup');
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = '1';

//        $onclick = new stdClass();
//        $onclick->action = 'show-div';
//        $onclick->div_id = 'open_terms';
//        $onclick->tap_to_close = 1;
//        $onclick->transition = 'fade';
//
//        $onclick->layout = new stdClass();
//        $onclick->layout->top = 100;
//        $onclick->layout->right = 10;
//        $onclick->layout->left = 10;
//        $onclick->background = 'blur';

        $this->copyAssetWithoutProcessing('circle_non.png');
        $this->copyAssetWithoutProcessing('circle_selected.png');

        $selectedState = array('style' => 'desee_register_radio_selected', 'allow_unselect' => 1, 'animation' => 'fade', 'variable_value' => '1');

        if ( $this->getSubmittedVariableByName(  'terms_accepted') ) {
            $selectedState['active'] = 1;
        }

        $columns[] = $this->getRow(array(
            $this->getText('', array(
                'style' => 'desee_register_radio_default',
                'variable' => 'terms_accepted',
                'selected_state' => $selectedState
            ))
        ), array(
            'width' => '10%',
        ));
        $columns[] = $this->getText('I agree to', array(
            'margin' => '0 0 0 0'
        ));
        $columns[] = $this->getText(' Terms & Conditions', array(
            'color' => '#ff6600',
            'onclick' => $onclick,
        ));
        $this->data->scroll[] = $this->getRow($columns, array(
            'text-align' => 'center',
        ));
    }

    public function finishUp()
    {
        if (!$this->getSavedVariable('gender')) {
            $this->saveVariable('gender', 'man');
        }

        $this->updateLocalRegVars();
        $this->beforeFinishRegistration();

        if (!$this->getConfigParam('require_match_entry')) {
            return false;
        }

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false, __FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent, $this->gid, $this->playid);
    }

    public function registerTermsDiv() {
        $closeDiv = new stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->div_id = 'open_terms';

        $this->data->divs['open_terms'] = $this->getColumn(array(
            $this->getText('Some text goes here', array(
                'background-color' => 'C7003A',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center',
                'width' => '100%',
            )),
        ), array(
            'width' => '100%',
        ));
    }

    public function updateLocalRegVars()
    {
        // If user already completed the registration process
        if ($this->getSavedVariable('reg_phase') == 'complete') {
            return false;
        }

        $this->loadVariableContent(true);
        $gender = $this->getSavedVariable('gender');

        if ($gender == 'man') {
            $this->saveVariable('men', '0');
            $this->saveVariable('women', 1);
            $this->saveVariable('look_for_women', 1);
            $this->saveVariable('look_for_men', '0');
        } else if ($gender == 'woman') {
            $this->saveVariable('men', 1);
            $this->saveVariable('women', '0');
            $this->saveVariable('look_for_women', '0');
            $this->saveVariable('look_for_men', 1);
        }

        // $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
        $this->saveVariable('filter_distance', 50);
    }

    public function setExtraProfileData($fb_data = false)
    {

        if (empty($fb_data)) {
            return false;
        }

        if (isset($fb_data->name)) {
            $this->saveVariable('name', $fb_data->name);
            $this->saveVariable('real_name', $fb_data->name);
        }

        if (isset($fb_data->email)) {
            $this->saveVariable('email', $fb_data->email);
        }

        if (isset($fb_data->birthday)) {
            $birth_stamp = strtotime($fb_data->birthday);
            $age = $this->computeAge($birth_stamp, time());
            $this->saveVariable('age', $age);
        }

        if (isset($fb_data->gender)) {
            $gender = $fb_data->gender;
            $gender = ($gender == 'male' ? 'man' : 'woman');
            $this->saveVariable('gender', $gender);
        }

    }

    public function computeAge($starttime, $endtime)
    {
        $age = date('Y', $endtime) - date('Y', $starttime);

        //if birthday didn't occur that last year, then decrement
        if (date('z', $endtime) < date('z', $starttime)) $age--;

        return $age;
    }

    public function geolocateMe()
    {
        $this->data->footer[] = $this->getText('There was an error locating you. If you disabled the location permission, go to settings and enable location for this app. ', array('style' => 'register-text-step-2'));
        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1, $btnaction2);
        $buttonparams['style'] = 'desee_general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        $this->data->footer[] = $this->getText('Locate me', $buttonparams);
    }

    private function completeRegistration()
    {
        $this->closeLogin();
        $this->saveGenderPreference();
        $this->data->scroll = array();
        $this->data->scroll[] = $this->getSpacer('100');
        $this->data->scroll[] = $this->getText('{#loading_matches#}...', array('style' => 'register-text-step-2'));
        $this->data->footer = array();
        $this->finishUp();
        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;
    }

    private function saveGenderPreference()
    {
        $maleVariations = array('man', 'men', 'male');
        $gender = in_array($this->getVariable('gender'), $maleVariations) ? 'men' : 'women';
        $this->saveVariable($gender, '1');
    }

    public function setHeader() {

        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#welcome#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));
        $this->data->header[] = $this->getImage('header-shadow-white.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        return true;
    }

}