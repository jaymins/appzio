<?php

class ItemCronModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $game_id;
    public $category_id;
    public $place_id;

    /* varchar, not used in all cases */
    public $type;
    public $images;
    public $name;
    public $description;
    public $price;
    public $time;
    public $owner;
    public $date_added;
    public $status;
    public $lat;
    public $lon;

    public $pretty_tags;
    public $featured;
    public $external;
    public $city;
    public $country;
    public $buyer_play_id;
    public $source;
    public $importa_date;
    public $external_id;
    public $created_at;
    public $slug;
    public $extra_data;

    public function tableName()
    {
        return 'ae_ext_items';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array();
    }

}