<?php

class CalendarNotificationsModelCron extends CActiveRecord
{

    public $id;
    public $play_id;
    public $notification_id;
    public $calendar_entry_id;
    public $item_id;
    public $text;
    public $type;
    public $timestamp;
    public $status;

    public function tableName()
    {
        return 'ae_ext_calendar_notification';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'calendar_entry' => [self::BELONGS_TO, 'packages\actionMcalendar\Models\CalendarModel', 'calendar_entry_id'],
            'item' => [self::BELONGS_TO, 'packages\actionMitems\Models\ItemModel', 'item_id'],
        ];
    }

    public static function insertNotification($play_id, $notification_id, $message)
    {
        try {
            $model = new self();
            $model->play_id = $play_id;
            $model->notification_id = $notification_id;
            $model->text = $message;
            $model->type = 'reminder';
            $model->timestamp = time();
            $model->status = 0;
            $model->insert();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}