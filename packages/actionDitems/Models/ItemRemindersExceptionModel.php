<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemRemindersExceptionModel extends CActiveRecord
{
    public $id;
    public $item_reminder_id;
    public $is_rescheduled;
    public $is_cancelled;
    public $start_date;
    public $end_date;
    public $start_time;
    public $end_time;
    public $is_full_day_event;
    public $created_date;

    public function tableName()
    {
        return 'ae_ext_items_reminders_exception';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item_reminder' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemRemindersModel', 'id')
        );
    }

}