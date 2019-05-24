<?php

Yii::import('moosendV3'); // Equivalent to use moosendV3;
Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileproperties.models.*');

class rentalMobileregister2SubController extends Mobileregister2Controller
{

    /** @var MobileloginModel */
    public $loginmodel;

    public function rentalPhase1()
    {
        $error = false;

        /* handling case where user already exists */
        if ($this->getSavedVariable('password') AND $this->menuid != 'mobilereg_do_registration') {
            if ($this->menuid == 'create-new-user') {
                Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
                $loginmodel = new MobileloginModel();
                $loginmodel->userid = $this->userid;
                $loginmodel->playid = $this->playid;
                $loginmodel->gid = $this->gid;
                $play = $loginmodel->newPlay();
                $this->playid = $play;
                $this->data->scroll[] = $this->getText('{#creating_new_account#}', array('style' => 'register-text-step-2'));

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
                return true;
            } else {
                $this->data->scroll[] = $this->getSpacer('15');
                $this->data->scroll[] = $this->getText('{#are_you_sure#}', array('style' => 'register-text-step-2'));
                $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));

                $this->data->scroll[] = $this->getSpacer('15');
                $buttonparams2 = new StdClass();
                $buttonparams2->action = 'submit-form-content';
                $buttonparams2->id = 'create-new-user';
                $this->data->footer[] = $this->getText('{#create_a_new_account#}', array('style' => 'general_button_style_footer', 'onclick' => $buttonparams2));
                return true;
            }
        }

        if (strstr($this->menuid, 'set-role-')) {
            $role = str_replace('set-role-', '', $this->menuid);

            /* save the role or give error */
            if (empty($role)) {
                $error = '(#please_choose_your_role#}';
            } else {

                // Save the original user's selected role
                // This would be used for filtering only
                $this->saveVariable( 'filter_role', $role );

                if($role == 'landlord'){
                    $this->saveVariable('subrole', 'landlord');
                    $role = 'agent';
                } else {
                    $this->saveVariable('subrole', null);
                }
                
                $this->saveVariable('role', $role);
                $this->saveVariable('reg_phase', 2);
                $this->rentalPhase2();
                return true;
            }
        }

        $this->setRole($error);

    }

    public function rentalPhase2()
    {

        if ( $this->menuid == 'back-to-roles' ) {
            $this->saveVariable('reg_phase', 1);
            $this->rentalPhase1();
            return true;
        }

        $this->saveVariable('reg_phase', 2);
        $this->data->scroll[] = $this->getSpacer('10');
        $regfields = $this->setRegFields();

        if ($regfields === true) {
            $this->closeLogin();
            $this->saveRegData();

            if ($this->getVariable('role') == 'agent' OR $this->getVariable('role') == 'landlord') {
                $this->finishUp();

                $this->saveVariable('area_settings', 'sq_meter');
                $this->saveVariable('price_settings', 'price_per_month');

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
            } else {
                $this->saveVariable('reg_phase', 3);
                $this->rentalPhase3();
            }
            return true;
        } else {
            $submit = new stdClass();
            $submit->action = 'submit-form-content';
            $submit->id = 'mobilereg_do_registration';
            $submit->viewport = 'top';

            $this->data->footer[] = $this->getTextbutton(strtoupper('{#register#}'), array(
                'onclick' => $submit,
                'style' => 'submit-button',
                'id' => 'id',
                'submit_menu_id' => 'saver'
            ));
        }

        return true;
    }

    public function rentalPhase3()
    {
        $this->rewriteActionField('subject', 'Additional Information');

        if ($this->menuid == 'saver-2') {
            $this->rentalPhase4();
            return true;
        }

        $this->saveVariables();
        $this->loadVariableContent(true);
        $this->data->scroll[] = $this->getSnippet('rentaltenantproperties');

        $additionalInformationRow[] = $this->getText(strtoupper('{#tell_us_more_about_yourself#}'), array(
            'style' => 'form-field-textfield-onoff'
        ));

        //$additionalInformationRow[] =
        $additionalInformationRow[] = $this->getFieldtextarea('', array('style' => 'mobileproperty_textarea_register', 'variable' => 'additional_info'));
        $this->data->scroll[] = $this->getColumn($additionalInformationRow, array(
            'style' => 'propertydetail-shadowbox-indent'
        ));

        /* if there is location error, we will provide a locate button instead of this */
        if (!isset($locationerror)) {
            $this->data->footer[] = $this->getTextbutton(strtoupper('{#continue#}'), array('style' => 'submit-button', 'id' => 'saver-2'));
        }

        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache($this->playid . '-' . 'registration', true);
        return true;
    }

    public function rentalPhase4()
    {
        $this->saveVariables();
        $this->saveVariable('reg_phase', 4);
//        $this->data->scroll[] = $this->getSpacer(200);

        if ($this->menuid == 'register-complete') {
//            $this->submitvariables['filter_price_per_week'] = 'price_per_month';
            MobilepropertiesSettingModel::saveSubmit($this->submitvariables, $this->playid, $this->getSavedVariable('temp_district_filtering'), $this->gid);
            $this->data->scroll = array();
            $this->data->scroll[] = $this->getSpacer('100');
            $this->data->scroll[] = $this->getText('{#loading_data#}...', array('style' => 'register-text-step-2'));
            $this->data->footer = array();
            $this->finishUp();

            $complete = new StdClass();
            $complete->action = 'complete-action';
            $this->data->onload[] = $complete;
            return true;
        }

        $this->showTenantPreferences();
    }

    public function showTenantPreferences()
    {
        $settings = new stdClass();

        $areaOptions = array(
            'sq_ft' => '{#sq_ft#}',
            'sq_meter' => '{#sq_meter#}'
        );

        $data = array(
            'variable' => 'filter_sq_ft',
            'field_offset' => 3,
        );

        $searchFields[] = $this->formkitRadiobuttons('', $areaOptions, $data);

        $priceOptions = array(
            'price_per_month' => '{#price_per_month#}',
            'price_per_week' => '{#price_per_week#}',
        );

        $data = array(
            'variable' => 'filter_price_per_week',
            'field_offset' => 3,
        );

        $searchFields[] = $this->formkitRadiobuttons('', $priceOptions, $data);

        $this->data->scroll[] = $this->getColumn($searchFields, array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
        ));

//        $onclick = new StdClass();
//        $onclick->action = 'open-action';
//        $onclick->action_config = $this->getConfigParam('suggest_location_action');
//        $onclick->id = 'tmp-property';
//        $onclick->open_popup = 1;
//        $onclick->sync_open = 1;
//        $onclick->back_button = 1;
//        $onclick->keep_user_data = 1;
//
//        $districts = $this->getSavedVariable('temp_district_filtering') ? json_decode($this->getSavedVariable('temp_district_filtering'),true) : '{#no_district_limitation#}';
//        if(is_array($districts)){ $districts = substr(implode(', ',$districts),0,150); }
//
//        $data = array();
//        $data[] = $this->getImage('address-icon.png', array('width' => '20', 'margin' => '10 0 0 10'));
//        $data[] = $this->getText($districts, array(
//            'variable' => 'temp_district',
//            'style' => 'mobileproperty_textbutton',
//        ));
//
//        $this->data->scroll[] = $this->getRow($data, array(
//            'style' => 'mobileproperty_input',
//            'onclick' => $this->getOnclick('tab2')
//        ));
//
//        if ($this->getVariable('role') == 'tenant') {
//            $this->data->scroll[] = $this->getSpacer('10');
//            $this->getUnitSettingsFields($settings);
//        }

        $propertyValues = array();

//        if ($settings->type_flat) {
//            $propertyValues['flat'] = 1;
//        }
//        if ($settings->type_room) {
//            $propertyValues['room'] = 1;
//        }
//        if ($settings->type_house) {
//            $propertyValues['house'] = 1;
//        }

        $searchFields = array();

        $propertyOptions = array(
            'room' => ucfirst('{#room#}'),
            'flat' => ucfirst('{#flat/apartment#}'),
            'house' => ucfirst('{#house#}')
        );

        $data = array(
            'variable' => 'property_type',
            'field_offset' => 3,
            'value' => $propertyValues
        );

        $searchFields[] = $this->formkitTags('', $propertyOptions, $data);

        $optionsValues = array();

//        if ($settings->furnished == 1) {
//            $optionsValues['unfurnished'] = 0;
//        } else if ($settings->furnished == 0 && ! is_null($settings->furnished)) {
//            $optionsValues['furnished'] = 0;
//        }
//
//        $optionsValues['pets_allowed'] = $settings->options_pets_allowed;
//        $optionsValues['outside_spaces'] = $settings->options_outside_spaces;

        $options = array(
            'furnished' => '{#furnished#}',
            'unfurnished' => '{#unfurnished#}',
            'pets_allowed' => '{#pets_allowed#}',
            'outside_spaces' => '{#outside_spaces#}'
        );

        $data = array(
            'variable' => 'options',
            'field_offset' => 3,
            'value' => $optionsValues,
            'clustered_mode' => false,
        );

        $searchFields[] = $this->formkitTags('', $options, $data);

        $this->data->scroll[] = $this->getColumn($searchFields, array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
        ));

        $this->data->scroll[] = $this->getSpacer('10');

        $slider[] = $this->getText('{#min_bedrooms#}', array('style' => 'mobileproperty_fieldlist_label'));
        $slider[] = $this->getRangeslider(0, array(
            'style' => 'mobileproperty_rangeslider',
            'min_value' => 0,
            'max_value' => 10,
            'step' => 1,
            'variable' => 'from_num_bedrooms',
            'value' => 0,
        ));

        $slider[] = $this->getRow(array(
            $this->getText('0', array( 'style' => 'slider-values' ) ),
            $this->getText( 0, array( 'variable' => 'from_num_bedrooms', 'style' => 'slider-values-center' ) ),
            $this->getText( '', array( 'style' => 'slider-values-center-2' ) ),
            $this->getText('10', array( 'style' => 'slider-values-right' ) ),
        ), array( 'margin' => '0 30 10 30', ));

        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

        $slider = array();
        $slider[] = $this->getText('{#max_bedrooms#}', array('style' => 'mobileproperty_fieldlist_label'));
        $slider[] = $this->getRangeslider(5, array(
            'style' => 'mobileproperty_rangeslider',
            'min_value' => 0,
            'max_value' => 10,
            'step' => 1,
            'variable' => 'to_num_bedrooms',
            'value' => 5
        ));

        $slider[] = $this->getRow(array(
            $this->getText('0', array( 'style' => 'slider-values' ) ),
            $this->getText( 5, array( 'variable' => 'to_num_bedrooms', 'style' => 'slider-values-center' ) ),
            $this->getText('', array( 'style' => 'slider-values-center-2' ) ),
            $this->getText('10', array( 'style' => 'slider-values-right' ) ),
        ), array( 'margin' => '0 30 10 30', ));

        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

        $this->data->scroll[] = $this->getColumn(array(
            $this->getFieldtext(0, array('variable' => 'from_price_per_month', 'hint' => '{#min_price#}', 'style' => 'mobileproperty_textfield'))
        ), array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

//        $slider = array();
//        $slider[] = $this->getText('{#min_price#}', array('style' => 'mobileproperty_fieldlist_label'));
//        $slider[] = $this->getRangeslider($settings->from_price_per_month, array(
//            'style' => 'mobileproperty_rangeslider',
//            'min_value' => 0,
//            'max_value' => 15000,
//            'step' => 100,
//            'variable' => 'from_price_per_month',
//            'value' => $settings->from_price_per_month
//        ));
//
//        $slider[] = $this->getRow(array(
//            $this->getText('£0', array( 'style' => 'slider-values' ) ),
//            $this->getText( '£', array( 'style' => 'slider-values-center' ) ),
//            $this->getText( $settings->from_price_per_month, array( 'variable' => 'from_price_per_month', 'style' => 'slider-values-center-2-normal' ) ),
//            $this->getText('£15000', array( 'style' => 'slider-values-right' ) ),
//        ), array( 'margin' => '0 30 10 30', ));

//        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

//        $slider = array();
//        $slider[] = $this->getText('{#max_price#}', array('style' => 'mobileproperty_fieldlist_label'));
//        $slider[] = $this->getRangeslider($settings->to_price_per_month, array(
//            'style' => 'mobileproperty_rangeslider',
//            'min_value' => 0,
//            'max_value' => 15000,
//            'step' => 100,
//            'variable' => 'to_price_per_month',
//            'value' => $settings->to_price_per_month
//        ));
//
//        $slider[] = $this->getRow(array(
//            $this->getText('£0', array( 'style' => 'slider-values' ) ),
//            $this->getText('£', array( 'style' => 'slider-values-center' ) ),
//            $this->getText( $settings->to_price_per_month, array( 'variable' => 'to_price_per_month', 'style' => 'slider-values-center-2-normal' ) ),
//            $this->getText('£15000', array( 'style' => 'slider-values-right' ) ),
//        ), array( 'margin' => '0 30 10 30', ));

        $this->data->scroll[] = $this->getColumn(array(
            $this->getFieldtext(12000, array('variable' => 'to_price_per_month', 'hint' => '{#max_price#}', 'style' => 'mobileproperty_textfield'))
        ), array(
            'margin' => '10 10 10 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $this->data->scroll[] = $this->getText('{#you_can_change_those_filters_after_your_registration_as_well#}', array(
            'text-align' => 'center',
            'font-size' => '14',
            'margin' => '0 0 10 0',
            'color' => '#ffffff'
        ));

//        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

//        $col[] = $this->getTextbutton(strtoupper('{#clear_filters#}'),
//            array('id' => 'save-settings', 'style' => 'filtering-warning-button', 'onclick' => $clicker_clear));
//
//        $col[] = $this->getTextbutton(strtoupper('{#save#}'),
//            array('id' => 'save-settings', 'style' => 'filtering-save-button', 'onclick' =>$clicker_save));

        $onclick = new stdClass();
        $onclick->id = 'register-complete';
        $onclick->action = 'submit-form-content';

        $this->data->footer[] = $this->getTextbutton(strtoupper('{#save#}'),
            array('id' => 'save-settings', 'style' => 'submit-button', 'onclick' => $onclick));

    }

    public function getUnitSettingsFields()
    {
        $searchFields = [];
        $areaOptions = array(
            'sq_ft' => '{#sq_ft#}',
            'sq_meter' => '{#sq_meter#}'
        );

        $data = array(
            'variable' => 'filter_sq_ft',
            'field_offset' => 3,
        );

        $searchFields[] = $this->formkitRadiobuttons('', $areaOptions, $data);

        $priceOptions = array(
            'price_per_month' => '{#price_per_month#}',
            'price_per_week' => '{#price_per_week#}',
        );

        $data = array(
            'variable' => 'filter_price_per_week',
            'field_offset' => 3,
        );

        $searchFields[] = $this->formkitRadiobuttons('', $priceOptions, $data);

        $this->data->scroll[] = $this->getColumn($searchFields, array(
            'margin' => '0 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
        ));
    }

    public function generalPhaseComplete(){
        if($this->menuid == 'create-new-user') {
            Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
            $loginmodel = new MobileloginModel();
            $loginmodel->userid = $this->userid;
            $loginmodel->playid = $this->playid;
            $loginmodel->gid = $this->gid;
            $play = $loginmodel->newPlay();
            $this->playid = $play;

            $this->data->scroll[] = $this->getText('{#creating_new_account#}', array('style' => 'register-text-step-2'));
            $complete = new StdClass();
            $complete->action = 'complete-action';
            $this->data->onload[] = $complete;
            return true;
        } else {
            if($this->no_spacer == false){
                $this->data->scroll[] = $this->getSpacer('15');
            }

            if ( $this->getConfigParam( 'actionimage1' ) ) {
                $image_file = $this->getConfigParam( 'actionimage1' );
                $this->data->scroll[] = $this->getImage($image_file);
            }

            $this->data->scroll[] = $this->getSpacer('15');
            $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_create_a_new_account#}?', array( 'style' => 'register-text-step-2'));
            $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                'style' => 'register-text-step-2',
                'id' => 'backer',
                'action' => 'open-branch',
                'config' => $this->getConfigParam('login_branch'),
            ));

            $this->data->scroll[] = $this->getSpacer('15');
            $buttonparams2 = new StdClass();
            $buttonparams2->action = 'submit-form-content';
            $buttonparams2->id = 'create-new-user';
            $this->data->footer[] = $this->getText(strtoupper('{#create_a_new_account#}'),array('style' => 'submit-button','onclick' => $buttonparams2));
            return true;
        }
    }

    public function setBackButton(){
        $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_roles#}', array(
           'style' => 'register-text-step-2',
           'id' => 'back-to-roles',
           'action' => 'submit-form-content',
       ));
    }

    public function setProfilePic()
    {

//        if ($this->getConfigParam('require_photo')) {
//            $this->data->scroll[] = $this->getText('{#please_add_profile_pic#}', array('style' => 'register-text-step-2'));

            if (isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']) {
                $pic = $this->varcontent['fb_image'];
                $txt = '{#change_the_photo#}';
            } elseif (isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
                $pic = $this->varcontent['profilepic'];
                $txt = '{#change_the_photo#}';
            } else {
                $pic = 'upload_pic.png';
                $txt = '{#add_a_photo#}';
            }

            //$this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '600','imgcrop'=>'yes'));
            $onclick = new stdClass();
            $onclick->id = $this->vars['profilepic'];
            $onclick->action = 'upload-image';
            $onclick->sync_upload = true;
            $onclick->max_dimensions = '1200';
            $onclick->variable = $this->vars['profilepic'];

            $img[] = $this->getImage($pic, array(
                'variable' => $this->vars['profilepic'],
                'crop' => 'round',
                'width' => '150',
                'priority' => 9,
                'text-align' => 'center',
                'floating' => "1",
                'float' => 'center',
                'border-width' => '5',
                'border-color' => '#ffffff',
                'border-radius' => '75',
                'onclick' => $onclick
            ));
            $img[] = $this->getText(' ');

            $this->data->scroll[] = $this->getColumn($img, array('text-align' => 'center', 'height' => '150', 'margin' => '8 0 8 0', 'floating' => "1", 'float' => 'center'));

            if ($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 'sex-woman')) {
                $this->data->scroll[] = $this->getText('{#uploading#} ...', array('style' => 'uploading_text'));
            }

//            $this->data->scroll[] = $this->getTextbutton($txt, array(
//                'variable' => $this->vars['profilepic'],
//                'action' => 'upload-image',
//                'sync_upload' => true,
//                'max_dimensions' => '900',
//                'style' => 'general_button_style',
//                'id' => $this->vars['profilepic']));
//        }
    }

    public function setRole($error = false) {
        $imageRow[] = $this->getImage('logo-new.png', array('width' => $this->screen_width / 2, 'margin' => '80 0 50 0'));
        $this->data->scroll[] = $this->getRow($imageRow, array('text-align' => 'center'));

        // $tenant = array('variable_value' => "tenant", 'style' => 'selector_role_selected', 'allow_unselect' => 1, 'animation' => 'fade');
        // $landlord = array('variable_value' => "landlord", 'style' => 'selector_role_selected', 'allow_unselect' => 1, 'animation' => 'fade');
        // $agent = array('variable_value' => "agent", 'style' => 'selector_role_selected', 'allow_unselect' => 1, 'animation' => 'fade');

        if ($error) {
            $this->data->footer[] = $this->getText($error, array('style' => 'register-text-step-error'));
        }

        $this->data->scroll[] = $this->getRoleComponent('tenant');
        $this->data->scroll[] = $this->getRoleComponent('landlord');
        $this->data->scroll[] = $this->getRoleComponent('agent');

        $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
            'style' => 'register-text-step-2',
            'id' => 'backer',
            'action' => 'open-branch',
            'config' => $this->getConfigParam('login_branch'),
        ));

//        $col[] = $this->getTextbutton('{#tenant#}', array('variable' => 'role', 'style' => 'selector_role', 'id' => 'set-role-tenant'));
//        $col[] = $this->getSpacer('10');
//        $col[] = $this->getTextbutton('{#landlord#}', array('variable' => 'role', 'style' => 'selector_role', 'id' => 'set-role-agent'));
//        $col[] = $this->getSpacer('10');
//        $col[] = $this->getTextbutton('{#agent#}', array('variable' => 'role', 'style' => 'selector_role', 'id' => 'set-role-agent'));
//
//        $this->data->scroll[] = $this->getColumn($col, array('margin' => '14 40 31 40', 'text-align' => 'center'));
//        $this->data->footer[] = $this->getTextbutton('{#continue#}', array('id' => 'set-role'));
//        $this->data->scroll[] = $this->getText('{#role_long_explanation#}', array('style' => 'register-text-step-roles'));

    }



    public function setProfileComment()
    {
        if (!$this->getConfigParam('require_comment')) {
            return false;
        }

        if (isset($this->varcontent['profile_comment'])) {
            $commentcontent = $this->varcontent['profile_comment'];
        } elseif ($this->getSavedVariable('instagram_bio')) {
            $commentcontent = $this->getSavedVariable('instagram_bio');
        } else {
            $commentcontent = '';
        }

        if ($this->getSavedVariable('role') == 'brand') {
            $this->data->scroll[] = $this->getFieldtext($this->getSubmittedVariableByName('company'), array('style' => 'general_button_style_black', 'variable' => 'company', 'hint' => '{#company_name#}',
                'value' => $this->getSubmittedVariableByName('company')));
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#company_description#} ({#required#})', 'style' => 'general_textarea'));
        } else {
            $this->data->scroll[] = $this->getFieldtextarea($commentcontent, array('variable' => $this->getVariableId('profile_comment'), 'hint' => '{#comment#} ({#required#})', 'style' => 'general_textarea'));
        }

    }


    public function finishUp() {

        $this->saveVariable('reg_phase', 'complete');

        $this->updateLocalRegVars();
        $this->beforeFinishRegistration();

        if ( $this->getSavedVariable('subrole') AND $this->getSavedVariable('subrole') != null ) {
            $user_role = $this->getSavedVariable('subrole');
        } else {
            $user_role = $this->getSavedVariable('role');
        }

        $this->addToMoosend( $user_role );

        if (!$this->getConfigParam('require_match_entry')) {
            return false;
        }

        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false, __FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent, $this->gid, $this->playid);

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
            $this->saveVariable('men', 0);
            $this->saveVariable('women', 1);
        } else if ($gender == 'woman') {
            $this->saveVariable('men', 1);
            $this->saveVariable('women', 0);
        }

        // $this->saveVariable('logged_in', 1);
        $this->saveVariable('notify', 1);
    }



    public function generalComplete(){
        $this->beforeFinishRegistration();

        $menu = new StdClass();
        $menu->action = 'list-branches';

        $this->data->onload[] = $menu;
//        $this->data->footer[] = $this->getHairline('#ffffff');
        $this->data->footer[] = $this->getTextbutton(strtoupper('{#complete_registration#}'), array('submit_menu_id' => 'saver','action' => 'complete-action', 'style' => 'submit-button' ,'id' => 'submitter'));
    }

    public function setRegFields()
    {
        $role = $this->getVariable('role');
        $subrole = $this->getVariable('subrole');

        $error = false;
        $error2 = false;
        $error3 = false;
        $error4 = false;
        $error5 = false;
        $error6 = false;
        $errorImage = false;
        $errorBranchName = false;
        $errorCountryCode = false;
        $errorPostCode = false;
        $errorTitle = false;
        $errorAddress = false;

        $this->setProfilePic();

        if ( ! $this->getSavedVariable('profilepic') && $this->menuid == 'mobilereg_do_registration') {
            $errorImage = true;
            $this->data->scroll[] = $this->getText('{#please_add_a_profile_photo#}', array(
                'text-align' => 'center',
                'color' => '#CB232A'
            ));
        }

        if ($role == 'tenant' || $subrole == 'landlord') {
//            $titles = array(
//                'mr' => '{#mr.#}',
//                'mrs' => '{#mrs.#}',
//                'ms' => '{#ms.#}',
//                'miss' => '{#miss#}'
//            );
//
//            $data = array(
//                'variable' => 'title',
//                'field_offset' => 3
//            );
//
//            $titleRow[] = $this->formkitRadiobuttons('{# #}', $titles, $data);
//            $this->data->scroll[] = $this->getRow($titleRow, array('margin' => '0 25 0 25'));
//
//            if ( ! $this->getSubmitVariable('title') && $this->menuid == 'mobilereg_do_registration') {
//                $errorTitle = '{#the_title_is_required#}';
//                $this->data->scroll[] = $this->getText($errorTitle, array('text-align' => 'center', 'color' => '#CB232A'));
//            }

            if ($subrole == 'landlord') {
                $icon = 'landlord_icon_white2.png';
            } else {
                $icon = 'tenant_icon_white2.png';
            }

            $firstName = $this->getSubmittedVariableByName('first_name');
            $lastName = $this->getSubmittedVariableByName('surname');

            if ($this->menuid == 'mobilereg_do_registration' && $firstName && $lastName) {
                $this->saveVariable('name', $firstName . ' ' . $lastName);
                $this->saveVariable('real_name', $firstName . ' ' . $lastName);
            }

            $this->data->scroll[] = $this->getFieldWithIcon($icon, $this->vars['first_name'], '{#first_name#}', $error,'text',false,'name');
            $this->data->scroll[] = $this->getFieldWithIcon($icon, $this->vars['surname'], '{#surname#}', $error,'text',false,'name');
        }

        if ($this->getSavedVariable('instagram_username')) {

            $this->data->scroll[] = $this->getText("{#you_are_connected_with_instagram#}", array('style' => 'register-text-step-2'));
            if ($this->getConfigParam('collect_name', 1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name', $realname);
                }

                $error = $this->checkForError('real_name', '{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png', $this->vars['real_name'], '{#name#}', $error, 'text', false, 'name');
            }

        } elseif ($this->fblogin === false AND $this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getFacebookSignInButton('fb-login');

            if ($this->getConfigParam('collect_name', 1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name', $realname);
                }

                $error = $this->checkForError('real_name', '{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png', $this->vars['real_name'], '{#name#}', $error,'text',false,'name');
            }
        } elseif ($this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getText("{#you_are_connected_with_facebook#}", array('style' => 'register-text-step-2'));
        } else {
            if ($this->getConfigParam('collect_name', 1) && $role != 'tenant' && $subrole != 'landlord') {

                $error2 = $this->checkForError('real_name', '{#please_input_valid_name#}');

                $name = $role == 'agent' ? '{#agency_name#}' : '{#name#}';
                $icon = $role . '_icon_white2.png';

                if ($subrole == 'landlord') {
                    $icon = 'landlord_icon_white.png';
                    $name = '{#name#}';
                }

                $this->data->scroll[] = $this->getFieldWithIcon($icon, $this->vars['real_name'], $name, $error2,'text',false,'name');
            }
        }

        if ($role == 'agent' && $subrole != 'landlord') {
            $errorBranchName = $this->getAgentFieldError('branch_name');
            $this->data->scroll[] = $this->getFieldWithIcon('branch_name_icon.png', $this->vars['branch_name'], '{#branch_name#}', $errorBranchName ,'text',false);

            $this->data->scroll[] = $this->getFieldWithIcon('website_icon.png', $this->vars['website'], '{#website#}', null,'text',false);

            $errorPostCode = $this->getAgentFieldError('postcode');
            $this->data->scroll[] = $this->getFieldWithIcon('zip_code_icon.png', $this->vars['postcode'], '{#postcode#}', $errorPostCode,'text',false);

            $errorAddress = $this->getAgentFieldError('address');
            $this->data->scroll[] = $this->getFieldWithIcon('branch_name_icon.png', $this->vars['address'], '{#address#}', $errorAddress,'text',false);

        }

        if ($this->getConfigParam('show_email', 1)) {
            $error3 = $this->checkForError('email', '{#input_valid_email#}', '{#email_already_exists#}');
            $this->data->scroll[] = $this->getFieldWithIcon('email_icon.png', $this->getVariableId('email'), '{#email#}', $error3);
        }

//        $errorCountry = $this->getAgentFieldError('country');
//        $this->data->scroll[] = $this->getClickableField('icon-globe.png', $this->vars['country'], '{#country#}', $this->getActionidByPermaname( 'countrieshelper' ), $errorCountry);

        $errorCountryCode = $this->getAgentFieldError('country_code');
        $this->data->scroll[] = $this->getClickableField('icon-zip.png', $this->vars['country_code'], '{#country_code#}', $this->getActionidByPermaname( 'zipcodeshelper' ), $errorCountryCode);

        if ($this->getConfigParam('collect_phone')) {
            $error4 = $this->checkForError('phone', '{#please_input_a_valid_phone#}');
            $this->data->scroll[] = $this->getFieldWithIcon('phone_icon.png', $this->vars['phone'], '{#phone#}', $error4, 'text', false, 'number');
        }

        if ($this->getConfigParam('collect_address')) {
            $this->data->scroll[] = $this->getFieldWithIcon('icon-address.png', $this->vars['address'], '{#address#}', false, 'text');
        }

        if ($this->getConfigParam('collect_password')) {
            $error5 = $this->checkForError('password_validity', '{#at_least#} 4 {#characters#}');
            $error6 = $this->checkForError('password_match', '{#passwords_do_not_match#}');
            $this->data->scroll[] = $this->getFieldWithIcon('pass_icon.png', $this->fields['password1'], '{#password#}', $error5, 'password');
            $this->data->scroll[] = $this->getFieldWithIcon('pass_icon.png', $this->fields['password2'], '{#password_again#}', $error6, 'password', 'mobilereg_do_registration');
        }

        $this->data->scroll[] = $this->getSpacer('5');

        $this->setBackButton();

        $this->data->scroll[] = $this->getText('{#your_information_is_safe_with_us_and_will_not_be_displayed_to_other_users#}', array(
            'text-align' => 'center',
            'color' => '#FF0000',
            'font-size' => 12
        ));

        if (!$error AND !$error2 AND !$error3 AND !$error4 AND !$error5 AND !$error6 AND !$errorImage AND !$errorTitle AND $this->menuid == 'mobilereg_do_registration') {
            $this->saveVariable('reg_phase', 2);
            unset($this->data->scroll);
            $this->data->scroll = array();
            // Need to investigate why ..
            unset($this->data->footer);
            return true;
        }

        return false;
    }

    protected function getAgentFieldError($field)
    {
        if ($this->menuid != 'mobilereg_do_registration') {
            return false;
        }

        $value = $this->getSubmitVariable($field);

        if ( !empty($value) ) {
            return false;
        }

        switch ($field) {
            case 'branch_name':
                return '{#please_input_branch_name#}';
                break;

            case 'country':
                return '{#please_select_a_country#}';
                break;
            
            case 'country_code':
                return '{#please_input_country_code#}';
                break;

            case 'postcode':
                return '{#please_input_postcode#}';
                break;
        }

    }

    protected function getRoleComponent($role)
    {
        $imageWrapper[] = $this->getImage($role . '_icon.png');
        $col[] = $this->getColumn($imageWrapper, array('style' => 'register_role_image'));

        $textWrapper[] = $this->getText(ucfirst($role), array('style' => 'register_role_title'));
        $textWrapper[] = $this->getText($this->getRoleDescription($role), array('style' => 'register_role_subtitle'));
        $col[] = $this->getColumn($textWrapper, array('style' => 'register_role_text'));

        $col[] = $this->getText('›', array('style' => 'register_role_arrow'));

        $onclick = new stdClass();
        $onclick->id = 'set-role-' . $role;
        $onclick->action = 'submit-form-content';

        return $this->getRow($col, array('style' => 'register_role_component', 'onclick' => $onclick));
    }

    protected function getRoleDescription($role)
    {
        $descriptions = [
            'tenant' => '{#i_am_looking_for_property_to_rent#}',
            'landlord' => '{#i_have_a_property_for_rent#}',
            'agent' => '{#we_have_a_property_for_rent#}'
        ];

        return $descriptions[$role];
    }

    protected function addToMoosend( $user_type ) {

        $apikey = '1abfbad1d8e24ee180322cba00d1109b';
        $mailing_list_id = '4ada6688-a05e-4e7d-bc0f-9833b1fa1f2e';

        if ( $user_type == 'tenant' ) {
            $mailing_list_id = '4ada6688-a05e-4e7d-bc0f-9833b1fa1f2e';
        } else if ( $user_type == 'landlord' ) {
            $mailing_list_id = '313d7ce8-292f-4edc-9ee5-bae1c2bd2722';
        } else if ( $user_type == 'agent' ) {
            $mailing_list_id = '6faa7244-297d-49c5-b8a5-88459a1fc0cc';
        }

        $api_instance = new moosendV3\Api\SubscribersApi();

        $format = 'json'; // string |
        $body = new moosendV3\Model\AddingSubscribersRequest([
            'email' => $this->getSavedVariable('email', 'daniel.minchev@appzio.com'),
            'name' => $this->getSavedVariable('real_name', 'Daniel Minchev')
        ]);

        $result = @$api_instance->addingSubscribers($format, $mailing_list_id, $apikey, $body);
        
        if ( empty($result) ) {
            return false;
        }

        return true;
    }

}