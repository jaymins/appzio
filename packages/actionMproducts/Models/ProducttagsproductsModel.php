<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProducttagsproductsModel extends CActiveRecord {


    public $tag_id;
    public $product_id;

    public function tableName()
    {
        return 'ae_ext_products_tags_products';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'products' => array(self::BELONGS_TO, 'packages\actionMproducts\Models\ProductitemsModel','product_id'),
        );
    }



}
