<?php

Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobileuserprofileDeseeController extends MobileuserprofileController
{

    public $selectedState = array(
        'style' => 'radio_selected_state',
        'variable_value' => '1',
        'allow_unselect' => 1,
        'animation' => 'fade'
    );

    public $metas;
    public $unmatched_user;

    public function init()
    {
        $this->metas = new MobilematchingmetaModel();
        $this->metas->current_playid = $this->playid;
    }

    public function showProfileEdit()
    {

        $this->setHeader();

        $this->setGridWidths();
        $this->profileEditSaves();
        $this->rewriteActionConfigField('background_color', '#f9fafb');

        if (strstr($this->menuid, 'save-status')) {
            $fields = $this->getAdditionalInformationFieldNames();
            foreach ($fields as $key => $field) {
                $value = $this->getSubmittedVariableByName($key);
                if ($value) {
                    $this->saveVariable($key, $value);
                }
            }
        }

        if (strstr($this->menuid, 'delete-profile')) {
            $this->doLogout();
            Aeplay::deletePlay($this->playid, $this->gid);
            $this->data->onload[] = $this->getOnclick('list-branches');
            return $this->data;
        }

        $this->handleExtrasPayments();

        /* set the screen pixels */
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if ($this->menuid == 'del-images') {
            $user[] = $this->getText('{#click_on_images_to_delete#}', array('vertical-align' => 'top', 'font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user, array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        }

        $this->setCompletionHeader();
        $this->registerProfileDeleteDiv();

        $this->data->scroll[] = $this->getSpacer(20);
        $this->data->scroll[] = $this->getGrid();
        $this->data->scroll[] = $this->getSpacer(20);

        $this->data->scroll[] = $this->getText('{#name#}', array('style' => 'profile_field_label'));
        $this->getTextField('real_name');

        $this->data->scroll[] = $this->getText('{#email#}', array('style' => 'profile_field_label'));
        $this->getTextField('email');

        $this->data->scroll[] = $this->getText('{#about_me#}', array('style' => 'profile_field_label'));
        $this->getTextArea();

        $this->renderBirthYearField();

        $this->data->scroll[] = $this->getText('{#more_about_me#}', array('style' => 'profile_field_label'));

        $this->getSnippet('userpreferences');

        $this->data->scroll[] = $this->getHairline('#e8e9f2');
        $this->data->scroll[] = $this->getSpacer( 10 );

        $this->getHideFromFriendsCheckbox();
        $this->getDistanceCheckbox();
        $this->getAgeCheckbox();

        $onclick = new stdClass();
        $onclick->action = 'show-div';
        $onclick->div_id = 'delete_profile';
        $onclick->tap_to_close = 1;
        $onclick->transition = 'fade';
        $onclick->layout = new stdClass();
        $onclick->layout->top = 100;
        $onclick->layout->right = 10;
        $onclick->layout->left = 10;
        $onclick->background = 'blur';

        $this->data->scroll[] = $this->getText('{#delete_my_account#}', array(
            'style' => 'profile_field_label_delete',
            'onclick' => $onclick
        ));

        $this->data->footer[] = $this->saveButton();
    }

    public function renderBirthYearField()
    {
        $this->data->scroll[] = $this->getText('{#birth_year#}', array('style' => 'profile_field_label'));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');

        $year = $this->getVariable('birth_year');
        $minLegalYear = date('Y') - 18;

        $years = '';

        for ($i = $minLegalYear; $i > 1920; $i--) {
            $years .= "$i;$i; ";
        }

        $this->data->scroll[] = $this->getFieldlist($years, array(
            'variable' => 'birth_year',
            'value' => $year,
            'background-color' => '#ffffff',
            'height' => '100',
        ));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');
    }

    public function doLogout(){
        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText('{#deleting#}...', array(
            'text-align' => 'center',
            'margin' => '50 0 0 0'
        ));
        $this->saveVariable('logged_in','0');

        $complete = new StdClass();
        $complete->action = 'fb-logout';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;

        if($this->getConfigParam('instagram_login_provider')){
            $complete = new StdClass();
            $complete->action = 'open-action';
            $complete->action_config = $this->getConfigParam('instagram_login_provider');
            //$complete->open_popup = 1;
            $this->data->onload[] = $complete;
            //$this->getConfigParam('instagram_login_provider');
        }


    }

    protected function registerProfileDeleteDiv()
    {
        $closeDiv = new stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->div_id = 'delete_profile';

        $deleteProfile = new stdClass();
        $deleteProfile->action = 'submit-form-content';
        $deleteProfile->id = 'delete-profile';

        $clicker = [ $closeDiv, $deleteProfile ];

        $this->data->divs['delete_profile'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => 'C7003A',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getText('When you click on DELETE you will permanently delete your Desee account. All of your chats, shared images and contacts inside the app would be erased forever.
Are you sure that you want to delete your account?', array(
                'padding' => '60 20 60 20',
                'text-align' => 'center'
            )),
            $this->getRow(array(
                $this->getText('Delete', array(
                    'style' => 'desee_default_button_style_half',
                    'onclick' => $clicker,
                )),
                $this->getText('Cancel', array(
                    'style' => 'desee_general_button_style_footer_half',
                    'onclick' => $closeDiv,
                ))
            ), array(
                'width' => '100%'
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'border-radius' => '8',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
        ));
    }

    public function getTextArea()
    {

        $text = '';
        if (!empty($this->getVariable('profile_comment'))) {
            $text = $this->getVariable('profile_comment');
        }

        $this->data->scroll[] = $this->getHairline('#f3f3f3');
        $this->data->scroll[] = $this->getFieldtextarea($text, array(
            'font-size' => '15',
            'padding' => '10 20 10 20',
            'background-color' => '#FFFFFF',
            'variable' => $this->getVariableId('profile_comment'),
        ));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');

    }

    private function getHideFromFriendsCheckbox()
    {
        $args = array(
            'style' => 'radio_default_state',
        );

        $col_args = array(
            'background-color' => '#ffffff',
            'height' => '45',
        );

        if ($this->getVariable('hide-from-fb-friends')) {
            $this->selectedState['active'] = 1;
        }

        $args['selected_state'] = $this->selectedState;
        $args['variable'] = 'hide-from-fb-friends';

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('{#hide_me_from_my_facebook_friends#}', array(
                    'padding' => '5 20 5 20',
                    'color' => '#000000',
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', $args)
                ), array(
                    'style' => 'desee-column-right-aligned'
                )),
            ), $col_args),
        ));

        $this->selectedState['active'] = 0;
    }

    private function getDistanceCheckbox()
    {
        $args = array(
            'style' => 'radio_default_state',
        );

        $col_args = array(
            'background-color' => '#ffffff',
            'height' => '45',
        );

        $is_active = $this->metas->checkMeta('hide-distance');

        if ($is_active) {

            if ($this->getVariable('profile_location_invisible')) {
                $this->selectedState['active'] = 1;
            }

            $args['selected_state'] = $this->selectedState;
            $args['variable'] = 'profile_location_invisible';
        } else {
            $this->registerProductDiv( 'buy-hide-distance', 'm_hide_location.png', '{#distance_invisible#}', 'Make your distance invisible from views on your profile for next 30 days.', 'distance_invisible.01', 'distance_invisible.001' );
            $col_args['onclick'] = $this->showProductDiv( 'buy-hide-distance' );
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('{#make_my_location_invisible#}', array(
                    'padding' => '5 20 5 20',
                    'color' => ($is_active ? '#000000' : '#afafae'),
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', $args)
                ), array(
                    'style' => 'desee-column-right-aligned'
                )),
            ), $col_args),
        ));

        $this->selectedState['active'] = 0;
    }

    private function getAgeCheckbox()
    {
        $args = array(
            'style' => 'radio_default_state',
        );

        $col_args = array(
            'background-color' => '#ffffff',
            'height' => '45',
        );

        $is_active = $this->metas->checkMeta('hide-age');

        if ($is_active) {

            if ($this->getVariable('profile_age_invisible')) {
                $this->selectedState['active'] = 1;
            }

            $args['selected_state'] = $this->selectedState;
            $args['variable'] = 'profile_age_invisible';
        } else {
            $this->registerProductDiv( 'buy-hide-age', 'm_hide_age.png', '{#age_invisible#}', 'Make your age invisible from views on your profile for next 30 days.', 'age_invisible.01', 'age_invisible.001' );
            $col_args['onclick'] = $this->showProductDiv( 'buy-hide-age' );
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('{#make_my_age_invisible#}', array(
                    'padding' => '5 20 5 20',
                    'color' => ($is_active ? '#000000' : '#afafae'),
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', $args)
                ), array(
                    'style' => 'desee-column-right-aligned'
                )),
            ), $col_args),
        ));

        $this->selectedState['active'] = 0;
    }

    private function setCompletionHeader()
    {
        $completion = $this->getProfileCompletion();
        $this->data->scroll[] = $this->getColumn(array(
            $this->getText($completion * 100 . '%', array(
                'text-align' => 'center',
                'color' => '#ff6600',
                'font-size' => 15,
                'margin' => '10 0 0 0',
            )),
            $this->getProgress($completion, array(
                'margin' => '5 20 10 20',
                'progress_color' => '#7ED321',
                'height' => '5',
                'border-radius' => '3',
                'track_color' => '#B7B7B7'
            )),
            $this->getText('{#user_with_100%_profile_completion_gets_more_chance_to_get_matched#}', array(
                'font-size' => '15',
                'padding' => '0 20 20 20',
                'color' => '#ff6600'
            ))
        ), array(
            'background-color' => '#ffffff',
//            'background-image' => $this->getImageFileName('bgr_top_desee.png'),
//            'background-size' => 'cover'
        ));
        $this->data->scroll[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));
    }

    public function getTextField($variable)
    {

        $text = '';
        if (!empty($this->getVariable($variable))) {
            $text = $this->getVariable($variable);
        }

        $this->data->scroll[] = $this->getHairline('#f3f3f3');
        $this->data->scroll[] = $this->getFieldtext($text, array(
            'font-size' => '15',
            'padding' => '10 20 10 20',
            'background-color' => '#FFFFFF',
            'variable' => $this->getVariableId($variable),
        ));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');
    }

    /**
     * Render additional information fields
     */
    public function getAdditionalInformationFields()
    {
        $fields = $this->getAdditionalInformationFieldNames();

        foreach ($fields as $identifier => $field) {
            $this->renderAdditionalInformationField($identifier, $field);
        }
    }

    /**
     * Get additional information field identifiers and names
     *
     * @return array
     */
    protected function getAdditionalInformationFieldNames()
    {
        return array(
            'relationship_status' => 'Status',
            'seeking' => 'They are seeking',
            'religion' => 'Religion',
            'diet' => 'Diet',
            'tobacco' => 'Tobacco',
            'alcohol' => 'Alcohol',
            'zodiac_sign' => 'Zodiac Sign'
        );
    }

    protected function renderAdditionalInformationField(string $identifier, string $field)
    {
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname('profilestatusselect');
        $onclick->id = $identifier;
        $onclick->open_popup = 1;
        $onclick->sync_open = 1;
        $onclick->back_button = 1;
        $onclick->keep_user_data = 1;

        $content = $this->getVariable($identifier);

        if (!empty($content) && $identifier == 'seeking') {
            // Variable value is json, transform it into text
            $content = json_decode($content);
            $content = join(', ', $content);
            $content = substr($content, 0, 10) . '...';
        }

        $this->data->scroll[] = $this->getHairline('#f3f3f3');
        $this->data->scroll[] = $this->getRow(array(
            $this->getText($field, array('style' => 'profile_field_label_additional_info')),
            $this->getRow(array(
                $this->getText($content, array(
                    'style' => 'profile_status_value'
                )),
                $this->getImage('arrow.png', array(
                    'width' => '10',
                ))
            ), array(
                'floating' => 1,
                'float' => 'right',
                'vertical-align' => 'middle',
                'margin' => '0 20 0 0'
            ))
        ), array(
            'onclick' => $onclick,
            'padding' => '0 0 0 0',
            'background-color' => '#FAFAFA'
        ));
    }

    /**
     * Calculate profile completion percentage
     *
     * @return float|int
     */
    public function getProfileCompletion()
    {
        // Starting completion percentage
        $completion = 35;

        // Each additional profile picture gives 3% more to completion
        for ($i = 2; $i <= 6; $i++) {
            $image = $this->getVariable('profilepic' . $i);

            if (!empty($image)) {
                $completion += 3;
            }
        }

        // Additional info field gives 15% more
        $additionalInfo = $this->getVariable('profile_comment');
        if (!empty($additionalInfo)) {
            $completion += 15;
        }

        // Each of the additional information fields give 5% more
        $fields = $this->getAdditionalInformationFieldNames();
        foreach ($fields as $identifier => $name) {
            $variable = $this->getVariable($identifier);
            if (!empty($variable)) {
                $completion += 5;
            }
        }

        return $completion / 100;
    }

    public function saveButton()
    {
        if ($this->menuid == 'save-data') {
            $this->validateAndSave();
            $this->initMobileMatching();
            $this->mobilematchingobj->turnUserToItem(false, __FILE__);
            $cachename = 'uservars-' . $this->playid;
            Appcaching::removeGlobalCache($cachename);
            return $this->getTextbutton('{#saved#}', array('id' => 'save-data', 'style' => 'desee_general_button_style_footer'));
        } else {
            return $this->getTextbutton('{#save#}', array('id' => 'save-data', 'style' => 'desee_general_button_style_footer'));
        }
    }

    public function showProfileView()
    {
        $this->rewriteActionConfigField('mobile_sharing', 1);

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();
        $this->sessionSet('lastOpenedUser', $this->profileid);

        $this->registerDivs();

        if (strstr($this->menuid, 'unhide_user_')) {
            $facebookId = str_replace('unhide_user_', '', $this->menuid);
            $this->unhideUser($facebookId);

            $this->data->onload[] = $this->getCloseDivAction('unhide');
        } else if (strstr($this->menuid, 'hide_user_')) {
            $facebookId = str_replace('hide_user_', '', $this->menuid);
            $this->hideUser($facebookId);

            $this->data->onload[] = $this->getCloseDivAction('hide');
        } else if (strstr($this->menuid, 'unblock_user_')) {
            $id = str_replace('block_user_', '', $this->menuid);
            $this->unblockUser($id);

            $this->data->onload[] = $this->getCloseDivAction('unblock');
        } else if (strstr($this->menuid, 'block_user_')) {
            $id = str_replace('block_user_', '', $this->menuid);
            $this->blockUser($id);

            $this->data->onload = array(
                // $this->getCloseDivAction('block'),
                $this->getOnclick('go-home'),
                $this->getOnclick('list-branches'),
            );
        } else if (strstr($this->menuid, 'unmatch_user_')) {
            $id = str_replace('unmatch_user_', '', $this->menuid);
            $this->unmatchUser($id);

            $this->data->onload[] = $this->getCloseDivAction('unmatch');
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);
        $name = isset($vars['real_name']) ? $vars['real_name'] : '{#profile#}';
        $this->rewriteActionField('subject', $name);

        $metas = new MobilematchingmetaModel();
        $metas->current_playid = $this->profileid;

        $unmatchedUsers = json_decode($this->getVariable('unmatched_me'));

        if (!empty($unmatchedUsers)) {
            foreach ($unmatchedUsers as $unmatchedUser) {
                if ($unmatchedUser[0] == $this->profileid) {
                    $this->unmatched_user = $unmatchedUser;
                    break;
                }
            }
        }

        if (strstr($this->menuid, 'yes_')) {
            $this->matchUser($this->profileid);
            $this->goBack();
        } else if (strstr($this->menuid, 'no_')) {
            $this->initMobileMatching($this->profileid);
            $this->mobilematchingobj->skipMatch();
            $this->goBack();
        }

        $this->data->scroll[] = $this->getImageScroll($vars);

        $this->getDivs();

        if (isset($vars['birth_year'])) {
            $txt2 = date('Y') - $vars['birth_year'];
        } else if ( isset($vars['age']) AND $vars['age'] ) {
	        $txt2 = $vars['age'];
        } else {
            $txt2 = 'N/A';
        }

        if (isset($vars['real_name']) AND $vars['real_name']) {
            $name = $vars['real_name'];
        } else if (isset($vars['name']) AND $vars['name']) {
            $name = $vars['name'];
        } else {
            $name = '{#anonymous#}';
        }

        $title_text = $name . ', ' . $txt2;
        if (
            $metas->checkMeta('hide-age') AND
            (isset($vars['profile_age_invisible']) AND $vars['profile_age_invisible'])
        ) {
            $title_text = $name;
        }

        $info[] = $this->topRowData($title_text, $metas);

        $distance = $this->sessionGet('distance-' . $this->profileid) > 0 ? $this->sessionGet('distance-' . $this->profileid) : 0;
        $distance_text = $distance . ' km';

        if (
            // $metas->checkMeta('hide-distance') AND
            (isset($vars['profile_location_invisible']) AND $vars['profile_location_invisible'])
        ) {
            $distance_text = '';
        }

        if ($distance_text) {
            $info[] = $this->getRow(array(
                $this->getImage('ic_location_sm.png', array('width' => '23', 'margin' => '0 5 0 0')),
                $this->getText($distance_text, array('font-size' => '14')),
            ), array(
                'margin' => '5 20 0 15',
                'vertical-align' => 'middle',
            ));
        }

        $lastLogin = $this->getLastLogin($vars);
        $info[] = $this->getRow(array(
            $this->getImage('ic_status_online.png', array('width' => '23', 'margin' => '0 5 0 0')),
            $this->getText($lastLogin, array('font-size' => '14'))
        ), array(
            'margin' => '0 20 0 15',
            'vertical-align' => 'middle',
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn($info, array(
                'width' => '100%',
                'id' => 'profile-info-holder',
                'dynamic_content' => 1,
            )),
        ), array(
            'width' => '100%',
        ));

        $this->renderStatusOptions($vars);
        $this->profileCommentView($vars);

        $matches = $this->mobilematchingobj->getMyMatches();

        if (!in_array($this->profileid, $matches) && !$this->isUnmatched()) {
            $this->data->scroll[] = $this->getBtns($this->profileid);
        }

        if ($this->userIsUnmatched()) {
            $this->chatAddon();
        }

    }

    protected function registerDivs()
    {
        $vars = $this->getPlayVariables($this->playid);

        // Most probably the Profile of the user is not completed at this point
        if ( !isset($vars['name']) OR !isset($vars['fb_id']) ) {
            return false;
        }

        $this->data->divs['div_hide_me_from'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => '#FF6600',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getText('Are you sure you want to hide from ' . $vars['name'] . '?', array(
                'padding' => '60 20 60 20',
                'text-align' => 'center'
            )),
            $this->getText('Hide', array(
                'style' => 'desee_general_button_style_footer',
                'onclick' => $this->getOnclick('id', false, 'hide_user_' . $vars['fb_id'])
            ))
        ), $this->getDivArgs());

        $this->data->divs['div_unhide'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => 'FF6600',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getText('Are you sure you want to unhide from ' . $vars['name'] . '?', array(
                'padding' => '60 20 60 20',
                'text-align' => 'center'
            )),
            $this->getText('Unhide', array(
                'style' => 'desee_general_button_style_footer',
                'onclick' => $this->getOnclick('id', false, 'unhide_user_' . $vars['fb_id'])
            ))
        ), $this->getDivArgs());

        // If images are not prepared up front they will not be rendered
        $this->copyAssetWithoutProcessing('circle_non_bg.png');
        $this->copyAssetWithoutProcessing('circle_selected_bg.png');

        $reasons = array(
            '{#offensive_photos#}',
            '{#fake_profile#}',
            '{#offensive_description#}',
            '{#other_reasons#}'
        );
        $selectedState = array('style' => 'radio_selected_state', 'allow_unselect' => 1, 'animation' => 'fade');

        $blockReasons = array();

        foreach ($reasons as $i => $reason) {
            $selectedState['variable_value'] = $reason;

            $blockReasons[] = $this->getRow(array(
                $this->getText(ucfirst($reason), array(
                    'padding' => '15 10 15 20'
                )),
                $this->getRow(array(
                    $this->getText('', array(
                        'style' => 'radio_default_state',
                        'variable' => 'block_reason',
                        'selected_state' => $selectedState
                    ))
                ), array(
                    'width' => '40%',
                    'floating' => 1,
                    'float' => 'right'
                ))
            ));

            if ( ($i+1) < count($reasons) ) {
                $blockReasons[] = $this->getHairline('#f3f3f3');
            }
        }

        $this->data->divs['div_block'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => 'FF6600',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getColumn($blockReasons),
            $this->getText('Block', array(
                'style' => 'desee_general_button_style_footer',
                'onclick' => $this->getOnclick('id', false, 'block_user_' . $this->profileid)
            ))
        ), $this->getDivArgs());

        $this->data->divs['div_unblock'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => 'C7003A',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getText('Are you sure you want to unblock ' . $vars['name'] . '?', array(
                'padding' => '60 20 60 20',
                'text-align' => 'center'
            )),
            $this->getText('Unblock', array(
                'style' => 'desee_general_button_style_footer',
                'onclick' => $this->getOnclick('id', false, 'unblock_user_' . $this->profileid)
            ))
        ), $this->getDivArgs());

        $this->data->divs['div_unmatch'] = $this->getColumn(array(
            $this->getText('Are you sure?', array(
                'background-color' => 'C7003A',
                'color' => '#FFFFFF',
                'padding' => '20 20 20 20',
                'text-align' => 'center'
            )),
            $this->getText('Are you sure you want to unmatch ' . $vars['name'] . '?', array(
                'padding' => '60 20 60 20',
                'text-align' => 'center'
            )),
            $this->getText('Unmatch', array(
                'style' => 'desee_general_button_style_footer',
                'onclick' => $this->getOnclick('id', false, 'unmatch_user_' . $this->profileid)
            ))
        ), $this->getDivArgs());
    }

    private function unmatchUser($playId)
    {
        $this->mobilematchingobj->removeTwoWayMatches(false);

        $unmatchedUsers = json_decode($this->getVariable('unmatched_users'));
        $isUnmatched = false;

        if (!empty($unmatchedUsers)) {
            foreach ($unmatchedUsers as $unmatchedUser) {
                if ($unmatchedUser[0] == $playId) {
                    $isUnmatched = true;
                }
            }
        }

        if (!$isUnmatched) {
            $this->addToVariable('unmatched_users', array($playId, time()));
        }

        $otherUserVariables = $this->getPlayVariables($playId);

        if (isset($otherUserVariables['unmatched_me'])) {
            $otherUserUnmatchers = json_decode($otherUserVariables['unmatched_me']);
        } else {
            $otherUserUnmatchers = array();
        }

        $otherUserUnmatchers[] = array($this->playid, time());
        $this->saveRemoteVariable('unmatched_me', json_encode($otherUserUnmatchers), $playId);

        /*$notificationTitle = 'You have been unmatched';
        $notificationText = $this->getVariable('real_name') . ' has unmatched you';
        Aenotification::addUserNotification($playId, $notificationTitle, $notificationText, 0, $this->gid);*/
    }

    protected function getCloseDivAction($option)
    {
        $action = new stdClass();
        $action->action = 'hide-div';
        $action->div_id = 'div_' . $option;

        return $action;
    }

    protected function hideUser($facebookId)
    {
        $hiddenUsers = json_decode($this->getVariable('hidden_users'));

        if (is_null($hiddenUsers) || !in_array($facebookId, $hiddenUsers)) {
            $this->addToVariable('hidden_users', $facebookId);
        }
    }

    protected function unhideUser($facebookId)
    {
        $hiddenUsers = json_decode($this->getVariable('hidden_users'));

        if (!empty($hiddenUsers)) {
            $hiddenUsers = array_filter($hiddenUsers, function ($user) use ($facebookId) {
                return $user != $facebookId;
            });

            $this->saveVariable('hidden_users', $hiddenUsers);
        }
    }

    protected function blockUser($playId)
    {
        $this->initMobileMatching($playId);
        $this->mobilematchingobj->removeTwoWayMatches(false);
        $this->mobilematchingobj->skipMatch();

        $blockedUsers = json_decode($this->getVariable('blocked_users'));
        $isBlocked = false;

        if (!empty($blockedUsers)) {
            foreach ($blockedUsers as $blockedUser) {
                if ($blockedUser[0] == $playId) {
                    $isBlocked = true;
                }
            }
        }

        if (!$isBlocked) {
            $this->addToVariable('blocked_users', array(
                $playId,
                time(),
                $this->submitvariables['block_reason'],
            ));
        }
    }

    protected function unblockUser($playId)
    {
        $blockedUsers = json_decode($this->getVariable('blocked_users'));

        $blockedUsers = array_filter($blockedUsers, function ($user) use ($playId) {
            return $user[0] == $playId;
        });

        $this->saveVariable('blocked_users', $blockedUsers);
    }

    protected function getDivs()
    {
        $options = array(
            'block',
            'hide',
            'unmatch',
        );

        $divs = array();
        foreach ($options as $option) {
            $divs[] = $this->getDivButton($option);
            $divs[] = $this->getHairline('#f3f3f3');
        }
        array_pop($divs);

        $this->data->divs['div1'] = $this->getColumn($divs, array(
            'background-color' => '#FFFFFF',
            'width' => $this->screen_width / 2.7,
            'border-radius' => '5',
            'shadow-color' => '#000000',
            'shadow-radius' => '4',
            'shadow-offset' => '0 3'
        ));

        $onclick = new stdClass();
        $onclick->action = 'show-div';
        $onclick->div_id = 'div1';
        $onclick->tap_to_close = 1;
        $onclick->transition = 'fade';
        $onclick->layout = new stdClass();
        $onclick->layout->top = $this->screen_height / 16;
        $onclick->layout->right = $this->screen_width / 8;

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('desee-back-arrow-2.png', array(
                'width' => '30',
                'margin' => '0 0 0 0',
                'onclick' => $this->getOnclick('go-home', true)
            )),
            $this->getImage('desee-block.png', array(
                'width' => '35',
                'floating' => 1,
                'float' => 'right',
                'onclick' => $onclick
            ))
        ), array(
            'margin' => '-' . $this->screen_height / 1.9 . ' 0 ' . $this->screen_height / 2.1 . ' 0',
            'padding' => '0 20 0 20'
        ));
    }

    protected function getDivButton($option)
    {
        $vars = $this->getPlayVariables($this->playid);

        switch ($option) {
            case 'unmatch':
                $matches = $this->mobilematchingobj->getMyMatches();
                if (!in_array($this->profileid, $matches)) {
                    return $this->getDisabledDivButton($option);
                }
                break;
            case 'hide':
                $option = 'hide_me_from';
                $matches = $this->mobilematchingobj->getMyMatches();
                if (in_array($this->profileid, $matches)) {
                    return $this->getDisabledDivButton($option);
                }
                $hiddenUsers = json_decode($this->getVariable('hidden_users'));
                if (!empty($hiddenUsers) && in_array($vars['fb_id'], $hiddenUsers)) {
                    $option = 'unhide';
                }
                break;
            case 'block':
                $blockedUsers = json_decode($this->getVariable('blocked_users'));
                if (!empty($blockedUsers)) {
                    foreach ($blockedUsers as $user) {
                        if ($user[0] == $this->profileid) {
                            $option = 'unblock';
                            break;
                        }
                    }
                }
                break;
        }

        $onclick = new stdClass();
        $onclick->action = 'show-div';
        $onclick->div_id = 'div_' . $option;
        $onclick->tap_to_close = 1;
        $onclick->transition = 'from-bottom';
        $onclick->background = 'blur';
        $onclick->layout = new stdClass();
        $onclick->layout->top = 100;
        $onclick->layout->right = 10;
        $onclick->layout->left = 10;

        $closeDiv = new stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->div_id = 'div1';

        $clicker = array();
        $clicker[] = $onclick;
        $clicker[] = $closeDiv;

        return $this->getText(ucfirst('{#' . $option . '#}'), array(
            'padding' => '10 10 10 10',
            'color' => '#C7003A',
            'onclick' => $clicker
        ));
    }

    protected function getDisabledDivButton($option)
    {
        return $this->getText('{#'. $option .'#}', array(
            'padding' => '10 10 10 10',
            'color' => '#DADADA',
        ));
    }

    public function chatAddon()
    {

        if ($this->metas->checkMeta('chat-with-blocked')) {

            $chat_btn = new stdClass();
            $chat_btn->action = 'open-action';
            $chat_btn->id = $this->getTwoWayChatId($this->profileid, $this->playid);
            $chat_btn->back_button = true;
            $chat_btn->sync_open = true;
            $chat_btn->sync_close = true;
            $chat_btn->viewport = 'bottom';
            $chat_btn->action_config = $this->getActionidByPermaname('chat');

            if ( $this->userCanStillChat() ) {
                $text = '{#still_time_to_chat#}';
            } else {
                $text = '{#preview_chat#}';
            }

            $this->data->footer[] = $this->getText($text, array(
                'style' => 'desee-orange-button',
                'onclick' => $chat_btn,
            ));

        } else {

            $this->registerProductDiv( 'buy-chat-with-blocked', 'm_chat_with_blocked.png', '{#second_chance#}', 'Chat with blocked users for 12 hours for next 30 days', 'second_chance.01', 'second_chance.001' );

            $this->data->footer[] = $this->getText('{#buy_addon_&_chat#}', array(
                'style' => 'desee-green-button',
                'onclick' => $this->showProductDiv( 'buy-chat-with-blocked' ),
            ));

        }

    }

    public function getImageScroll($vars)
    {

        if (!isset($vars['profilepic'])) {
            return $this->getSwipearea(array(
                $this->getColumn(array(
                    $this->getImage('anonymous2.png', array(
                        'margin' => '5 30 0 30',
                    ))
                ))
            ), array(
                'id' => 'profile-images',
                'dynamic_content' => 1,
            ));
        }

        $count = 1;
        $params['imgwidth'] = '600';
        $params['imgheight'] = '600';
        $params['imgcrop'] = 'yes';
        $params['height'] = $this->screen_width;
        $params['width'] = $this->screen_width;
        $params['not_to_assetlist'] = true;
        $params['priority'] = 9;

        if ($this->userIsUnmatched() AND !$this->metas->checkMeta('chat-with-blocked')) {
            $params['blur'] = 1;
        }

        if (!$this->userCanStillChat()) {
            $params['blur'] = 1;
        }

        $swipnavi['margin'] = '-35 0 0 0';
        $swipnavi['align'] = 'center';
        $totalcount = 1;

        while ($count < 10) {
            $n = 'profilepic' . $count;
            if (isset($vars[$n])) {
                $path = $_SERVER['DOCUMENT_ROOT'] . $vars[$n];
            }

            if (isset($vars[$n]) AND strlen($n) > 2 AND isset($path) AND file_exists($path) AND filesize($path) > 40) {
                $totalcount++;
            }
            $count++;
        }

        $count = 1;
        $mycount = 1;

        $scroll[] = $this->getImage($vars['profilepic'], $params);
        $scroll[] = $this->getSwipeNavi($totalcount, 1, $swipnavi);
        $item[] = $this->getColumn($scroll, array('width' => '100%'));
        unset($scroll);

        while ($count < 10) {
            $n = 'profilepic' . $count;
            if (isset($vars[$n]) AND strlen($n) > 2) {
                $path = $_SERVER['DOCUMENT_ROOT'] . $vars[$n];

                if (file_exists($path) AND filesize($path) > 40) {
                    $mycount++;
                    $scroll[] = $this->getImage($vars[$n], $params);
                    $scroll[] = $this->getSwipeNavi($totalcount, $mycount, $swipnavi);
                    $item[] = $this->getColumn($scroll, array());
                    unset($scroll);
                }
            }
            $count++;
        }

        return $this->getSwipearea($item, array(
            'id' => 'profile-images',
            'dynamic_content' => 1,
        ));
    }

    private function goBack()
    {
        $action = new StdClass();
        $action->action = 'open-action';
        $action->action_config = $this->getActionidByPermaname('people');
        $this->data->onload[] = $action;
    }

    public function topRowData($title_text, $metas)
    {

        $onclick = new stdClass();
        $onclick->action = 'share';

        $data[] = $this->getColumn(array(
            $this->getText($title_text, array(
                'font-size' => '22',
                'font-ios' => 'VarelaRound',
                'font-android' => 'VarelaRound',
            ))
        ), array(
            'width' => '68%',
            'vertical-align' => 'middle',
        ));

        $icons[] = $this->getImage('ic_share.png', array(
            'width' => '23',
            'onclick' => $onclick
        ));


        if ($metas->checkMeta('spark-profile')) {
            $icons[] = $this->getImage('ic_home_boost.png', array(
                'width' => '32',
                'margin' => '0 0 0 5',
            ));
        }

        $superlikes = AeplayKeyvaluestorage::model()->findByAttributes(array(
            'play_id' => $metas->current_playid,
            'key' => 'superlikes',
            'value' => $this->playid
        ));

        if (is_object($superlikes)) {
            $icons[] = $this->getImage('ic_home_superlike.png', array(
                'width' => '32',
                'margin' => '0 0 0 5',
            ));
        }

        $data[] = $this->getColumn(array(
            $this->getRow($icons, array(
                'vertical-align' => 'middle',
                'text-align' => 'right',
            ))
        ), array(
            'width' => '32%',
        ));

        return $this->getRow($data, array(
            'width' => '100%',
            'padding' => '5 10 5 20',
            'vertical-align' => 'middle',
            'background-color' => '#ffffff',
            'id' => 'profile-top-row',
            'dynamic_content' => 1,
        ));
    }

    private function isUnmatched()
    {
        $unmatchedMe = json_decode($this->getVariable('unmatched_me'));
        $isUnmatched = false;

        if (empty($unmatchedMe)) {
            return $isUnmatched;
        }

        foreach ($unmatchedMe as $unmatchedUser) {
            if ($unmatchedUser[0] == $this->profileid) {
                $isUnmatched = true;
            }
        }

        return $isUnmatched;
    }

    private function renderStatusOptions($vars)
    {
        $options = array_keys($this->getAdditionalInformationFieldNames());

        $tags = array();
        foreach ($options as $option) {
            $variable = isset($vars[$option]) ? $vars[$option] : null;

            if (empty($variable)) continue;

            if ($this->isJson($variable) AND !is_numeric($variable)) {
                foreach (json_decode($variable) as $item) {
                    $tags[] = $item;
                }
                continue;
            }

            $tags[] = $variable;
        }

        $maxCharsPerRow = 32;
        $currentRowChars = 0;
        $index = 0;
        $row = array();
        foreach ($tags as $tag) {
            $currentRowChars += strlen($tag);
            $index++;

            if ($currentRowChars >= $maxCharsPerRow || $index > 3) {
                $this->data->scroll[] = $this->getRow($row, array('margin' => '10 20 0 20'));
                $row = array();
                $currentRowChars = 0;
                $index = 0;
            }
            $row[] = $this->getText($tag, array(
                'margin' => '0 10 0 0',
                'padding' => '12 12 12 12',
                'border-color' => '#FFC204',
                'border-radius' => '5',
                'font-size' => '14',
                'color' => '#636363'
            ));
        }

        $this->data->scroll[] = $this->getRow($row, array('margin' => '10 20 0 20'));
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function profileCommentView($vars)
    {
        $textareastyle['margin'] = '20 24 10 20';
        $textareastyle['font-size'] = '16';
        $textareastyle['color'] = '#636363';

        $textareastyle['variable'] = $this->getVariableId('profile_comment');
        if (isset($vars['profile_comment'])) {
            $this->data->scroll[] = $this->getText($vars['profile_comment'], $textareastyle);
        }
    }

    public function getBtns($id)
    {
        $btn_no_img = 'icn_dislike.png';
        if ($this->getConfigParam('actionimage5')) {
            $btn_no_img = $this->getConfigParam('actionimage5');
        }

        $btn_yes_img = 'icn_like.png';
        if ($this->getConfigParam('actionimage6')) {
            $btn_yes_img = $this->getConfigParam('actionimage6');
        }

        $column[] = $this->getImagebutton($btn_no_img, 'no_' . $id, false, array('width' => '20%', 'margin' => '0 20 0 0'));

        $column[] = $this->getImagebutton($btn_yes_img, 'yes_' . $id, false, array('width' => '20%'));

        return $this->getRow($column, array(
            'text-align' => 'center',
            'margin' => '20 0 20 0'
        ));
    }

    /**
     * Get user's last login time
     *
     * @param $vars
     * @return string
     */
    private function getLastLogin($vars)
    {
        if (!isset($vars['last_login'])) {
            return '{#long_time_ago#}';
        }

        $timeDifference = time() - $this->getVariable('last_login');
        $hours = floor($timeDifference / 3600);
        $hoursString = 'hours';

        if ($hours >= 24) {
            $days = floor($timeDifference / 86400);
            return $days . ' {#days_ago#}';
        }

        if ($hours >= 1) {
            // We are not showing minutes, so it's at least an hour ago
            $hoursString = 'hour';
        }
        return $hours . ' {#' . $hoursString . '_ago#}';
    }

    private function matchUser($playId)
    {
        $this->initMobileMatching($playId);
        $this->mobilematchingobj->saveMatch();

        $unmatchedUsers = $this->getVariable('unmatched_users');
        $unmatchedUsers = !empty($unmatchedUsers) ? json_decode($unmatchedUsers) : array();
        $unmatchedIds = array_map(function ($user) {
            return $user[0];
        }, $unmatchedUsers);

        if (!in_array($playId, $unmatchedIds)) {
            // not a previously unmatched user
            return;
        }

        // match again the other user to have two-way match
        $this->matchOtherUser($playId);

        // remove the matched user from the unmatched list
        $unmatchedUsers = $this->removeUserFromUnmatched($playId, $unmatchedUsers);
        $this->saveVariable('unmatched_users', $unmatchedUsers);

        // remove the user from the unmatched list of the other user
        $otherUserUnmatched = $this->removeFromUnmatchedMeList($playId);
        $this->saveRemoteVariable('unmatched_me', json_encode($otherUserUnmatched), $playId);
    }

    private function matchOtherUser($playId)
    {
        $this->mobilematchingobj->playid_otheruser = $this->playid;
        $this->mobilematchingobj->playid_thisuser = $playId;
        $this->mobilematchingobj->saveMatch();
    }

    private function removeUserFromUnmatched($playId, $unmatchedUsers)
    {
        return array_filter($unmatchedUsers, function ($user) use ($playId) {
            return $user[0] != $playId ? true : false;
        });
    }

    private function removeFromUnmatchedMeList($playId)
    {
        $otherUserVariables = $this->getPlayVariables($playId);
        $otherUserUnmatched = isset($otherUserVariables['unmatched_me']) ? json_decode($otherUserVariables['unmatched_me']) : array();
        return array_filter($otherUserUnmatched, function ($user) {
            return $user[0] != $this->playid ? true : false;
        });
    }

    public function userIsUnmatched()
    {
        if (empty($this->unmatched_user)) {
            return false;
        }

        return true;
    }

    public function userCanStillChat()
    {

        if (empty($this->unmatched_user)) {
            return true;
        }

        $ttl = '43200';
        $unmatched_time = $this->unmatched_user[1];

        $seconds_left = $ttl - (time() - $unmatched_time);

        // The current user can still chat with his/her partner
        if ($seconds_left > 0) {
            return true;
        }

        return false;
    }

    public function validateAndSave()
    {
        if ($this->menuid != 'save-data') {
            return false;
        }

        $this->validateCommonVars();

        if (empty($this->error)) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        } else {
            // Display the error messages in the footer section
            $this->displayErrors();
        }
    }

    public function displayErrors()
    {
        if (empty($this->error_messages) OR !is_array($this->error_messages)) {
            return false;
        }

        foreach ($this->error_messages as $message) {
            $this->data->scroll[] = $this->getText($message, array('style' => 'register-text-step-2'));
        }

        return true;
    }

    public function handleExtrasPayments() {

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->metas->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $this->metas->play_id = $this->playid;
        $this->metas->meta_key = $card_config['trigger'];
        $this->metas->meta_value = ( $card_config['measurement'] == 'time' ? time() : $card_config['amount'] );
        $this->metas->meta_limit = $card_config['measurement'];
        $this->metas->saveMeta();

        return true;
    }

    private function getDivArgs() {
        return array(
            'width' => '100%',
            'background-color' => '#FFFFFF',
            'border-radius' => '8',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
        );
    }

    public function setHeader() {
        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#my_profile#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));

        return true;
    }

}