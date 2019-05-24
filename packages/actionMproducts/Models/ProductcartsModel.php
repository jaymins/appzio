<?php


namespace packages\actionMproducts\Models;
use CActiveRecord;

class ProductcartsModel extends CActiveRecord {

    public $play_id;
    public $product_id;
    public $date_added;
    public $quantity;
    public $id;
    public $cart_status;

    public $category_name;

    public function tableName()
    {
        return 'ae_ext_products_carts';
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
            'play' => array(self::BELONGS_TO, '\Aeplay', 'play_id'),
            'task' => array(self::BELONGS_TO, 'packages\actionMtasks\Models\TasksModel', 'task_id')
        );
    }



}
