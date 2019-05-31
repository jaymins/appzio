<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemReportModel extends CActiveRecord
{
    public $id;
    public $play_id;
    public $item_id;
    public $item_owner_id;
    public $reason;
    public $created_at;
    public $updated_at;

    public function tableName()
    {
        return 'ae_ext_items_reports';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemModel', 'item_id')
        );
    }
}