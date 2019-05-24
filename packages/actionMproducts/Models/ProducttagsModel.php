<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProducttagsModel extends CActiveRecord {

    public $id;
    public $title;
    public $app_id;


    public function tableName()
    {
        return 'ae_ext_products_tags';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            //'products' => array(self::BELONGS_TO, 'ProductitemsModel','product_id'),
        );
    }



}
