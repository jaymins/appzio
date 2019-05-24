<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class SettingsMobileproperties extends MobilepropertiesController
{

    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;

    public function tab1()
    {
        $this->initModel();
        $this->data = new stdClass();

        $onload = new StdClass();
        $onload->action = 'list-branches';
        $this->data->onload[] = $onload;

        switch ($this->menuid) {
            case 'save-settings':
                MobilepropertiesSettingModel::saveSubmit($this->submitvariables, $this->playid, $this->getSavedVariable('temp_district_filtering'), $this->gid);
                $this->showSettingsForm();
                break;

            case 'clear-settings':
                if ($this->playid) {
                    MobilepropertiesSettingModel::model()->deleteAllByAttributes(array(
                        'play_id' => $this->playid,
                        'game_id' => $this->gid
                    ));

                    $this->deleteVariable('temp_user_search_term');
                    $this->deleteVariable('temp_district_filtering');
                }
                $this->no_output = true;
                break;

            default:
                $this->showSettingsForm();
                break;
        }

        return $this->data;
    }

    public function tab2()
    {
        $this->initModel();
        $this->data = new stdClass();
        $this->listSubmits();

        $col[] = $this->getImage('navi-back-arrow.png', array('height' => '25', 'width' => '25'));
        $col[] = $this->getText('{#filter_by_districts#}', array('width' => $this->screen_width - 100, 'text-align' => 'center', 'color' => '#ffffff'));

        $this->data->header[] = $this->getRow($col, array('background-color' => $this->color_topbar, 'height' => '40', 'padding' => '8 4 8 4',
            'onclick' => $this->getOnclick('tab1')));

        $value = '';
        if (isset($this->submitvariables['searchterm-location']) AND !empty($this->submitvariables['searchterm-location'])) {
            $value = $this->submitvariables['searchterm-location'];
        }

        $args = array(
            'style' => 'search-filter-location',
            'hint' => '{#enter_a_postcode_or_an_address#}',
            'submit_menu_id' => 'searchbox-location',
            'variable' => 'searchterm-location',
            'id' => 'something',
        );

        $this->data->header[] = $this->getRow(array(
            $this->getFieldtext($value, $args),
            $this->getTextbutton('Search', array('id' => 'do-google-search', 'style' => 'google-search-btn')),
        ), array('background-color' => $this->color_topbar));

        $recent = false;

        $this->data->scroll[] = $this->getLoader('Loading', array('color' => '#000000', 'visibility' => 'onloading'));

        if (
            ($this->menuid == 'searchbox-location' OR $this->menuid == 'do-google-search') AND
            (isset($this->submitvariables['searchterm-location']) AND !empty($this->submitvariables['searchterm-location']))
        ) {
            $searchterm = trim($this->submitvariables['searchterm-location']);

            // Save the user's search term
            $this->saveVariable('temp_user_search_term', $searchterm);

            $apikey = Aemobile::getConfigParam($this->gid, 'google_maps_api');

            if (!$apikey) {
                $apikey = 'AIzaSyBOh1EACK1VOkmLVjx50RAEP-D7XNjIhiE';
            }

            $places_module = new ArticlePlacesSearch($apikey);

            if (isset($this->varcontent['lat']) AND isset($this->varcontent['lon'])) {
                $places_module->location = array($this->varcontent['lat'], $this->varcontent['lon']);
            } else {
                $places_module->location = array('51.5074', '0.1278'); // London
            }

            $places_module->radius = 50000;
            $places_module->rankby = 'distance';
            $places_module->types = 'geocode';
            $places_module->input = urlencode($searchterm);

            $data = $places_module->autocomplete();

            if (!isset($data['predictions']) OR empty($data['predictions'])) {
                $onclick = $this->getOnclick('id', false, 'addregion_' . json_encode($searchterm));

                $this->data->scroll[] = $this->getText('{#looks_like_your_address_is_not_valid#}. {#you_can_still_use_it#}', array('style' => 'search-result', 'onclick' => $onclick));
                $this->data->scroll[] = $this->getText($searchterm, array('style' => 'search-result-large', 'onclick' => $onclick));
                $this->data->footer[] = $this->getText('Use this location', array('style' => 'submit-button', 'onclick' => $onclick));
            } else {
                $predictions = $data['predictions'];

                foreach ($predictions as $prediction) {

                    if (!isset($prediction['description'])) {
                        continue;
                    }

                    $predicted_address = $prediction['description'];

                    $this->data->scroll[] = $this->getRow(array(
                        $this->getText($predicted_address, array('style' => 'text-location-name-bold')),
                    ), array(
                        'style' => 'location-info-row',
                        'onclick' => $this->getOnclick('id', false, 'addregion_' . json_encode($predicted_address)), // dirty ..
                    ));

                }
            }

        }

        if ($this->menuid != 'searchbox-location' AND $this->menuid != 'do-google-search' OR empty($this->submitvariables['searchterm-location'])) {
            $this->displayRecentSearches();
        }

        $this->collectionBanner();

        return $this->data;
    }

    public function displayRecentSearches()
    {
        $recents = $this->propertyModel->getRecentDistricts();

        if (empty($recents)) {
            return false;
        }

        $this->data->scroll[] = $this->getText('{#your_recent_searches#}', array(
            'background-color' => $this->color_topbar,
            'color' => '#ffffff',
            'padding' => '7 0 11 8',
            'text-align' => 'left',
            'font-size' => '16'
        ));

        foreach (array_reverse($recents) as $i => $region) {

            if (!isset($region['postcode']) OR !isset($region['region'])) {
                continue;
            }

            // Show top 3 results
            if ($i > 2) {
                continue;
            }

            $onclick = $this->getOnclick('id', false, 'addregion_' . json_encode($region['postcode']));
            $this->data->scroll[] = $this->getText($region['postcode'] . ', ' . $region['region'], array(
                'style' => 'filter-search-result',
                'onclick' => $onclick
            ));
        }
    }

    public function tab3()
    {
        $this->initModel();
        $this->data = new stdClass();

        if (strstr($this->menuid, 'removeregion_')) {
            $id = str_replace('removeregion_', '', $this->menuid);
            $id = str_replace('^', ' ', $id);
            $this->removeFromVariable('temp_district_filtering', $id);
        }

        $col[] = $this->getImage('navi-back-arrow.png', array('height' => '25', 'width' => '25'));
        $col[] = $this->getText('{#district_filtering#}', array('width' => $this->screen_width - 100, 'text-align' => 'center', 'color' => '#ffffff'));

        $this->data->header[] = $this->getRow($col, array('background-color' => $this->color_topbar, 'height' => '40', 'padding' => '8 4 8 4',
            'onclick' => $this->getOnclick('tab2')));
        unset($col);

        $recents = $this->getSavedVariable('temp_district_filtering');
        $recents = json_decode($recents, true);

        if (is_array($recents)) {
            foreach ($recents as $region) {
                $onclick = $this->getOnclick('id', false, 'removeregion_' . str_replace(' ', '^', $region));
                $col[] = $this->getText($region);
                $col[] = $this->getImage('apple-delete-icon.png', array('floating' => '1', 'float' => 'right', 'height' => '20', 'onclick' => $onclick));
                $this->data->scroll[] = $this->getRow($col, array('style' => 'filter-search-result'));
                unset($col);
            }
        }

        $this->collectionBanner();

        return $this->data;
    }

    public function listSubmits()
    {
        if (strstr($this->menuid, 'addregion_')) {

            $data_raw = str_replace('addregion_', '', $this->menuid);
            $temp_address = json_decode($data_raw);

            $apikey = Aemobile::getConfigParam($this->gid, 'google_maps_api');

            if (!$apikey) {
                $apikey = 'AIzaSyBOh1EACK1VOkmLVjx50RAEP-D7XNjIhiE';
            }

            $maps_module = new ArticleGmapsSearch($apikey);
            $maps_module->search_param = $temp_address;
            $results = $maps_module->getInfoLocation();
            $address = $maps_module->getAddress();
            $coords = $maps_module->getCoords();
            $postal_code = $maps_module->getAddressComponent('postal_code');
            $data = $maps_module->getData();

            if (empty($postal_code) AND $coords) {
                // Get the actual address using the retrieved coords
                $maps_module_coords = new ArticleGmapsSearch($apikey);
                $maps_module_coords->search_type = 'coords';
                $maps_module_coords->search_param = implode(',', $coords);

                $results_coords = $maps_module_coords->getInfoLocation();
                $postal_code = $maps_module_coords->getAddressComponent('postal_code');
                // $address = $maps_module_coords->getAddress();
            }

            if ($address AND isset($postal_code['long_name'])) {
                $this->propertyModel->addToRecentDistricts($temp_address, $address);
                // $this->saveVariable('temp_district', $address);
                $this->addToVariable('temp_district_filtering', $postal_code['long_name']);
            }

        }

        if ($this->menuid == 'clear') {
            $this->deleteVariable('temp_user_search_term');
            $this->deleteVariable('temp_district_filtering');
        }
    }

    public function collectionBanner()
    {

        $items = $this->getSavedVariable('temp_district_filtering');
        $items = json_decode($items, true);

        if (empty($items) AND !isset($items[0])) {
            return false;
        }

        $count = count($items);
        $listing = $items[0];
        $listing .= isset($items[1]) ? '...' : '';

        $filter[] = $this->getText($count, array('style' => 'filter_count'));

        $col[] = $this->getText($count, array('style' => 'filter_count'));
        $col[] = $this->getImage('blue-location-marker.png', array('width' => '40', "margin" => "7 3 5 3"));
        $col[] = $this->getText($listing, array('font-size' => '16', "margin" => "0 7 3 7"));
        $col[] = $this->getText('');

        $btns[] = $this->getText('{#clear#}', array('style' => 'filter_btn_clear', 'onclick' => $this->getOnclick('id', false, 'clear')));
        $btns[] = $this->getText('{#ready#}', array('style' => 'filter_btn_ready', 'onclick' => $this->getOnclick('tab1')));

        $col[] = $this->getRow($btns, array('floating' => 1, 'float' => 'right'));
        $this->data->footer[] = $this->getRow($col, array('vertical-align' => 'middle', 'background-color' => '#E9E9E9',
            'onclick' => $this->getOnclick('tab3'), 'height' => '50'
        ));
    }

    public function showSettingsForm()
    {
        $settings = MobilepropertiesSettingModel::getSettings($this->playid, $this->gid);

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('suggest_location_action');
        $onclick->id = 'tmp-property';
        $onclick->open_popup = 1;
        $onclick->sync_open = 1;
        $onclick->back_button = 1;
        $onclick->keep_user_data = 1;

        $districts = $this->getSavedVariable('temp_district_filtering') ? json_decode($this->getSavedVariable('temp_district_filtering'), true) : '{#no_district_limitation#}';
        if (is_array($districts)) {
            $districts = mb_substr(implode(', ', $districts), 0, 150);
        }

        $data = array();
        // $data[] = $this->getImage('address-icon.png', array('width' => '20', 'margin' => '10 0 0 10'));
        $data[] = $this->getText($districts, array(
            'variable' => 'temp_district',
            'style' => 'mobileproperty_textbutton',
        ));

        $this->data->scroll[] = $this->getRow($data, array(
            'style' => 'mobileproperty_input',
            'onclick' => $this->getOnclick('tab2')
        ));

        if ($this->getVariable('role') == 'tenant') {
            $this->data->scroll[] = $this->getSpacer('10');
            $this->getUnitSettingsFields($settings);
        }

        $propertyValues = array();

        if ($settings->type_flat) {
            $propertyValues['flat'] = 1;
        }
        if ($settings->type_room) {
            $propertyValues['room'] = 1;
        }
        if ($settings->type_house) {
            $propertyValues['house'] = 1;
        }

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

        $optionsValues = array('furnished' => 1, 'unfurnished' => 1);

        if ($settings->furnished == 1) {
            $optionsValues['unfurnished'] = 0;
        } else if ($settings->furnished == 0 && !is_null($settings->furnished)) {
            $optionsValues['furnished'] = 0;
        }

        $optionsValues['pets_allowed'] = $settings->options_pets_allowed;
        $optionsValues['outside_spaces'] = $settings->options_outside_spaces;

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
        $slider[] = $this->getRangeslider($settings->from_num_bedrooms, array(
            'style' => 'mobileproperty_rangeslider',
            'min_value' => 0,
            'max_value' => 10,
            'step' => 1,
            'variable' => 'from_num_bedrooms',
            'value' => $settings->from_num_bedrooms,
        ));

        $slider[] = $this->getRow(array(
            $this->getText('0', array('style' => 'slider-values')),
            $this->getText($settings->from_num_bedrooms, array('variable' => 'from_num_bedrooms', 'style' => 'slider-values-center')),
            $this->getText('', array('style' => 'slider-values-center-2')),
            $this->getText('10', array('style' => 'slider-values-right')),
        ), array('margin' => '0 30 10 30',));

        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

        $slider = array();
        $slider[] = $this->getText('{#max_bedrooms#}', array('style' => 'mobileproperty_fieldlist_label'));
        $slider[] = $this->getRangeslider($settings->to_num_bedrooms, array(
            'style' => 'mobileproperty_rangeslider',
            'min_value' => 0,
            'max_value' => 10,
            'step' => 1,
            'variable' => 'to_num_bedrooms',
            'value' => $settings->to_num_bedrooms
        ));

        $slider[] = $this->getRow(array(
            $this->getText('0', array('style' => 'slider-values')),
            $this->getText($settings->to_num_bedrooms, array('variable' => 'to_num_bedrooms', 'style' => 'slider-values-center')),
            $this->getText('', array('style' => 'slider-values-center-2')),
            $this->getText('10', array('style' => 'slider-values-right')),
        ), array('margin' => '0 30 10 30',));

        $this->data->scroll[] = $this->getColumn($slider, array('style' => 'mobileproperty_rangeslider_wrapper'));

        $fromPrice = $settings->from_price_per_month;
        $toPrice = $settings->to_price_per_month;
        if ($settings->filter_price_per_week == 'price_per_week') {
            $fromPrice = ceil(($settings->from_price_per_month * 12) / 52);
            $toPrice = ceil(($settings->to_price_per_month * 12) / 52);
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getFieldtext($fromPrice, array('variable' => 'from_price_per_month', 'hint' => '{#min_price#}', 'style' => 'mobileproperty_textfield'))
        ), array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $this->data->scroll[] = $this->getColumn(array(
            $this->getFieldtext($toPrice, array('variable' => 'to_price_per_month', 'hint' => '{#max_price#}', 'style' => 'mobileproperty_textfield'))
        ), array(
            'margin' => '10 10 10 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $clicker_clear[] = $this->getOnclick('id', true, 'clear-settings');
        $clicker_clear[] = $this->getOnclick('close-popup');

        $clicker_save[] = $this->getOnclick('id', true, 'save-settings');
        $clicker_save[] = $this->getOnclick('close-popup');

        $col[] = $this->getTextbutton(strtoupper('{#clear_filters#}'),
            array('id' => 'save-settings', 'style' => 'filtering-warning-button', 'onclick' => $clicker_clear));

        $col[] = $this->getTextbutton(strtoupper('{#save#}'),
            array('id' => 'save-settings', 'style' => 'filtering-save-button', 'onclick' => $clicker_save));

        $this->data->footer[] = $this->getRow($col);

    }

    public function getUnitSettingsFields($settings)
    {
        $areaFilter = $settings->filter_sq_ft;

        if (empty($areaFilter)) {
            $areaFilter = 'sq_ft';
        }

        $priceFilter = $settings->filter_price_per_week;

        if (empty($priceFilter)) {
            $priceFilter = 'price_per_month';
        }

        $searchFields = [];
        $areaOptions = array(
            'sq_ft' => '{#sq_ft#}',
            'sq_meter' => '{#sq_meter#}'
        );

        $data = array(
            'variable' => 'filter_sq_ft',
            'field_offset' => 3,
            'value' => $areaFilter
        );

        $searchFields[] = $this->formkitRadiobuttons('', $areaOptions, $data);

        $priceOptions = array(
            'price_per_month' => '{#price_per_month#}',
            'price_per_week' => '{#price_per_week#}',
        );

        $data = array(
            'variable' => 'filter_price_per_week',
            'field_offset' => 3,
            'value' => $priceFilter
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

}