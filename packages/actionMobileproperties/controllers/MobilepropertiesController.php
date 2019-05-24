<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileproperties.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.controllers.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobilepropertiesController extends MobilematchingController
{

    public $data;
    public $theme;
    public $grid;
    public $margin;
    public $deleting;
    /** @var MobilepropertiesModel */
    public $propertyModel;
    public $currentId;
    public $errors;
    public $settings;

    public $action_mode;

    /**
     * Main entry point of the controller
     *
     * @return stdClass
     */
    public function tab1()
    {
        $this->initModel();
        $this->data = new stdClass();

        $this->migrateUserRoles();

        $this->rewriteActionConfigField('background_color', '#f6f6f6');

        $this->action_mode = $this->getConfigParam('mode');

        $this->askLocation();

        $this->settings = MobilepropertiesSettingModel::findOrFail($this->playid, $this->gid);

        // Handle different action modes here
        switch ($this->action_mode) {

            case 'favourites':
                $properties = MobilepropertiesBookmarkModel::getBookmarkedProperties($this->playid, $this->gid);
                $this->listProperties($properties);
                return $this->data;
                break;

            case 'inactive': // Show a list of the agent's unavailable properties

                $properties = MobilepropertiesModel::model()->findAllByAttributes(array(
                    'play_id' => $this->playid,
                    'available' => 0
                ));

                $this->listProperties($properties);
                return $this->data;

                break;
        }


        $requested_tab = $this->getRequestedTab();

        switch ($requested_tab) {

            case 'show-filters':
                $this->showFilters();
                break;

            case 'filter-properties':
                $properties = $this->propertyModel->filterProperties($this->submitvariables);
                $this->listProperties($properties);
                break;

            default:
                $this->listProperties();
                break;
        }

        return $this->data;
    }


    public function getRequestedTab()
    {
        if ($this->menuid == false && $this->action_mode == 'main') {
            return false;
        }

        $var = 'saved_tab_request';
        $saved_request = $this->getVariable($var);

        if ($saved_request AND empty($this->menuid)) {
            return $saved_request;
        }

        $options = array(
            'add', 'del-images-', 'show-filters', 'filter-properties', 'go-back'
        );

        foreach ($options as $option) {
            if (preg_match("~$option~", $this->menuid)) {
                $this->saveVariable($var, $option);
                return $option;
            }
        }

        return $this->menuid;
    }


    /**
     * Show all properties
     *
     * @param array|null $properties
     */
    public function listProperties(array $properties = null)
    {

        $this->rewriteActionConfigField('backarrow', false);

        if (is_null($properties)) {
            $properties = MobilepropertiesModel::model()->findAllByAttributes(array(
                'play_id' => $this->playid,
                'game_id' => $this->gid,
                'available' => 1
            ));
        }

        $this->setGridWidths();

//        if (($this->action_mode == 'main' || $this->action_mode == 'favourites') && count($properties) >= 20) {
//            $this->data->scroll[] = $this->getTextbutton('{#advanced_filters#}', array('id' => 'show-filters'));
//        }

        if (!$properties) {
            $this->data->scroll[] = $this->getText('{#no_properties_yet#}', array('style' => 'rentals-info-message-text'));
        } else {
        	$this->getPropertiesView( $properties );
        }

        $onclick = new stdClass();
        $onclick->id = 'add';
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $this->getConfigParam('add_new');
        $onclick->config = $this->getConfigParam('add_new');
        $onclick->sync_open = 1;

        if ($this->action_mode == 'main') {
            $this->data->footer[] = $this->getImage('addbtn-bluegreen.png', array('onclick' => $onclick,'margin' => '0 0 0 0'));
        }
    }

    private function getPropertiesView( $properties ) {

	    $is_scrollable = false;
	    $next_page_id = 1;
	    
	    if ( count($properties) > 10 ) {
		    $chunks = array_chunk( $properties, 10, true );

		    $next_page_id = 1;

		    if ( isset($this->submit['next_page_id']) ) {
			    $next_page_id = $this->submit['next_page_id'] + 1;
		    }

		    $data = ( isset($chunks[$next_page_id-1]) ? $chunks[$next_page_id-1] : array() );
		    $is_scrollable = true;
	    } else {
		    $data = $properties;
	    }

	    $results = $this->populateProperties( $data );

	    if ( !empty($results) ) {
		    if ( $is_scrollable ) {
			    $this->data->scroll[] = $this->getInfinitescroll( $results, array( 'next_page_id' => $next_page_id ) );
		    } else {

			    foreach ( $results as $result ) {
				    $this->data->scroll[] = $result;
			    }

		    }
	    } else {
		    $this->data->scroll[] = $this->getText( '{#no_more_properties#}!', array( 'style' => 'properties-notification' ) );
	    }

		return true;
    }

    private function populateProperties( $properties ) {
	    $output = array();

	    foreach ($properties as $property) {
		    $output[] = $this->renderSingleProperty($property);
	    }

	    return $output;
    }

    public function showFilters()
    {
        $this->data->scroll[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield', 'hint' => 'District', 'variable' => 'district'));

        $column = [];
        $column[] = $this->getFieldonoff(0, array('style' => 'mobileform_formfield', 'variable' => 'furnished'));
        $column[] = $this->getText('{#furnished#}', array('style' => 'mobileproperty_onoff_label'));
        $this->data->scroll[] = $this->getRow($column, array('style' => 'mobileproperty_onoff'));

        $column = [];
        $column[] = $this->getFieldonoff(0, array('style' => 'mobileform_formfield', 'variable' => 'unfurnished'));
        $column[] = $this->getText('{#unfurnished#}', array('style' => 'mobileproperty_onoff_label'));
        $this->data->scroll[] = $this->getRow($column, array('style' => 'mobileproperty_onoff'));

        $this->data->scroll[] = $this->getText('Price', array('style' => 'mobileproperty_filter_label'));

        $column = [];
        $column[] = $this->getText('{#from#}', array());
        $column[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield_filter', 'hint' => '{#from#}', 'variable' => 'from_price_per_month'));
        $column[] = $this->getText('{#to#}', array());
        $column[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield_filter', 'hint' => '{#to#}', 'variable' => 'to_price_per_month'));
        $this->data->scroll[] = $this->getRow($column, array('style' => 'mobileproperty_onoff'));

        $this->data->scroll[] = $this->getText('{#size#} (m2)', array('style' => 'mobileproperty_filter_label'));

        $column = [];
        $column[] = $this->getText('{#from#}', array());
        $column[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield_filter', 'hint' => '{#from#}', 'variable' => 'from_square_meters'));
        $column[] = $this->getText('{#to#}', array());
        $column[] = $this->getFieldtext('', array('style' => 'mobileproperty_textfield_filter', 'hint' => '{#to#}', 'variable' => 'to_square_meters'));
        $this->data->scroll[] = $this->getRow($column, array('style' => 'mobileproperty_onoff'));

        $this->data->footer[] = $this->getTextbutton('{#view_my_rentals#}', array('id' => 'filter-properties'));
    }

    /**
     * Initialize the property model
     */
    public function initModel()
    {
        $this->propertyModel = new MobilepropertiesModel();
        $this->propertyModel->game_id = $this->gid;
        $this->propertyModel->play_id = $this->playid;
        $this->propertyModel->factoryInit($this);

        /* this is one way to do it, you simply pass the submitted
        variables to your model and do the saving there */
        $this->propertyModel->submitvariables = $this->submitvariables;
    }

    /**
     * Get property input field with the appropriate data and styles
     *
     * @param $value
     * @param $variable
     * @param $hint
     * @param string $style
     * @param null $image
     * @param string $imageOffset
     * @return mixed
     */
    public function getPropertyInput($value, $variable, $hint, $style = 'mobileproperty_input', $image = null, $imageOffset = '10 0 0 10')
    {
        $data = array();

        if ( !is_null($image) ) {
            $data[] = $this->getImage($image, array('width' => '20', 'margin' => $imageOffset));
        }

        $params = array('style' => 'mobileproperty_textfield', 'hint' => $hint, 'variable' => $variable);

        $number_vars = array(
            'temp_square_ft', 'temp_price_per_month', 'temp_num_bedrooms', 'temp_num_bathrooms', 'temp_area', 'temp_price',
        );

        if ( in_array($variable, $number_vars) ) {
            $params['input_type'] = 'number';
        }

        if ( $variable == 'temp_name' ) {
            $params['input_type'] = 'name';
        }

        if ( empty($value) ) {
            $value = $this->getSubmitVariable( $variable );
        }

        $data[] = $this->getFieldtext($value, $params);
        return $this->getRow($data, array('style' => $style));
    }

    /**
     * Get district selection field
     *
     * @param $district
     * @return mixed
     */
    public function getDistrictField($district, $onclick = null)
    {

        // $this->deleteVariable('temp_district');

        if ($district) {
            $district_text = $district;
            $submitted_district = $district_text;
        } elseif ($this->getSavedVariable('temp_district')) {
            $district_text = $this->getSavedVariable('temp_district');
            $submitted_district = $district_text;
        } elseif ($this->getSubmittedVariableByName( 'temp_district' )) {
            $district_text = $this->getSubmittedVariableByName('temp_district');
            $submitted_district = $district_text;
        } else {
            $submitted_district = '';
            $district_text = '{#enter_a_district#}';
        }

        if (is_null($onclick)) {
            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->action_config = $this->getConfigParam('suggest_location_action');
            $onclick->id = 'tmp-property';
            $onclick->open_popup = 1;
            $onclick->sync_open = 1;
            $onclick->back_button = 1;
            $onclick->keep_user_data = 1;
        }

        $data = array();
        $data[] = $this->getText($district_text, array(
            'variable' => 'temp_district',
            'style' => 'mobileproperty_textbutton'
        ));

        $this->data->scroll[] = $this->getRow($data, array('style' => 'mobileproperty_input','onclick' => $onclick));
        $this->data->scroll[] = $this->getFieldtext($submitted_district, array(
            'variable' => 'temp_district',
            'opacity' => '0',
            'height' => '1',
        ));
    }

    /**
     * @param MobilepropertiesModel|null $property
     */
    public function getPropertyForm(MobilepropertiesModel $property = null, $mode = 'add')
    {
        if (is_null($property)) {
            $property = new MobilepropertiesModel();
            // If there are submitted values after validation, fill them in the object
            $property->prefillValues($this->propertyModel->submitvariables);
        }

        $this->data->scroll[] = $this->getPropertyInput($property->name, 'temp_name', '{#title,_e.g._1-bedroom_flat_in_Camden_town#}');
        
        $this->getDistrictField($property->district);

        $this->data->scroll[] = $this->getPropertyInput($property->full_address, 'temp_full_address', '{#full_address#}');

        $propertyOptions = array(
            'room' => ucfirst('{#room#}'),
            'flat' => ucfirst('{#flat#}'),
            'house' => ucfirst('{#house#}')
        );

        $data = array(
            'variable' => 'temp_property_type',
            'field_offset' => 3
        );

        if ($mode == 'edit' AND $property->property_type) {
            $data['value'] = $property->property_type;
        } else {
            $data['value'] = 'flat';
        }

        $propertyType[] = $this->formkitRadiobuttons('{#property_type#}', $propertyOptions, $data);
        $this->data->scroll[] = $this->getRow($propertyType, array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $areaSettings = $this->getVariable('area_settings');
        $areaLabel = $areaSettings == 'sq_ft' ? '{#square_ft#}' : '{#square_meter#}';
        $areaInput = $areaSettings == 'sq_ft' ? $property->square_ft : $property->square_meters;
        $row[] = $this->getPropertyInput($areaInput, 'temp_area', $areaLabel, 'mobileproperty_input_small');

        $priceSettings = $this->getVariable('price_settings');
        $priceLabel = $priceSettings == 'price_per_month' ? '{#price_per_month#}' : '{#price_per_week#}';
        $priceInput = $priceSettings == 'price_per_month' ? $property->price_per_month : $property->price_per_week;
        $row[] = $this->getPropertyInput($priceInput, 'temp_price', $priceLabel, 'mobileproperty_input_small_price_per_month');

        $this->data->scroll[] = $this->getRow($row, array('height' => '60', 'margin' => '0 0 -10 0'));

        $propertyDescription = is_null($property->description) ? '' : $property->description;
        $description[] = $this->getFieldtextarea($propertyDescription, array('style' => 'mobileproperty_textarea', 'hint' => '{#description#}', 'variable' => 'temp_description'));
        $this->data->scroll[] = $this->getRow($description, array('style' => 'mobileproperty_input'));

        $row = array();

        $row[] = $this->getPropertyInput($property->num_bedrooms, 'temp_num_bedrooms', '{#bedrooms#}', 'mobileproperty_input_small_bedrooms', 'icon-bed.png', '13 0 0 10');
        $row[] = $this->getPropertyInput($property->num_bathrooms, 'temp_num_bathrooms', '{#bathrooms#}', 'mobileproperty_input_small_bathrooms', 'icon-bath.png', '10 0 0 10');
        $this->data->scroll[] = $this->getRow($row, array('height' => '60'));

//        $options['value'] = $property->available_date;
//        $this->data->scroll[] = $this->formkitDate('{#available_date#}','temp_available_date',$options);

        $this->getCalendarField($property->available_date);

        $this->data->scroll[] = $this->getSpacer(20);

        $tenancyOptions = array(
            'shortlet' => '{#shortlet#}',
            'longlet' => '{#longlet#}'
        );

        $tenancyData = array(
            'variable' => 'temp_tenancy_option',
            'field_offset' => 4,
            'value' => array(
                'longlet' => $property->tenancy_option_longlet,
                'shortlet' => $property->tenancy_option_shortlet
            )
        );

        $tenancy[] = $this->formkitTags('{#tenancy#}', $tenancyOptions, $tenancyData);
        $this->data->scroll[] = $this->getRow($tenancy, array(
            'margin' => '-10 10 20 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $general_options = array(
            'bills_included' => '{#bills_included#}',
            'students_welcome' => '{#students_welcome#}',
            'concierge' => '{#concierge#}',
            'lift' => '{#lift#}',
            'pets_allowed' => '{#pets_allowed#}',
            'dish_washer' => '{#dish_washer#}',
            'air_conditioner' => '{#air_conditioner#}',
            'washing_machine' => '{#washing_machine#}',
            'private_parking' => '{#private_parking#}'
        );

        $g_o_data = array(
            'variable' => 'temp_feature',
            'field_offset' => 4,
        );

        if ($mode == 'edit') {
            $g_o_saved_data = array();
            foreach ($general_options as $g_o_key => $g_o_value) {
                $db_key = 'feature_' . $g_o_key;
                if (!isset($db_key)) {
                    continue;
                }

                $g_o_saved_data[$g_o_key] = $property->{$db_key};
            }

            $g_o_data['value'] = $g_o_saved_data;
        }
        $details[] = $this->formkitTags('{#more_details#}', $general_options, $g_o_data);
        $this->data->scroll[] = $this->getRow($details, array(
            'margin' => '-10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $furnishedOptions = array(
            'furnished' => '{#furnished#}',
            'unfurnished' => '{#unfurnished#}',
        );

        $furnishedData = array(
            'variable' => 'temp_furnishing',
            'field_offset' => 4,
        );

        if ( $mode == 'edit' ) {
            if ($property->feature_furnished) {
                $furnishedData['value']['furnished'] = 1;
            }

            if ($property->feature_unfurnished) {
                $furnishedData['value']['unfurnished'] = 1;
            }
        }

        $furnished[] = $this->formkitTags('{#furnished#}', $furnishedOptions, $furnishedData);
        
        $this->data->scroll[] = $this->getRow($furnished, array(
            'margin' => '10 10 0 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $this->data->scroll[] = $this->getSpacer(20);

        $balcony_options = array('garden' => '{#garden#}', 'terrace' => '{#terrace#}', 'balcony' => '{#balcony#}', 'patio' => '{#patio#}');
        $balconyValues = array(
            'garden' => $property->balcony_type_garden,
            'terrace' => $property->balcony_type_terrace,
            'balcony' => $property->balcony_type_balcony,
            'patio' => $property->balcony_type_patio,
        ) ;

        $b_o_data = array(
            'variable' => 'temp_balcony_type',
            'field_offset' => '3',
        );

        if ($mode == 'edit') {
            $b_o_data['value'] = $balconyValues;
        }

        $balcony[] = $this->formkitTags('{#balcony_type#}', $balcony_options, $b_o_data);
        $this->data->scroll[] = $this->getRow($balcony, array(
            'margin' => '-10 10 10 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3',
            'padding' => '10 10 10 0'
        ));

        $this->data->scroll[] = $this->getPropertyInput($property->offer_code, 'temp_offer_code', '{#offer_code#}', 'mobileproperty_input_offer_code');

        $tenantFee[] = $this->getFieldtext($property->tenant_fee, array('style' => 'mobileproperty_textfield', 'hint' => '{#tenant_fee#}', 'variable' => 'temp_tenant_fee',
            'input_type' => 'number'));
        $this->data->scroll[] = $this->getRow($tenantFee, array(
            'margin' => '10 10 10 10',
            'background-color' => '#FFFFFF',
            'shadow-color' => '#33000000',
            'shadow-radius' => 1,
            'shadow-offset' => '0 1',
            'border-radius' => '3'
        ));

        $current_role = $this->getVariable('role');
        $subrole = $this->getVariable('subrole');

	    if ($subrole == 'landlord') {

//		    if ( $mode == 'edit' ) {
//		    }

		    $this->data->scroll[] = $this->getRow(array(
		        $this->getFieldonoff($property->do_advertising, array('style' => 'mobileform_formfield', 'variable' => 'temp_do_advertising')),
		        $this->getText('{#advertise_on_zoopla_and_primelocation#}', array('style' => 'mobileproperty_onoff_label'))
	        ), array('style' => 'mobileproperty_onoff'));

	        $this->data->scroll[] = $this->getRow(array(
		        $this->getFieldonoff($property->full_management, array('style' => 'mobileform_formfield', 'variable' => 'temp_full_management')),
		        $this->getText('{#i_am_interested_in_full_management#}', array('style' => 'mobileproperty_onoff_label'))
	        ), array('style' => 'mobileproperty_onoff'));

            $this->data->scroll[] = $this->getRow(array(
	            $this->getFieldonoff($property->tenancy_agreement, array('style' => 'mobileform_formfield', 'variable' => 'temp_tenancy_agreement')),
	            $this->getText('{#i_need_tenancy_agreement#}', array('style' => 'mobileproperty_onoff_label')),
            ), array('style' => 'mobileproperty_onoff'));

            $this->data->scroll[] = $this->getRow(array(
	            $this->getFieldonoff($property->reference_check, array('style' => 'mobileform_formfield', 'variable' => 'temp_reference_check')),
	            $this->getText('{#arrange_reference_check_for_me#}', array('style' => 'mobileproperty_onoff_label')),
            ), array('style' => 'mobileproperty_onoff'));

        }

        if ( $mode == 'edit' ) {
	        $this->data->scroll[] = $this->getFieldtext($property->xml_imported, array(
		        'variable' => 'xml_imported',
		        'opacity' => '0',
		        'height' => '1',
	        ));
        }

    }

    /**
     * Filter properties to get only the available features
     *
     * @param $attributes
     * @return array
     */
    protected function getAvailableFeatures($attributes)
    {
        $features = [];

        foreach ($attributes as $key => $value) {
            if (!stristr($key, 'feature_')) {
                continue;
            }

            if ($value == 0) {
                continue;
            }

            $feature = str_replace('feature_', '', $key);
            $feature = $this->formatFeatureName($feature);
            $features[$key] = $feature;
        }

        return $features;
    }

    /**
     * Format the feature name
     *
     * @param $feature
     * @return string
     */
    protected function formatFeatureName($feature)
    {
        $words = explode('_', $feature);
        foreach ($words as $key => $word) {
            $words[$key] = ucfirst($word);
        }

        $feature = implode(' ', $words);
        return $feature;
    }

    /**
     * Get property images grid
     *
     * @return mixed
     */
    public function getImageGrid()
    {
        // Tthe first row contains one big picture and two small ones
        $column[] = $this->getPropertyImage('propertypic', true);
        $column[] = $this->getVerticalSpacer($this->margin);

        $row[] = $this->getPropertyImage('propertypic2');
        $row[] = $this->getSpacer($this->margin);
        $row[] = $this->getPropertyImage('propertypic3');

        $column[] = $this->getColumn($row);

        $this->data->scroll[] = $this->getRow($column, array('margin' => '0 ' . $this->margin . ' 0 ' . $this->margin));
        $this->data->scroll[] = $this->getSpacer($this->margin);

        unset($column);
        unset($row);

        // The second row contains three small pictures
        $column[] = $this->getPropertyImage('propertypic4');
        $column[] = $this->getVerticalSpacer($this->margin);
        $column[] = $this->getPropertyImage('propertypic5');
        $column[] = $this->getVerticalSpacer($this->margin);
        $column[] = $this->getPropertyImage('propertypic6');

        return $this->getRow($column, array('margin' => '0 ' . $this->margin . ' 0 ' . $this->margin));
    }

    /**
     * Set property images grid size
     */
    public function setGridWidths()
    {
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 10;
        $this->grid = $width - ($this->margin * 4);
        $this->grid = round($this->grid / 3, 0);
    }

    /**
     * Get property image for the grid
     *
     * @param $name
     * @param bool $mainimage
     * @return mixed
     */
    public function getPropertyImage($name, $mainimage = false)
    {

        $asset_name = $this->getVariable($name);

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
        $params['crop'] = (isset($crop) ? $crop : 'yes');
        $params['defaultimage'] = 'property-add-photo-grey.png';

        if (filter_var($asset_name, FILTER_VALIDATE_URL) !== false) {
            $params['use_filename'] = 1;
        }

        if ($this->deleting AND $this->getSavedVariable($name) AND strlen($this->getSavedVariable($name)) > 2) {
            $params['opacity'] = '0.6';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = $this->currentId . '-imgdel-' . $name;
        } else {
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '1200';
            $params['onclick']->variable = $this->getVariableId($name);
            $params['onclick']->action_config = $this->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'property-add-photo-grey.png';
        $params['priority'] = 9;

        return $this->getImage($asset_name, $params);
    }

    /**
     * Get property images count
     *
     * @return int
     */
    public function getImageCount()
    {
        $count = 0;

        foreach ($this->varcontent as $var => $content) {
            if ( preg_match('~propertypic~', $var) AND !empty($content) ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Save data or delete images depending on menuid
     */
    public function propertyEditSaves()
    {
//        if ($this->menuid == 'save-data') {
//            $this->saveVariables();
//            $this->loadVariableContent(true);
//        }

        if (strstr($this->menuid, '-imgdel-')) {
            $del = str_replace('-imgdel-', '', $this->menuid);
            $this->deleteVariable($del);
//            if ($del == 'propertypic') {
//                $this->bumpUpPropertyPics();
//            } else {
//                $this->deleteVariable($del);
//                $this->loadVariableContent(true);
//            }
        }
    }

    /**
     * Reposition the property pics in the grid
     */
    public function bumpUpPropertyPics()
    {
        $existing = json_decode($this->getPropertyImages());

        if (isset($existing) AND is_array($existing) AND count($existing) > 1) {
            $last = count($existing);
            $this->deleteVariable('propertypic' . $last);
            array_shift($existing);
            $count = 1;

            foreach ($existing as $val) {
                if ($val) {
                    if ($count == 1) {
                        $this->saveVariable('propertypic', $val);
                    } else {
                        $this->saveVariable('propertypic' . $count, $val);
                    }
                }
                $count++;
            }

        }
    }

    protected function getPropertyImages()
    {
        $count = 2;
        $pictures = array();

        if ($this->getSavedVariable('propertypic')) {
            $pictures['propertypic'] = $this->getSavedVariable('propertypic');
        }

        while ($count < 8) {
            $name = 'propertypic' . $count;
            if ($this->getSavedVariable($name)) {
                $pictures[$name] = $this->getSavedVariable($name);
            }
            $count++;
        }

        return json_encode($pictures);
    }

    public function setPropertyImages($images)
    {
        $images = (array)json_decode($images);

        $count = 2;
        if (isset($images['propertypic'])) {
            $this->saveVariable('propertypic', $images['propertypic']);
        }

        while ($count < 8) {
            $name = 'propertypic' . $count;

            if (isset($images[$name])) {
                $this->saveVariable($name, $images[$name]);
            }
            $count++;
        }
    }

    public function clearPropertyImages()
    {
        $count = 2;
        $this->saveVariable('propertypic', '');

        while ($count < 8) {
            $name = 'propertypic' . $count;
            $this->saveVariable($name, '');
            $count++;
        }
    }


    public function renderSingleProperty($property)
    {

        if (empty($property->name)) {
            return false;
        }

        $images = (array)json_decode($property->images);

        $image = 'property-add-photo-grey.png';

        if (isset($images['propertypic']) AND !empty($images['propertypic'])) {
            $image = $images['propertypic'];
        }

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getConfigParam('detail_view');
        $onclick->back_button = 1;
        $onclick->sync_open = 1;
//        $onclick->context = $this->getPropertyContext( $property->id );
        $onclick->id = 'property-id-' . $property->id;
        $shadow = $this->getImageFileName('shadow-image-wide.png');

        $property_data = array();

        if ($this->getVariable('role') == 'tenant') {
            $price = $property->price_per_month;
            $priceText = 'p/m';

            if ($this->settings && $this->settings->filter_price_per_week == 'price_per_week') {
                $price = $property->price_per_week;
                $priceText = 'p/w';
            }

            $area = $property->square_ft;
            $areaText = ' ft2';

            if ($this->settings && $this->settings->filter_sq_ft == 'sq_meter') {
                $area = $property->square_meters;
                $areaText = ' m2';
            }
        } else {
            $areaSettings = $this->getVariable('area_settings');
            $areaText = $areaSettings == 'sq_ft' ? 'ft2' : 'm2';
            $area = $areaSettings == 'sq_ft' ? $property->square_ft : $property->square_meters;

            $priceSettings = $this->getVariable('price_settings');
            $priceText = $priceSettings == 'price_per_month' ? 'p/m' : 'p/w';
            $price = $priceSettings == 'price_per_month' ? $property->price_per_month : $property->price_per_week;
        }

        if ( $property->square_meters ) {
            $property_data[] = $this->getImage('icon-house-green.png', array('width' => 15, 'margin' => '0 7 0 0', 'vertical-align' => 'middle'));
            $property_data[] = $this->getText($area. ' ' . $areaText, array('style' => 'property-description',));
            $property_data[] = $this->getImage('line.png', array('width' => 2, 'height' => 18, 'margin' => '0 7 0 7'));
        }

        if ( $property->num_bedrooms !== false OR $property->num_bedrooms != '' ) {
            $property_data[] = $this->getImage('icon-bed-green.png', array('width' => 15, 'margin' => '0 7 0 0', 'vertical-align' => 'middle'));
            $property_data[] = $this->getText($property->num_bedrooms . '', array('style' => 'property-description',));
            $property_data[] = $this->getImage('line.png', array('width' => 2, 'height' => 18, 'margin' => '0 7 0 7'));
        }

        if ( $property->price_per_month ) {
            $property_data[] = $this->getText('£ ' . $price . ' ' . $priceText, array('style' => 'property-description',));
        }

        if(!$property->available){
            $txt = $property->name .' ({#currently_unavailable#})';
        } else {
            $txt = $property->name;
        }

        $row = $this->getRow(array(
            $this->getColumn(array(
                $this->getRow(array(
                    $this->getText(strtoupper($txt), array('style' => 'property-title',)),
                ), array('text-align' => 'left', 'padding' => '3 10 3 10',)),
                $this->getRow(
                    $property_data,
                    array('text-align' => 'left', 'padding' => '2 0 6 10', 'vertical-align' => 'middle')),
            ), array(
                'width' => '100%',
                'height' => '230',
                'padding' => '0 0 0 0',
                'vertical-align' => 'bottom',
                'background-image' => $shadow,
                'background-size' => 'cover',
                'border-radius' => '5',
            )),
        ), array(
            'width' => '100%',
            'height' => '230',
            'background-image' => $this->getImageFileName($image),
            'background-size' => 'cover',
            'margin' => '3 3 0 3',
            'border-radius' => '5',
            'back_button' => 1,
            'onclick' => $onclick,
        ));

        return $row;
        $this->data->scroll[] = $row;
    }

    public function getEditImagesButton()
    {
        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if ($this->menuid == 'del-images-') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images-';
        }

        // This will be the delete images button with the proper image
        if ($this->getImageCount() > 1) {
            $this->data->scroll[] = $this->getImage('property-add-photo-grey.png', array(
                'height' => '30',
                'vertical-align' => 'bottom',
                'floating' => '1',
                'float' => 'right',
                'font-size' => '16',
                'onclick' => $onclick
            ));
        }
    }

    public function cleanTempVariables()
    {

        if (empty($this->varcontent)) {
            return false;
        }

        foreach ($this->varcontent as $var => $value) {
            if (preg_match('~temp_~', $var)) {
                $this->deleteVariable($var);
            }
        }

        return true;
    }

    public function prepopulateSavedValues()
    {
        $pre_saved_variables = array(
            'temp_district', 'temp_district_lat', 'temp_district_lng'
        );

        foreach ($pre_saved_variables as $var) {
            if ($data = $this->getSavedVariable($var)) {
                $this->submitvariables[$var] = $data;
            }
        }

        $this->propertyModel->submitvariables = $this->submitvariables;

        return true;
    }

    public function validatePropertyInput($images)
    {

        if (empty((array)json_decode($images))) {
            $this->errors[] = '{#please_add_at_least_one_image#}';
        }

        $rules = [
            'temp_name' => array(
                'empty' => '{#the_name_is_required#}',
            ),
            'temp_address' => array(
                'empty' => '{#the_address_is_required#}',
            ),
            'temp_district' => array(
                'empty' => '{#the_district_is_required#}',
            ),
            'temp_area' => array(
                'empty' => '{#the_area_is_required#}',
                'length' => '{#the_area_should_contain_less_than_8_numbers#}',
            ),
            'temp_price' => array(
                'empty' => '{#the_price_is_required#}',
                'length' => '{#the_price_should_contain_less_than_8_numbers#}',
            ),
            'temp_description' => array(
                'empty' => '{#the_description_is_required#}',
            ),
            'temp_num_bathrooms' => array(
                'empty' => '{#the_bathroom_is_required#}',
            ),
        ];

        foreach ($this->submitvariables as $key => $variable) {
            if (!array_key_exists($key, $rules)) {
                // There is no such validation rule, continue
                continue;
            }

	        // Disable validation for district
            if ( $key == 'temp_district' AND (isset($this->submitvariables['xml_imported']) AND $this->submitvariables['xml_imported'] == 1) ) {
            	continue;
            }

            $validation_data = $rules[$key];

            if ( isset($validation_data['empty']) AND empty($variable) ) {
                $this->errors[] = $validation_data['empty'];
            }

            if ( !empty($variable) AND isset($validation_data['length']) AND strlen($variable) > 8 ) {
                $this->errors[] = $validation_data['length'];
            }

        }

        if (!is_numeric((int)$this->submitvariables['temp_num_bedrooms'])) {
            $this->errors[] = '{#the_bedrooms_count_must_be_a_number#}';
        }

        if ((int)$this->submitvariables['temp_num_bedrooms'] > 10) {
            $this->errors[] = '{#please_indicate_number_of_bedrooms_up_to_10#}';
        }

        if (!ctype_digit($this->submitvariables['temp_num_bathrooms'])) {
            $this->errors[] = '{#the_bathrooms_count_must_be_a_number#}';
        }

        if ((int)$this->submitvariables['temp_num_bathrooms'] > 10) {
            $this->errors[] = '{#please_indicate_number_of_bedrooms_up_to_10#}';
        }
    }

    public function migrateUserRoles() {

        if ( $this->getSavedVariable( 'user_migrated' ) ) {
            return false;
        }

        $plays = Aeplay::getAllPlayIdsForGame( 1, $this->gid );

        if ( empty($plays) ) {
            return false;
        }

        foreach ($plays as $play) {
            $play_id = $play['id'];

            $vars = AeplayVariable::getArrayOfPlayvariables( $play_id );

            if ( isset($vars['filter_role']) ) {
                continue;
            }

            $role = '';

            if ( isset($vars['subrole']) AND $vars['subrole'] ) {
                $role = $vars['subrole'];
            } else if ( isset($vars['role']) AND $vars['role'] ) {
                $role = $vars['role'];
            }

            if ( $role ) {
                $this->saveRemoteVariable( 'filter_role', $role, $play_id );
            }

        }

        $this->saveVariable( 'user_migrated', 1 );

        return true;
    }

    public function getCalendarField( $date ) {

        $this->registerCalendarDiv( $date );

        if ($date) {
            $calendar_text = $date;
            $submitted_district = $calendar_text;
        } elseif ($this->getSavedVariable('temp_selected_date')) {
            $calendar_text = $this->getSavedVariable('temp_selected_date');
            $submitted_district = $calendar_text;
        } elseif ($this->getSubmittedVariableByName( 'temp_selected_date' )) {
            $calendar_text = $this->getSubmittedVariableByName('temp_selected_date');
            $submitted_district = $calendar_text;
        } else {
            $submitted_district = '';
            $calendar_text = '{#available_date#}';
        }

        $onclick = new stdClass();
        $onclick->action = 'show-div';
        $onclick->div_id = 'show_calendar';
        $onclick->tap_to_close = 1;
        $onclick->transition = 'fade';
        $onclick->layout = new stdClass();
        $onclick->layout->top = 100;
        $onclick->layout->right = 10;
        $onclick->layout->left = 10;
        $onclick->background = 'blur';

        $this->data->scroll[] = $this->getRow(array(
            $this->getText($calendar_text, array(
                'variable' => 'temp_selected_date',
                'style' => 'mobileproperty_textbutton',
            )),
        ), array(
            'style' => 'mobileproperty_input',
            'onclick' => $onclick
        ));
    }

    protected function registerCalendarDiv( $date = false ) {

        if ( empty($date) ) {
            $date = time();
        } else {
            $date = strtotime( $date );
        }

        $closeDiv = new stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->keep_user_data = 1;
        // $closeDiv->div_id = 'show_calendar';

        $background = 'background-color';

        $calendar_styles = new StdClass();
        $calendar_styles->{$background} = '#68c763';

        $this->data->divs['show_calendar'] = $this->getColumn(array(
            $this->getCalendar($date, array(
                'padding' => '20 20 20 20',
                'variable' => 'temp_selected_date',
                'selection_style' => $calendar_styles
            )),
            $this->getRow(array(
                $this->getText(strtoupper('{#close#}'), array(
                    'style' => 'submit-button-gray',
                    'onclick' => $closeDiv,
                ))
            ), array(
                'width' => '100%'
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'border-width' => '1',
            'border-color' => '#495166',
        ));
    }

    public function getPropertyContext( $property_id ) {

        $ps = $this->getSavedVariable( 'price_settings' );
        $as = $this->getSavedVariable( 'area_settings' );

        return 'property-id-' . $ps . ':' . $as . '-' . $property_id;
    }

}