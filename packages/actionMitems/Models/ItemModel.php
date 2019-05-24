<?php

namespace packages\actionMitems\Models;

use CActiveRecord;

class ItemModel extends CActiveRecord
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
    public $zip;
    public $buyer_play_id;
    public $source;
    public $importa_date;
    public $external_id;
    public $created_at;
    public $slug;
    public $extra_data;
    public $comments;

    public $next_date;

    public $distance;
    public $distance_lat;
    public $distance_lon;
    
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
        return array(
            'likes' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemLikeModel', 'item_id'),
            'tags' => array(self::MANY_MANY, 'packages\actionMitems\Models\ItemTagModel', 'ae_ext_items_tag_item(item_id, tag_id)'),
            'category' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemCategoryModel', 'category_id'),
            'categories' => array(self::MANY_MANY, 'packages\actionMitems\Models\ItemCategoryModel', 'ae_ext_items_category_item(item_id, category_id)'),
            'reminders' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemRemindersModel', 'item_id'),
            'images' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemImagesModel', 'item_id'),
            'dbimages' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemImagesModel', 'item_id'),
            'images_data' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemImagesModel', 'item_id'), // To do: get rid of the images column in the table
            'category_relations' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemCategoryRelationModel', 'item_id'),
            'places' => array(self::BELONGS_TO, 'packages\actionMvenues\Models\VenuesModel', 'place_id'),
        );
    }

    /**
     * Save a new item in storage.
     *
     * @param $variables
     * @return ItemModel
     */
    public static function store($variables)
    {
        // Unset this property to avoid rewriting the category_id field
        unset($variables['category']);

        if (!empty($variables['id'])) {
            $item = self::model()->findByPk($variables['id']);
        } else {
            $item = new ItemModel();
        }

        foreach ($variables as $key => $value) {
            if (!property_exists(ItemModel::class, $key)) {
                continue;
            }

            if ($key === 'images') {
                $item->{$key} = json_encode($value);
                continue;
            }

            $item->{$key} = $value;
        }

        if (!empty($variables['id'])) {
            unset($item->slug);
            $item->update();
        } else {
            @$item->insert();
        }

        return $item;
    }

    public function getImages() {
        $images = json_decode($this->images);
        $imagesArray = json_decode($this->images, 1);

        if (empty($images->itempic)) {
            $images->itempic = current($imagesArray);
        }

        return $images;
    }

}