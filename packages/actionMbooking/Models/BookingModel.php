<?php

namespace packages\actionMbooking\Models;

use CActiveRecord;

class BookingModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $assignee_play_id;
    public $item_id;
    public $date;
    public $length;
    public $notes;
    public $status;
    public $price;
    public $lat;
    public $lon;
    public $created_at;
    public $updated_at;

    public function tableName()
    {
        return 'ae_ext_bookings';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionMitems\Models\ItemModel', 'item_id'),
        );
    }
}