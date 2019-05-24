<?php

namespace packages\actionMMarketplace\Models;

use CActiveRecord;

class BidItemModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $title;
    public $description;
    public $styles;
    public $valid_date;
    public $status;
    public $lat;
    public $lon;

    const ACTIVE_STATUS = 'active';
    const COMPLETE_STATUS = 'completed';
    const CANCLLED_STATUS = 'cancelled';

    public function tableName()
    {
        return 'ae_ext_bid_items';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'images' => [self::HAS_MANY, BidItemImageModel::className(), 'bid_item_id'],
            'bids' => [self::HAS_MANY, UserBidModel::className(), 'bid_item_id']
        ];
    }

    public function getUser()
    {
        return \AeplayVariable::getArrayOfPlayvariables($this->play_id);
    }

    public static function className()
    {
        return __CLASS__;
    }

}