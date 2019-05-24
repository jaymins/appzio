<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileproperties.models.*');

class rentalMobileuserprofileSubController extends MobileuserprofileController
{

    public $availability;
    public $mobileplacesobj;
    public $errors;

    public function showProfileView()
    {
        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);

        if (empty($vars)) {
            $this->data->scroll[] = $this->getText('{#info_missing#}');
            $this->data;
        }

        $name = isset($vars['real_name']) ? $vars['real_name'] : '{#unknown#}';

        if (isset($vars['role']) AND $vars['role'] == 'agent' AND isset($vars['real_name']) AND !empty($vars['real_name'])) {
            $subject_line = $vars['real_name'];
            if (isset($vars['branch_name']) AND $vars['branch_name']) {
                $subject_line = $vars['real_name'] . ' - ' . $vars['branch_name'];
            }
            $this->rewriteActionField('subject', $subject_line);
        } else if (isset($vars['subrole']) && $vars['subrole'] == 'landlord') {
            $name = 'Landlord - ' . $this->getFirstName($vars);
            $this->rewriteActionField('subject', 'Landlord - ' . $this->getFirstName($vars));
        } else {
            if (isset($vars['first_name']) AND isset($vars['surname']) AND $vars['first_name'] AND $vars['surname']) {
                $this->rewriteActionField('subject', $vars['first_name'] . ' ' . $vars['surname']);
            }
        }

        $searchCriteria = MobilepropertiesSettingModel::getSettings($this->profileid, $this->gid);
        Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

        $this->getImageScroll($vars);
        $this->data->scroll[] = $this->getSpacer('5');

        $family = $this->getFamilyStatus($vars);
        $residency = $this->getResidenceOptions($vars);
        $profile_comment = isset($vars['additional_info']) ? $vars['additional_info'] : '{#no_description#}';

        if (!isset($vars['real_name'])) {
//            $this->data->scroll[] = $this->getText('{#info_missing#}');
            $this->data;
        }

        $descriptionStyle = 'profile_shadowbox_textarea_agent';

        if (isset($vars['role']) && $vars['role'] == 'tenant') {
            $descriptionStyle = 'profile_shadowbox_textarea';

            $left[] = $this->getImage('pound-icon-filled.png', array('width' => '30'));
            $left[] = $this->getText('£' . $searchCriteria->from_price_per_month . ' - ' . '£' . $searchCriteria->to_price_per_month, array('style' => 'psbox_text'));
            $col[] = $this->getRow($left, array('style' => 'profile_shadowbox'));

            $right[] = $this->getImage('icon-bed-filled.png', array('width' => '30'));
            $right[] = $this->getText($searchCriteria->from_num_bedrooms . ' - ' . $searchCriteria->to_num_bedrooms, array('style' => 'psbox_text'));
            $col[] = $this->getRow($right, array('style' => 'profile_shadowbox'));
            $this->data->scroll[] = $this->getRow($col);

            $this->getTenantSearches($vars, $descriptionStyle);

            $this->data->scroll[] = $this->getColumn(array(
                $this->getRow(array(
                    $this->getText('{#family_situation#}', array('style' => 'psbox_title')),
                    $this->getText($family, array('style' => 'psbox_text'))
                ), array(
                    'style' => 'profile_shadowbox_wide'
                )),
            ), array(
                'style' => 'rentals_shadow_box'
            ));

            $this->data->scroll[] = $this->getColumn(array(
                $this->getRow(array(
                    $this->getText('{#residency_status#}', array('style' => 'psbox_title')),
                    $this->getText($residency, array('style' => 'psbox_text'))
                ), array('style' => 'profile_shadowbox_wide')),
            ), array(
                'style' => 'rentals_shadow_box'
            ));
        }

        $description[] = $this->getText('{#about#} ' . $this->getFirstName($vars), array('style' => 'psbox_title'));
        $description[] = $this->getSpacer(5);
        $description[] = $this->getText($profile_comment, array('style' => 'psbox_textarea'));

        $sub_description = [];

        if (
            isset($vars['email']) AND
            isset($vars['subrole']) AND
            isset($vars['role']) AND
            ($vars['role'] == 'agent' AND $vars['subrole'] != 'landlord')
        ) {
            $sub_description[] = $this->getRow(array(
                $this->getText('Email: ', array('style' => 'psbox_title')),
                $this->getText($vars['email'], array('style' => 'psbox_textarea')),
            ));
        }

        if (
            isset($vars['phone']) AND
            isset($vars['subrole']) AND
            isset($vars['role']) AND
            ($vars['role'] == 'agent' AND $vars['subrole'] != 'landlord')
        ) {
            $sub_description[] = $this->getRow(array(
                $this->getText('Phone: ', array('style' => 'psbox_title')),
                $this->getText($vars['phone'], array('style' => 'psbox_textarea')),
            ));
        }

        if (isset($vars['country']) AND isset($vars['subrole']) AND ($vars['role'] == 'agent')) {
            $sub_description[] = $this->getRow(array(
                $this->getText('Country: ', array('style' => 'psbox_title')),
                $this->getText($vars['country'], array('style' => 'psbox_textarea')),
            ));
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn($description, array('style' => $descriptionStyle)),
        ), array(
            'style' => 'rentals_shadow_box'
        ));

        if ($sub_description) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getColumn($sub_description, array('style' => $descriptionStyle)),
            ), array(
                'style' => 'rentals_shadow_box'
            ));
        }

        // $this->data->footer[] = $this->getText( $id );
        $this->data->scroll[] = $this->getSpacer('20');

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getTwoWayChatId($this->profileid);
        $onclick->action_config = $this->getConfigParam('chat');
        $onclick->back_button = 1;
        $onclick->sync_open = 1;

        $this->data->footer[] = $this->getButtonWithIcon('rental-chat-button-icon.png', 'chat', strtoupper('{#send_message#}'),
            array('id' => 'chat', 'style' => 'profile_chat_btn'), array('color' => '#ffffff'), $onclick);

    }

    public function showProfileEdit()
    {

        $this->setGridWidths();
        $this->profileEditSaves();
        $this->rewriteActionField('subject', 'My Profile');

        /* set the screen pixels */
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        if ($this->menuid == 'del-images') {
            $user[] = $this->getText('{#click_on_images_to_delete#}', array('vertical-align' => 'top', 'font-size' => '22'));
            $this->data->scroll[] = $this->getRow($user, array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        } else {
            /* top part */
            $txt = $this->getVariable('real_name');

            if ($this->getVariable('role') == 'tenant') {
                $txt = $this->getVariable('first_name') . ' ' . $this->getVariable('surname');
            }

            $tr_lang = ($this->appinfo->name == 'Rantevu' ? 'el' : $this->lang);

            $txt2 = ThirdpartyServices::translateString($this->getVariable('city'), 'en', $tr_lang);
            if ($this->getVariable('country')) {
                $txt2 .= ', ' . ThirdpartyServices::translateString($this->getVariable('country'), 'en', $tr_lang);
            }

            $this->data->scroll[] = $this->getColumn(array(
                $this->getText(strtoupper($txt), array('font-size' => '22', 'font-ios' => 'Roboto', 'font-android' => 'Roboto', 'text-align' => 'center'))
            ), array(
                'margin' => $total_margin,
                'vertical-align' => 'middle'
            ));
        }

        $role = $this->getVariable('role');
        $subrole = $this->getVariable('subrole');

        $image[] = $this->getProfileImage('profilepic', true);
        $this->data->scroll[] = $this->getRow($image, array('text-align' => 'center'));
        $titlecol = [];

        //$titlecol[] = $this->getImage('toolbar-info.png',array('width' => '30','height' => '30', 'margin' => '10 0 0 0'));

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if ($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        if ($this->getSavedVariable('profilepic2')) {
            $titlecol[] = $this->getImage('del-photos.png', array('height' => '30', 'vertical-align' => 'bottom', 'floating' => '1', 'float' => 'right', 'font-size' => '16', 'onclick' => $onclick));
        }
        $this->data->scroll[] = $this->getRow($titlecol, array('margin' => '10 ' . $this->margin . ' 0 ' . $this->margin));

        if ($role == 'agent' && $subrole != 'landlord') {
            $info[] = $this->formkitField('branch_name', '{#branch_name#}', '{#branch_name#}', 'branch_name');
            $info[] = $this->formkitField('website', '{#website#}', '{#website#}', 'website');
            $info[] = $this->formkitField('postcode', '{#postcode#}', '{#postcode#}', 'postcode');
        }
        if ($role == 'tenant' || $subrole == 'landlord') {
            $info[] = $this->formkitField('first_name', '{#first_name#}', '{#first_name#}', 'text');
            $info[] = $this->formkitField('surname', '{#surname#}', '{#surname#}', 'text');
        } else {
            $info[] = $this->formkitField('real_name', '{#name#}', '{#name#}', 'text');
        }

        $info[] = $this->formkitField('country', '{#country#}', '{#country#}', 'text', false, false, $this->getActionidByPermaname('countrieshelper'));
        $info[] = $this->formkitField('country_code', '{#country_code#}', '{#country_code#}', 'text', false, false, $this->getActionidByPermaname('zipcodeshelper'));
        $info[] = $this->formkitField('phone', '{#phone#}', '{#your_phone#}', 'phone');
        $info[] = $this->formkitField('email', '{#email#}', '{#your_email#}', 'email');

        $text[] = $this->getText(strtoupper('{#info_about_yourself#}'), array(
            'font-size' => 13,
            'margin' => '0 0 0 15'
        ));
        $text[] = $this->getText('{#1200_chars_max#}', array(
            'font-size' => '10',
            'color' => '#666666',
            'floating' => 1,
            'float' => 'right',
            'margin' => '0 20 0 0'
        ));
        $info[] = $this->getRow($text);

        $placeholderText = $subrole == 'landlord' ? '{#tell_tenants_about_yourself#}' : '{#little_info_about_yourself#}';
        $info[] = $this->formkitTextarea('additional_info', '', $placeholderText);

        $this->data->scroll[] = $this->getColumn($info, array(
            'style' => 'propertydetail-shadowbox-indent'
        ));

        if ($this->getVariable('role') == 'tenant') {
            $this->data->scroll[] = $this->getSnippet('rentaltenantproperties');
        }

        if ($role == 'agent') {
            $settings[] = $this->getText(strtoupper('{#input_settings#}'), array(
                'font-size' => 13,
                'margin' => '0 0 0 15'
            ));

            $areaOptions = array(
                'sq_ft' => '{#sq_ft#}',
                'sq_meter' => '{#sq_meter#}'
            );

            $data = array(
                'variable' => 'area_settings',
                'field_offset' => 3
            );

            $settings[] = $this->formkitRadiobuttons(' ', $areaOptions, $data);

            $priceOptions = array(
                'price_per_month' => '{#price_per_month#}',
                'price_per_week' => '{#price_per_week#}',
            );

            $data = array(
                'variable' => 'price_settings',
                'field_offset' => 3
            );

            $settings[] = $this->formkitRadiobuttons(' ', $priceOptions, $data);

            $this->data->scroll[] = $this->getColumn($settings, array(
                'style' => 'propertydetail-shadowbox-indent'
            ));
        }

        if ($this->errors) {
            $this->data->scroll[] = $this->getSpacer(10);
            foreach ($this->errors as $error) {
                $this->data->scroll[] = $this->getText($error, array('text-align' => 'center', 'color' => '#CB232A'));
            }
        }

        $this->data->scroll[] = $this->getText('{#we_do_not_share_your_private_information#}', array('margin' => '10 24 10 24', 'text-align' => 'center', 'font-size' => 12, 'color' => '#CB232A'));
        $this->data->scroll[] = $this->saveButton();

    }

    public function getProfileImage($name, $mainimage = false)
    {

        if ($mainimage) {
            $params['width'] = $this->grid * 2 + $this->margin;
            $params['height'] = $this->grid * 2 + $this->margin;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        } else {
            $params['width'] = $this->grid;
            $params['height'] = $this->grid;
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        }

        $params['imgcrop'] = 'yes';
        $params['crop'] = 'round';
        $params['defaultimage'] = 'profile-add-photo-grey.png';

        if ($this->deleting AND $this->getSavedVariable($name) AND strlen($this->getSavedVariable($name)) > 2) {
            $params['opacity'] = '0.6';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = 'imgdel-' . $name;
        } else {
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '600';
            $params['onclick']->variable = $this->getVariableId($name);
            $params['onclick']->action_config = $this->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $params['border-width'] = '5';
        $params['border-color'] = '#FFFFFF';
        $params['border-radius'] = ($this->grid * 2 + $this->margin) / 2;
        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';
        $params['priority'] = 9;

        return $this->getImage($this->getVariable($name), $params);
    }

    public function instaInfo($vars)
    {

        if (!isset($vars['instagram_username'])) {
            return false;
        }

        $username = isset($vars['instagram_username']) ? $vars['instagram_username'] : '{#unknown#}';
        $mediacount = isset($vars['instagram_media_count']) ? $vars['instagram_media_count'] : '{#unknown#}';
        $followers = isset($vars['instagram_followed_by']) ? $vars['instagram_followed_by'] : '{#unknown#}';

        $this->data->scroll[] = $this->getSettingsTitle('{#instagram_info#}');
        $this->data->scroll[] = $this->getText('{#name#}: ' . $username, array('style' => 'profile_info_fields'));
        $this->data->scroll[] = $this->getText('{#followers#}: ' . $followers, array('style' => 'profile_info_fields'));
        $this->data->scroll[] = $this->getText('{#photos#}: ' . $mediacount, array('style' => 'profile_info_fields'));

        if (isset($vars['instagram_bio']) AND strlen($vars['instagram_bio'] > 5)) {
            $this->data->scroll[] = $this->getSettingsTitle('{#instagram_bio#}');
            $this->data->scroll[] = $this->getText('username:' . $username);
        }

        $link = new stdClass();
        $link->action = 'open-url';
        $link->open_popup = 1;
        $link->action_config = 'https://www.instagram.com/' . $username;

        $this->data->scroll[] = $this->getSpacer('15');
        $this->data->scroll[] = $this->getTextbutton('{#open_instagram_profile#}', array('onclick' => $link, 'style' => 'general_button_style_red', 'id' => 'whatever'));
    }

    public function getImageScroll($vars)
    {

        if (!isset($vars['profilepic'])) {
            return $this->getText('{#no_profile_pic#}');
        }

        $params['imgwidth'] = '600';
        $params['imgheight'] = '400';
        $params['imgcrop'] = 'yes';
        $params['height'] = round($this->screen_width / 1.5, 0);
        $params['opacity'] = '0.2';
        $params['width'] = $this->screen_width;
        $params['not_to_assetlist'] = true;
        $params['priority'] = 9;
        //$params['mask'] = 'mask-sample.png';

        $filename = $this->getImageFileName($vars['profilepic'], $params);

        $this->data->scroll[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getImage($vars['profilepic'], array(
                    'imgwidth' => '400',
                    'imgheight' => '400',
                    'width' => round($this->screen_width / 2.5),
                    'height' => round($this->screen_width / 2.5),
                    'crop' => 'round',
                    'priority' => 9,
                    'not_to_assetlist' => true,
                )),
            ), array(
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'background-color' => '#BF2B2B42',
                'height' => round($this->screen_width / 1.5, 0),
            )),
        ), array(
            'background-image' => $filename,
            'background-size' => 'cover',
            'height' => round($this->screen_width / 1.5, 0),
        ));

        return true;
    }

    public function places($vars)
    {

        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#clubs_played#}', array('height' => '30', 'margin' => '10 0 0 0'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));
        $this->data->scroll[] = $this->getSpacer('10');

        Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->profileid;
        $this->mobileplacesobj->game_id = $this->gid;
        $this->mobileplacesobj->places_wantsee = json_decode($vars['places_wantsee'], true);
        $this->mobileplacesobj->places_havebeen = json_decode($vars['places_havebeen'], true);

        $wordlist = $this->mobileplacesobj->getMyClubs();

        if (!empty($wordlist)) {
            foreach ($wordlist as $word) {
                $this->setPlaceSimple($word);
            }
        }

        $titlecol2[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol2[] = $this->getText('{#clubs_want_to_visit#}', array('height' => '30', 'margin' => '10 0 0 0'));
        $this->data->scroll[] = $this->getRow($titlecol2, array('width' => '100%', 'margin' => '10 0 0 12'));
        $this->data->scroll[] = $this->getSpacer('10');

        $wordlist = $this->mobileplacesobj->getMyWishlist();

        foreach ($wordlist as $word) {
            $this->setPlaceSimple($word);
        }
    }


    public function homeClub($vars)
    {

        $club_name = '{#no_home_club_set#}';

        if (isset($vars['home_club'])) {
            $clubinfo = MobileplacesModel::model()->findByPk($vars['home_club']);

            if (isset($clubinfo->name)) {
                $club_name = $clubinfo->name;
            }
        }

        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#home_club#} : ' . $club_name, array('height' => '30', 'margin' => '10 0 0 0', 'font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));

    }


    public function hcp($vars)
    {
        $titlecol[] = $this->getImage('hcp-icon3.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#HCP#} : ' . $vars['hcp'], array('height' => '30', 'margin' => '10 0 0 0', 'font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));

    }

    public function availabilityView($vars)
    {
        $titlecol[] = $this->getImage('calendar-icon-availability2.png', array('width' => '30', 'height' => '30', 'margin' => '10 0 0 0'));
        $titlecol[] = $this->getText('{#general_availability_for#} ' . $this->getFirstName($vars), array('height' => '30', 'margin' => '10 0 0 0', 'font-size' => '13'));
        $this->data->scroll[] = $this->getRow($titlecol, array('width' => '100%', 'margin' => '10 0 0 12'));

        $this->availability = json_decode($vars['availability'], true);
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getDayMorningAfternoon('monday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('tuesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('wednesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('thursday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('friday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('saturday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('sunday');
        $this->data->scroll[] = $this->getSpacer('10');
    }

    public function getDayMorningAfternoon($daytitle)
    {
        $col[] = $this->getText('{#' . $daytitle . '#}', array('style' => 'selector_daytitle'));

        $varname = $daytitle . '_morning';
        if (isset($this->availability[$varname])) {
            $col[] = $this->getText('{#morning#}', array('style' => 'selector_day_selected'));
        } else {
            $col[] = $this->getText('{#morning#}', array('style' => 'selector_day'));
        }

        $varname = $daytitle . '_afternoon';
        if (isset($this->availability[$varname])) {
            $col[] = $this->getText('{#afternoon#}', array('style' => 'selector_day_selected'));
        } else {
            $col[] = $this->getText('{#afternoon#}', array('style' => 'selector_day'));
        }

        return $this->getRow($col, array('margin' => '4 15 4 10'));
    }


    public function setPlaceSimple($data)
    {
        if (!isset($data['logo']) OR $data['logo'] == 'dummylogo.png') {
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $col[] = $this->getImage($data['logo'], array('width' => '15%', 'vertical-align' => 'middle'));
        $col[] = $this->getPlaceRowPart($data, '100%');
        $this->data->scroll[] = $this->getRow($col, array('margin' => '0 15 2 15', 'padding' => '5 5 5 5'));
    }

    public function getPlaceRowPart($data, $width = '55%')
    {
        $distance = round($data['distance'], 0) . 'km';
        $id = $data['id'];

        $openinfo = new StdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        $row[] = $this->getText($data['name'], array('background-color' => '#ffffff', 'padding' => '3 5 3 5', 'color' => '#000000', 'font-size' => '12'));
        $row[] = $this->getText($data['county'], array('background-color' => '#ffffff', 'padding' => '0 5 3 5', 'color' => '#000000', 'font-size' => '11'));
        $row[] = $this->getText($data['city'] . ', ' . $distance, array('background-color' => '#ffffff', 'padding' => '0 5 3 5', 'color' => '#000000', 'font-size' => '11'));
        return $this->getColumn($row, array('width' => $width, 'onclick' => $openinfo));
    }

    public function getTenantSearches($vars, $descriptionStyle)
    {

        if (!isset($vars['recent_districts']) OR empty($vars['recent_districts'])) {
            return false;
        }

        $data = json_decode($vars['recent_districts'], true);

        if (empty($data)) {
            return false;
        }

        $entries = array(
            $this->getText('{#regions_of_most_recent_search#} ', array('style' => 'psbox_title')),
            $this->getSpacer(5),
        );

        $count = 0;
        foreach ($data as $entry) {

            if (!isset($entry['region'])) {
                continue;
            }

            $count++;

            if ($count > 3) {
                continue;
            }

            $entries[] = $this->getText($entry['region'], array('style' => 'psbox_textarea'));
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn($entries, array(
                'style' => $descriptionStyle
            )),
        ), array(
            'style' => 'rentals_shadow_box'
        ));

        return true;
    }

    public function saveButton()
    {
        if ($this->menuid == 'save-data') {
            $this->initMobileMatching();
            $this->mobilematchingobj->turnUserToItem(false, __FILE__);
            $cachename = 'uservars-' . $this->playid;
            Appcaching::removeGlobalCache($cachename);
            return $this->getTextbutton(strtoupper('{#saved#}'), array('style' => 'rentit-submit-button-profile', 'id' => 'save-data'));
        } else {
            return $this->getTextbutton(strtoupper('{#save#}'), array('style' => 'rentit-submit-button-profile', 'id' => 'save-data'));
        }
    }

    public function getFamilyStatus($vars)
    {
        if (!isset($vars['family_situation'])) {
            return '{#unknown#}';
        }

        $status = $vars['family_situation'];

        $options = array(
            'myself' => "{#it's_just_myself#}",
            'partner' => "{#i'm_living_with_a_partner#}",
            'friends' => "{#we're_friends_sharing#}",
            'family' => "{#we're_a_family_with_kids#}"
        );

        if (isset($options[$status])) {
            return $options[$status];
        }

        return '{#unknown#}';

    }

    public function getResidenceOptions($vars)
    {
        if (!isset($vars['residence_status'])) {
            return '{#unknown#}';
        }

        $status = $vars['residence_status'];

        $options = array(
            'professional' => '{#i_am_professional#}',
            'student' => '{#i_am_a_student#}'
        );

        if (isset($options[$status])) {
            return $options[$status];
        }

        return '{#unknown#}';
    }

    public function profileEditSaves()
    {
        if ($this->menuid == 'save-data') {
            $this->validateInput();

            if (!$this->errors) {
                if ($this->getVariable('role') == 'tenant' || $this->getVariable('subrole') == 'landlord') {
                    $this->saveVariable('name', $this->getVariable('first_name') . ' ' . $this->getVariable('surname'));
                    $this->saveVariable('real_name', $this->getVariable('first_name') . ' ' . $this->getVariable('surname'));
                }

                $this->saveVariables();
                $this->loadVariableContent(true);

                // Force a list-branches call
                // This is required as the new settings need to be applied for the properties listing
                $this->data->onload[] = $this->getOnclick('list-branches');
            }
        }

        if (strstr($this->menuid, 'imgdel-')) {
            $del = str_replace('imgdel-', '', $this->menuid);
            if ($del == 'profilepic') {
                $this->bumpUpProfilePics();
            } else {
                $this->deleteVariable($del);
                $this->loadVariableContent(true);
            }
        }
    }

    protected function validateInput()
    {

        $email_var = $this->getVariableId('email');

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if (empty($this->submitvariables[$email_var])) {
            $this->errors[] = '{#the_email_field_could_not_be_blank#}';
        } else if (!$validator->validateValue($this->submitvariables[$email_var])) {
            $this->errors[] = '{#please_enter_a_valid_email_address#}';
        }

        if ($this->getVariable('role') == 'tenant' || $this->getVariable('subrole') == 'landlord') {
            $first_name = $this->getVariableId('first_name');
            $surname = $this->getVariableId('surname');

            if (empty($this->submitvariables[$first_name])) {
                $this->errors[] = '{#first_name_field_could_not_be_blank#}';
            }

            if (empty($this->submitvariables[$surname])) {
                $this->errors[] = '{#surname_field_could_not_be_blank#}';
            }
        } else {
            $name_var = $this->getVariableId('real_name');

            if (empty($this->submitvariables[$name_var])) {
                $this->errors[] = '{#the_name_field_could_not_be_blank#}';
            }
        }

        $additionalInfoId = $this->getVariableId('additional_info');
        $description_field_lenght = strlen($this->submitvariables[$additionalInfoId]);
        if ($description_field_lenght > 1200) {
            $this->errors[] = '{#additional_info_must_be_1200_characters_or_less#}';
        }

    }

}