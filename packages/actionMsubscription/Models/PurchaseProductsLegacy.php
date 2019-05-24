<?php


/**/
class PurchaseProductsLegacy extends CActiveRecord
{
    public $id;
    public $app_id;
    public $name;
    public $code;
    public $type;
    public $price;
    public $currency;
    public $code_ios;
    public $code_android;
    public $description;
    public $image;
    public $icon;

    public function tableName()
    {
        return 'ae_ext_purchase_product';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'purchases' => array(self::HAS_MANY, 'packages\actionMsubscription\Models\PurchaseModel', ['id' => 'question_id'])
        );
    }
}