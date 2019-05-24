<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProductitemsModel extends CActiveRecord {

    public $app_id;
    public $category_id;
    public $play_id;
    public $amazon_product_id;

    // string
    public $photo;

    // json array
    public $additional_photos;
    public $title;
    public $header;
    public $description;
    public $link;
    public $rating;
    public $price;
    public $points_value;
    public $featured;

    public function tableName()
    {
        return 'ae_ext_products';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'categories' => array(self::BELONGS_TO, 'packages\actionMproducts\Models\ProductcategoriesModel','category_id'),
            'photos' => array(self::HAS_MANY, 'packages\actionMproducts\Models\ProductphotosModel','product_id'),
        );
    }



}
