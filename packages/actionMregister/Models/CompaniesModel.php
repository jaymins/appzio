<?php


namespace packages\actionMregister\Models;

use CActiveRecord;


/**/
class CompaniesModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $name;
    public $subscription_active;
    public $subscription_expires;
    public $user_limit;
    public $notes;
    public $admit_by_domain;
    public $domain;

    public function tableName()
    {
        return 'ae_ext_mregister_companies';
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