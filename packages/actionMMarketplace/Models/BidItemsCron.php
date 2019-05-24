<?php

class BidItemsCron extends CActiveRecord
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

    public $gid;

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
        return array();
    }

    public function cancelExpiredBids() {

        $sql = "SELECT `ae_ext_bid_items`.id, `ae_ext_bid_items`.valid_date  FROM `ae_ext_bid_items` "
            . "LEFT JOIN `ae_ext_user_bids` ON `ae_ext_bid_items`.id = `ae_ext_user_bids`.bid_item_id "
            . "WHERE `ae_ext_user_bids`.id IS NULL AND `ae_ext_bid_items`.valid_date < UNIX_TIMESTAMP()";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        if ( empty($rows) ) {
            return false;
        }

        foreach ($rows as $row) {
            $sqlUpdate = "UPDATE ae_ext_bid_items SET status = :status WHERE id = :id";
            \Yii::app()->db
                ->createCommand($sqlUpdate)
                ->bindValues(array(
                    ':status' => 'cancelled',
                    ':id' => $row['id']))
                ->query();
        }

        return true;
    }

}