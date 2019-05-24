<?php

class MobilepropertiesSettingModel extends ArticleModel
{
    public $id;
    public $play_id;
    public $game_id;
    public $from_num_bedrooms;
    public $to_num_bedrooms;
    public $from_price_per_month;
    public $to_price_per_month;
    public $property_type;
    public $furnished;
    public $type_house;
    public $type_flat;
    public $type_room;
    public $filter_sq_ft;
    public $filter_price_per_week;
    public $options_pets_allowed;
    public $options_outside_spaces;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileproperty_settings';
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'Aeplay', 'play_id')
        );
    }

    /**
     * Store settings for user
     *
     * @param array $variables
     * @param int $playId
     */
    public static function saveSubmit(array $variables, int $playId, string $districts = '', $gameId)
    {
        $settings = self::model()->find('play_id=:playId AND game_id=:gameId', array(':playId' => $playId, ':gameId' => $gameId));

        if (empty($settings)) {
            $settings = new MobilepropertiesSettingModel();
            $settings->play_id = $playId;
            $settings->game_id = $gameId;
        }

        if (($variables['options_furnished'] && $variables['options_unfurnished']) ||
            ( ! $variables['options_furnished'] && ! $variables['options_unfurnished'])) {
            // We don't filter by furnished, they cancel each other
            $settings->furnished = NULL;
        } else if ($variables['options_furnished']) {
            $settings->furnished = 1;
        } else {
            $settings->furnished = 0;
        }

        $settings->from_num_bedrooms = $variables['from_num_bedrooms'];
        $settings->to_num_bedrooms = $variables['to_num_bedrooms'];
        $settings->from_price_per_month = $variables['from_price_per_month'];
        $settings->to_price_per_month = $variables['to_price_per_month'];
        $settings->options_outside_spaces = $variables['options_outside_spaces'];
        $settings->options_pets_allowed = $variables['options_pets_allowed'];
        $settings->districts = $districts;

        $settings->type_house = $variables['property_type_house'];
        $settings->type_room = $variables['property_type_room'];
        $settings->type_flat = $variables['property_type_flat'];

        if (isset($variables['filter_sq_ft'])) {
            $settings->filter_sq_ft = $variables['filter_sq_ft'];
        }

        $settings->filter_price_per_week = $variables['filter_price_per_week'];

        if (isset($variables['filter_price_per_week']) && $variables['filter_price_per_week'] == 'price_per_week') {
            // User is saving in price per week.
            // We need to store those as price per month in order for filtering to work propertly.
            $settings->from_price_per_month = ($variables['from_price_per_month'] * 52) / 12;
            $settings->to_price_per_month = ($variables['to_price_per_month'] * 52) / 12;
        }

        try {
            $settings->save();
        } catch (\Exception $e) {
            // handle exception
        }
    }

    /**
     * Get user's settings or an empty model
     *
     * @param int $playId
     * @return array|CActiveRecord|mixed|MobilepropertiesSettingModel|null
     */
    public static function getSettings($playId, $gameId)
    {
        $settings = self::model()->find('play_id=:playId AND game_id=:gameId', array(':playId' => $playId, ':gameId' => $gameId));

        if (empty($settings)) {
            $settings = new MobilepropertiesSettingModel();
            $settings->from_num_bedrooms = 0;
            $settings->to_num_bedrooms = 5;
            $settings->from_price_per_month = 0;
            $settings->to_price_per_month = 12000;
            $settings->property_type = '';
        }

        return $settings;
    }

    /**
     * Match agent settings with users
     *
     * @param int $playId
     * @param int $gameId
     * @return array|CActiveRecord[]|mixed|null
     */
    public static function matchSettings(int $playId, $gameId)
    {
        $settings = self::model()->find('play_id=:id', array(':id' => $playId));

        if (empty($settings)) {
            // Settings are not set, return tenants which match the existing properties
            $settings = MobilepropertiesModel::getPropertiesBoundaries($playId, $gameId);
            $matches = self::matchInterestedTenants($settings, $playId, $gameId);
            return $matches;
        }

        $criteria = new CDbCriteria();
        $criteria->condition = 'Settings.play_id!=:id AND Settings.game_id=:gameId AND from_num_bedrooms >= :fromBedrooms AND to_num_bedrooms <= :toBedrooms AND from_price_per_month >= :fromPrice AND to_price_per_month <= :toPrice AND (property_type = :propertyType OR property_type IS NULL) AND districts != :districts';
        $criteria->params = array(
            ':id' => $playId,
            ':gameId' => $gameId,
            ':fromBedrooms' => $settings['from_num_bedrooms'],
            ':toBedrooms' => $settings['to_num_bedrooms'],
            ':fromPrice' => $settings['from_price_per_month'],
            ':toPrice' => $settings['to_price_per_month'],
            ':propertyType' => $settings['property_type'],
            ':districts' => ''
        );
        $criteria->alias = 'Settings';

        $matches = self::model()->findAll($criteria);

        return $matches;
    }

    public static function matchInterestedTenants($settings, $playId, $gameId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'play_id!=:id AND game_id=:gameId AND to_num_bedrooms >= :toBedrooms AND to_price_per_month >= :toPrice';
        $criteria->params = array(
            ':id' => $playId,
            ':gameId' => $gameId,
            ':toBedrooms' => $settings['to_num_bedrooms'],
            ':toPrice' => $settings['to_price_per_month']
        );

        $matches = self::model()->findAll($criteria);

        $matches = array_filter($matches, function($match) use ($settings) {

            $propertyTypes = self::getPreferedTypes($match);

            if (!empty($propertyTypes)) {
                $typesMatch = false;

                foreach ($propertyTypes as $propertyType) {
                    if (in_array($propertyType, $settings['property_type'])) {
                        $typesMatch = true;
                    }
                }

                if (!$typesMatch) {
                    return false;
                }
            }

            $districts = json_decode($match->districts,true);

            if (!empty($districts) AND is_array($districts)) {
                foreach ($districts as $district) {
                    if (in_array($district, $settings['districts'])) {
                        return true;
                    }
                    foreach ($settings['districts'] as $settingsDistrict) {
                        if (strpos($settingsDistrict, $district) !== false) {
                            return true;
                        }
                    }
                }
            }

            return false;
        });

        return $matches;
    }

    protected static function getPreferedTypes($match) {
        $types = array('house', 'room', 'flat');
        $matchTypes = array();

        foreach ($types as $type) {
            $property = 'type_' . $type;
            if ($match->{$property}) {
                $matchTypes[] = $type;
            }
        }

        return $matchTypes;
    }

    public static function findOrFail($userId, $gameId)
    {
        $settings = self::model()->find('play_id=:playId AND game_id=:gameId', array(':playId' => $userId, ':gameId' => $gameId));

        if (!$settings) {
            return false;
        }

        return $settings;
    }

    public function getPropertyCondition()
    {
        $types = array();

        if ($this->type_house) {
            $types[] = 'house';
        }

        if ($this->type_flat) {
            $types[] = 'flat';
        }

        if ($this->type_room) {
            $types[] = 'room';
        }

        return $types;
    }

}