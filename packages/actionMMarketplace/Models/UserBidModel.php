<?php

namespace packages\actionMMarketplace\Models;

use CActiveRecord;

class UserBidModel extends CActiveRecord
{
    public $id;
    public $bid_item_id;
    public $play_id;
    public $price;
    public $message;
    public $created_date;
    public $status;

    const AVAILABLE_STATUS = 'available';
    const PENDING_STATUS = 'pending';
    const ACCEPTED_STATUS = 'selected';
    const DECLINED_STATUS = 'declined';

    public function tableName()
    {
        return 'ae_ext_user_bids';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'bidItem' => [self::BELONGS_TO, BidItemModel::className(), 'bid_item_id']
        ];
    }


    public static function className()
    {
        return __CLASS__;
    }

}