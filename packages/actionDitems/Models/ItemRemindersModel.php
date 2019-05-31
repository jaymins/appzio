<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemRemindersModel extends CActiveRecord
{
    public $id;
    public $item_id;
    public $name;
    public $message;
    public $type;
    public $date;
    public $end_date;
    public $created_date;
    public $is_full_day;
    public $parent_reminder_id;
    public $recurring;
    public $frequency;
    public $to_calendar;
    public $is_completed;
    public $notification_sent;
    public $sent_recurring_notifications;

    public $next_date;

    public function tableName()
    {
        return 'ae_ext_items_reminders';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemModel', 'item_id'),
            'pattern' => array(self::HAS_ONE, 'packages\actionDitems\Models\ItemRemindersPatternModel', 'item_reminder_id'),
        );
    }

	/**
	 * Save a new reminder in storage.
	 *
	 * @param $variables
	 * @return ItemRemindersModel
	 */
	public static function store($variables) {

        $do_insert = false;

        $reminder = self::model()->findByAttributes([
            'item_id' => $variables['item_id']
        ]);

        if ( empty($reminder) ) {
            $reminder = new ItemRemindersModel();
            $do_insert = true;
        }

		foreach ($variables as $key => $value) {
			if (!property_exists(ItemRemindersModel::class, $key)) {
				continue;
			}

			$reminder->{$key} = $value;
		}

		if ( $do_insert ) {
            $reminder->insert();
        } else {
            $reminder->update();
        }

        return $reminder;
	}

	public function markCompleted( $reminder_id ) {

		$reminders_model = new ItemRemindersModel();
		$reminder = $reminders_model->findByPk( $reminder_id );

		if ( !is_object($reminder) ) {
			return false;
		}

		$reminder->is_completed = 1;
		$reminder->update();

		return true;
	}

}