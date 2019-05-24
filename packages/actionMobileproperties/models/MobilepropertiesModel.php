<?php

class MobilepropertiesModel extends ArticleModel
{

    public $id;
    public $game_id;
    public $name;
    public $address;
    public $district;
    public $full_address;
    public $district_lat;
    public $district_lng;
    public $square_ft;
    public $price_per_month;
    public $offer_code;
    public $description;
    public $num_bedrooms;
    public $num_bathrooms;
    public $full_management;
    public $reference_check;
    public $tenancy_agreement;
    public $active;
    public $balcony_type;
    public $tenant_fee;
    public $images;
    public $available_date;
    public $title;
    public $play_id;
    public $feature_pets_allowed;
    public $feature_lift;
    public $feature_concierge;
    public $feature_private_parking;
    public $feature_students_welcome;
    public $feature_bills_included;
    public $feature_dish_washer;
    public $feature_air_conditioner;
    public $feature_washing_machine;
    public $feature_furnished;
    public $feature_unfurnished;
    public $furnished;
    public $available;
    public $square_meters;
    public $price_per_week;
    public $property_type;
    public $balcony_type_garden;
    public $balcony_type_terrace;
    public $balcony_type_balcony;
    public $balcony_type_patio;
    public $tenancy_option_shortlet;
    public $tenancy_option_longlet;
    public $xml_imported;
    public $is_premium;
    public $do_advertising;

    public $submitvariables;

    const SQUARE_METER_COEFFICIENT = 0.092903;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileproperty';
    }

    public function relations()
    {
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
            'bookmarks' => array(self::HAS_MANY, 'MobilepropertiesBookmarkModel', 'mobileproperty_id'),
        );
    }

    /**
     * Store or update property
     *
     * @param string $mode
     * @param bool $id
     * @param string $images
     * @return mixed
     */
    public function saveSubmit($mode = 'insert', $id = false, $images)
    {
        if ( $mode == 'update' ) {
            $model = MobilepropertiesModel::model()->findByPk($id);
        } else if ( $mode == 'auto' AND !empty( $id ) ) {

            // Search for a property before trying to create a new one
            $model = MobilepropertiesModel::model()->findByAttributes(array(
                'offer_code' => $id
            ));

            if ( empty($model) ) {
                $model = new MobilepropertiesModel();
                $mode = 'insert';
            } else {
                $mode = 'update';
            }

        } else { // mode 'insert'
            $model = new MobilepropertiesModel();
        }

        $temp_district = $this->factory->getSubmittedVariableByName( 'temp_district' );

        if($temp_district){
            $model->district = $temp_district;
        }

        /* we simply look at what's submitted */
        foreach ($this->submitvariables as $key => $value) {
            $key = str_replace('temp_', '', $key);

            if (property_exists('MobilepropertiesModel', $key)) {
                $model->$key = $value;
            }
        }

        if ( isset($this->submitvariables['temp_selected_date']) ) {
            $date = $this->submitvariables['temp_selected_date'];
            $model->available_date = date('Y-m-d', $date);
        }

        $area = $this->factory->getVariable('area_settings');
        $price = $this->factory->getVariable('price_settings');

        if ( isset($this->submitvariables['temp_area']) ) {
            if ($area == 'sq_meter' || is_null($area)) {
                $model->square_meters = $this->submitvariables['temp_area'];
                $model->square_ft = $this->submitvariables['temp_area'] / self::SQUARE_METER_COEFFICIENT;
            } else {
                $model->square_ft = $this->submitvariables['temp_area'];
                $model->square_meters = $this->submitvariables['temp_area'] * self::SQUARE_METER_COEFFICIENT;
            }
        }

        if ( isset($this->submitvariables['temp_price']) ) {
            if ($price == 'price_per_week' || is_null($price)) {
                $model->price_per_week = $this->submitvariables['temp_price'];
                $model->price_per_month = ($this->submitvariables['temp_price'] * 52) / 12;
            } else {
                $model->price_per_month = $this->submitvariables['temp_price'];
                $model->price_per_week = (int)(($this->submitvariables['temp_price'] * 12) / 52);
            }
        }

        /* we are overriding these here */
        $model->game_id = $this->game_id;
        $model->play_id = $this->play_id;
        $model->images = json_encode($this->rearangePropertyPics((array)json_decode($images)));

        if ( $this->factory->getSubmittedVariableByName('temp_furnishing_furnished') ) {
            $model->furnished = 1;
            $model->feature_furnished = 1;
        }

        if ( isset($this->submitvariables['xml_property']) ) {
            $model->feature_furnished = $this->submitvariables['feature_furnished'];
            $model->feature_unfurnished = $this->submitvariables['feature_unfurnished'];
            $model->furnished = $this->submitvariables['furnished'];
            $model->xml_imported = 1;
        } else {
            $model->feature_furnished = $this->factory->getSubmittedVariableByName('temp_furnishing_furnished');
            $model->feature_unfurnished = $this->factory->getSubmittedVariableByName('temp_furnishing_unfurnished');
            $model->furnished = ( $model->feature_furnished ? 1 : 0 );
        }

        if (!$model->tenant_fee) {
            $model->tenant_fee = 0;
        }

        try {
            $model->$mode();
            return $model->id;
        } catch (\Exception $e) {

        }

        return false;
    }

    /**
     * @param array $variables
     * @return array|CActiveRecord[]|mixed|null
     */
    public function filterProperties($variables)
    {
        $condition = $this->generateFilterCondition($variables);

        $properties = self::model()->findAll(array(
            'select' => '*',
            'condition' => $condition,
            'params' => array(
                'play_id' => $this->play_id,
                'game_id' => $this->game_id,
            )
        ));

        return $properties;
    }

    /**
     * Generate filtering condition from the submitted variables
     *
     * @param $variables
     * @return string
     */
    protected function generateFilterCondition($variables)
    {
        $condition = 'play_id = :play_id AND game_id = :game_id';

        $variables = array_filter($variables);
        end($variables);
        $last = key($variables);

        if (isset($variables['furnished']) && isset($variables['unfurnished'])) {
            // Don't add anything since they neutralize each other
        } else if (isset($variables['furnished'])) {
            $condition .= " AND furnished=1";
        } else if (isset($variables['unfurnished'])) {
            $condition .= " AND furnished=0";
        }

        unset($variables['furnished']);
        unset($variables['unfurnished']);

        if (count($variables) > 0) {
            $condition .= ' AND ';
        }

        // Loop through the variables and add them to a query string
        foreach ($variables as $key => $value) {

            if (stristr($key, 'from_')) {
                $attribute = str_replace('from_', '', $key);
                $condition .= $attribute . '>=' . $value;
            } else if (stristr($key, 'to_')) {
                $attribute = str_replace('to_', '', $key);
                $condition .= $attribute . '<=' . $value;
            }

            if ($key != $last) {
                // If not the last property of the array add an AND statement
                $condition .= ' AND ';
            }
        }

        return $condition;
    }

    /**
     * Delete property image
     *
     * @param $id
     * @param $variable
     */
    public function deleteImage($id, $variable)
    {
        $property = self::model()->findByPk($id);

        $images = (array)json_decode($property->images);
        unset($images[$variable]);

        if ($variable == 'propertypic') {
            $images = $this->rearangePropertyPics($images);
        }

        $property->images = json_encode($images);
        $property->update();
    }

    /**
     * Rearange profile pics to fill missing after image deletion
     *
     * @param $images
     * @return mixed
     */
    protected function rearangePropertyPics($images)
    {
        $count = 1;
        $arrangedImages = array();

        foreach ($images as $key => $image) {

            if ($count == 1) {
                $arrangedImages['propertypic'] = $image;
                $count++;
                continue;
            }

            $arrangedImages['propertypic' . $count] = $image;
            $count++;
        }

        return $arrangedImages;
    }

    /**
     * Toggle available status
     *
     * @param $id
     */
    public function toggleAvailable($id)
    {
        $model = self::model()->findByPk($id);
        $model->available = !$model->available;

        try {
            $model->update();
        } catch (\Exception $e) {
            // Handle exception
        }
    }

    public static function findUnmatchedProperties($userId, $settings, $gameId, $from = 'agent', $user_search_term)
    {
        $query = "
          SELECT mobileproperty_id
          FROM ae_ext_mobileproperty_bookmark
          WHERE play_id = $userId AND game_id = $gameId";

        $result = Yii::app()->db->createCommand($query)->queryAll();

        $propertyIds = [];

        foreach ($result as $item) {
            $propertyIds[] = $item['mobileproperty_id'];
        }

        // Get properties that are not bookmarked AND are available
        $criteria = new CDbCriteria;

        // Get only properties added by people of the required role
        $criteria->alias = 'Properties';
        $criteria->join = "
            LEFT JOIN ae_game_play_variable ON ae_game_play_variable.play_id = Properties.play_id
            LEFT JOIN ae_game_variable ON ae_game_variable.id = ae_game_play_variable.variable_id";
        $criteria->condition = "ae_game_variable.name = :variable AND ae_game_play_variable.value = :role";
        $criteria->params[':variable'] = 'subrole';

        if ($from == 'agent') {
            $criteria->params[':role'] = '';
        } else if ($from == 'landlord') {
            $criteria->params[':role'] = $from;
        }

        $criteria->condition .= ' AND Properties.game_id = :gameId AND available = :available';
        $criteria->params[':gameId'] = $gameId;
        $criteria->params[':available'] = 1;
        
        if ($settings) {

            $priceColumn = 'price_per_month';

//            if ($settings->filter_price_per_week == 'price_per_week') {
//                $priceColumn = 'price_per_week';
//            }

            $criteria->condition .= ' AND ( ' . $priceColumn . ' >= :fromPrice AND ' . $priceColumn . ' <= :toPrice ) AND ( num_bedrooms >= :fromBedrooms AND num_bedrooms <= :toBedrooms )';
            // $criteria->condition .= ' AND ' . $priceColumn . ' >= :fromPrice AND ' . $priceColumn . ' <= :toPrice AND num_bedrooms >= :fromBedrooms AND num_bedrooms <= :toBedrooms';

            $criteria->params[':fromPrice'] = "{$settings['from_price_per_month']}";
            $criteria->params[':toPrice'] = "{$settings['to_price_per_month']}";
            $criteria->params[':fromBedrooms'] = "{$settings['from_num_bedrooms']}";
            $criteria->params[':toBedrooms'] = "{$settings['to_num_bedrooms']}";

            if ($settings['furnished'] != '') {
                $criteria->condition .= ' AND furnished = :furnished';
                $criteria->params[':furnished'] = $settings['furnished'];
            }

            if ($settings['options_pets_allowed']) {
                $criteria->condition .= ' AND feature_pets_allowed = :petsAllowed';
                $criteria->params[':petsAllowed'] = true;
            }

            if ($settings['options_outside_spaces']) {
                $criteria->condition .= ' AND (balcony_type_terrace = 1 OR balcony_type_patio = 1 OR balcony_type_balcony = 1)';
            }

            $propertyCondition = $settings->getPropertyCondition();
            if (!empty($propertyCondition)) {
                $criteria->addInCondition('property_type', $propertyCondition);
            }

//            if($settings['districts'] != '') {
//                $criteria->condition .= " AND JSON_CONTAINS ('" .$settings['districts'] ."', CAST(`districts` as json))";
//            }

        } else {
            // If settings are empty set bedroom search to the default value
            $criteria->condition .= ' AND ( num_bedrooms <= :toNumBedrooms AND price_per_month <= :toPrice )';
            $criteria->params[':toNumBedrooms'] = 5;
            $criteria->params[':toPrice'] = 12000;
        }

        // This must be called after the conditional rules to avoid overriding
        $criteria->addNotInCondition('Properties.id', $propertyIds);

        $criteria->group = 'Properties.id';

        $properties = MobilepropertiesModel::model()->findAll($criteria);

        $districts = json_decode($settings['districts'], true);

        if ( $user_search_term ) {
            $districts[] = $user_search_term;
        }

        if ($districts) {
            $properties = self::getFilteredProperties( $properties, $districts );
        }

        return $properties;
    }

    public static function getFilteredProperties( $properties, $search_values ) {

    	if ( empty($properties) ) {
    		return $properties;
	    }

        $filtered = array();
        $property_track = array();

        $search_fields = array(
            'address', 'district', 'full_address'
        );

        foreach ($properties as $property) {

            $property_id = $property->id;

            foreach ($search_values as $search_value) {
                $criteria = strtolower($search_value);

                foreach ($search_fields as $search_field) {

	                $property_search_field = strtolower($property->{$search_field});

	                if (
		                preg_match("~$criteria~", $property_search_field) AND
		                !in_array($property_id, $property_track)
	                ) {
		                $filtered[] = $property;
		                $property_track[] = $property_id;
		                break;
	                }

                	/*
	                $property_search_field = str_replace(',', '', $property->{$search_field});
                    $search_field_pieces = explode( ' ', $property_search_field );

                    $criteria_pieces = explode( ' ', $criteria );

                    foreach ($search_field_pieces as $sfp) {

	                    foreach ( $criteria_pieces as $criteria_piece ) {
		                    if (
			                    preg_match("~^$criteria_piece~", strtolower($sfp)) AND
			                    !in_array($property_id, $property_track)
		                    ) {
			                    $filtered[] = $property;
			                    $property_track[] = $property_id;
			                    break;
		                    }
	                    }

                    }
                	*/

                }

            }

        }

        return $filtered;
    }

    public static function getPropertiesBoundaries($userId, $gameId)
    {
        $properties = MobilepropertiesModel::model()->findAll(array(
            'condition' => 'play_id = :userId AND game_id = :gameId',
            'params' => array(':userId' => $userId, ':gameId' => $gameId)
        ));

        $boundaries['to_price_per_month'] = 15000;
        $boundaries['to_num_bedrooms'] = 15;
        $boundaries['property_type'] = [];
        $boundaries['districts'] = [];

        foreach ($properties as $property) {
            if ($property->price_per_month < $boundaries['to_price_per_month']) {
                $boundaries['to_price_per_month'] = $property->price_per_month;
            }
            if ($property->num_bedrooms < $boundaries['to_num_bedrooms']) {
                $boundaries['to_num_bedrooms'] = $property->num_bedrooms;
            }
            if (!in_array($property->property_type, $boundaries['property_type'])) {
                $boundaries['property_type'][] = $property->property_type;
            }
            if (!in_array($property->district, $boundaries['districts'])) {
                $boundaries['districts'][] = $property->district;
            }
        }

        return $boundaries;
    }

    public function prefillValues($values)
    {
        foreach ($values as $key => $value) {
            $attribute = str_replace('temp_', '', $key);
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function addToRecentDistricts($postcode,$region)
    {
        $recent = $this->factory->getSavedVariable('recent_districts');

        if($recent){
            $recent = json_decode($recent,true);
            if(is_array($recent)){
                if(count($recent) > 10){
                    array_shift($recent);
                }
            } else {
                $recent = array();
            }
        } else {
            $recent = array();
        }

        $arr = array('postcode' => $postcode,'region' => $region);
        if(!in_array($arr,$recent)){
            array_push($recent,$arr);
            $recent = json_encode($recent);
            $this->factory->saveVariable('recent_districts',$recent);
        }

    }

    public function getRecentDistricts(){
        $recent = $this->factory->getSavedVariable('recent_districts');
        $recent = json_decode($recent,true);

        if(!empty($recent)){
            return $recent;
        }

        return false;

    }

    public function saveFullPropertyAddress() {

        $var_name = 'temp_district';

        if ( !isset($this->factory->submitvariables[$var_name]) OR empty($this->factory->submitvariables[$var_name]) ) {
            return false;
        }

        $temp_address = $this->factory->submitvariables[$var_name];

        $apikey = Aemobile::getConfigParam($this->factory->gid, 'google_maps_api');

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

        if ( $address ) {
            $this->addToRecentDistricts($temp_address, $address);
            // $this->saveVariable('temp_district', $address);
        }

        if ( $coords ) {
            $this->factory->saveVariable('temp_district_lat', $coords['lat']);
            $this->factory->saveVariable('temp_district_lng', $coords['lng']);
        }

        return true;
    }

}