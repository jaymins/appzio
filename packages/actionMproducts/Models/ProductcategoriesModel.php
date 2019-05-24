<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProductcategoriesModel extends CActiveRecord {

    public $id;
    public $app_id;
    public $title;
    public $sorting;

    public function tableName()
    {
        return 'ae_ext_products_categories';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'product' => array(self::BELONGS_TO, 'packages\actionMproducts\Models\ProductitemsModel', 'product_id'),
        );
    }



}
