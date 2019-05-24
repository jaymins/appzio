<?php


namespace packages\actionMsubscription\Models;

use CActiveRecord;


/**/
class PurchaseModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $play_id;
    public $product_id;
    public $price;
    public $currency;
    public $type;
    public $date;
    public $store_id;
    public $receipt;
    public $subject;
    public $subscription;
    public $yearly;
    public $monthly;
    public $expiry;
    public $email;

    public function tableName()
    {
        return 'ae_ext_purchase';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'product' => array(self::BELONGS_TO, 'packages\actionMsubscription\Models\PurchaseProductsModel', 'product_id', 'joinType'=>'LEFT JOIN')
        );
    }
}