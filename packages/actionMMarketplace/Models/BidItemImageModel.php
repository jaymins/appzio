<?php

namespace packages\actionMMarketplace\Models;

use CActiveRecord;

class BidItemImageModel extends CActiveRecord
{
    public $id;
    public $bid_item_id;
    public $image;
    public $weight;



    public function tableName()
    {
        return 'ae_ext_user_bid_item_images';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'bidItem' => [self::BELONGS_TO, BidItemModel::className(), 'id']
        ];
    }

    public static function className()
    {
        return __CLASS__;
    }
}