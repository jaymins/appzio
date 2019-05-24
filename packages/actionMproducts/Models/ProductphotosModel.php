<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProductphotosModel extends CActiveRecord {

    public $id;
    public $product_id;
    public $photo;

    // string

    public function tableName()
    {
        return 'ae_ext_products_photos';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'categories' => array(self::BELONGS_TO, 'packages\actionMproducts\Models\ProductitemsModel','product_id'),
        );
    }



}
