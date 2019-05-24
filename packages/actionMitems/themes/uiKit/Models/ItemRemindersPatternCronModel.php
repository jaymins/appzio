<?php

class ItemRemindersPatternCronModel extends CActiveRecord
{
    public $id;
    public $item_reminder_id;
    public $recurring_type;
    public $max_num_of_occurrances;
    public $day_of_week;
    public $day_of_month;
    public $week_of_month;
    public $month_of_year;
    public $separation_count;

    public function tableName()
    {
        return 'ae_ext_items_reminders_pattern';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item_reminder' => array(self::BELONGS_TO, 'ItemsCron', 'id')
        );
    }

}