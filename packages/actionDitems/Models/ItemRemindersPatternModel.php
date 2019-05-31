<?php


namespace packages\actionDitems\Models;

use CActiveRecord;

class ItemRemindersPatternModel extends CActiveRecord
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
            'item_reminder' => array(self::BELONGS_TO, 'packages\actionDitems\Models\ItemRemindersModel', 'id')
        );
    }

	/**
	 * Save a new pattern in storage.
	 *
	 * @param $variables
	 * @return ItemRemindersPatternModel
	 */
	public static function store($variables) {

        $do_insert = false;

        $pattern = self::model()->findByAttributes([
            'item_reminder_id' => $variables['item_reminder_id']
        ]);

        if ( empty($pattern) ) {
            $pattern = new ItemRemindersPatternModel();
            $do_insert = true;
        }

		foreach ($variables as $key => $value) {
			if (!property_exists(ItemRemindersPatternModel::class, $key)) {
				continue;
			}

			$pattern->{$key} = $value;
		}

        if ( $do_insert ) {
            $pattern->insert();
        } else {
            $pattern->update();
        }

		return $pattern;
	}

}