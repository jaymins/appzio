<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProductpurchasesModel extends CActiveRecord {

    public $id;
    public $play_id;
    public $prouduct_id;
    public $date;
    public $price;
    public $status;

    public function tableName()
    {
        return 'ae_ext_products_purchases';
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
