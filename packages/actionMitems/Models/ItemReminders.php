<?php

namespace packages\actionMitems\Models;

use Bootstrap\Models\BootstrapModel;

trait ItemReminders
{

    public $reminderRequiredVars = array(
        'item_id' => 'empty',
        'type' => 'empty',
        'date' => 'empty',
        'recurring' => 'empty',
        'frequency' => 'empty',
        'to_calendar' => 'empty',
    );


    public function getRemindersForUser($playId = null)
    {

        /** @var BootstrapModel $this */

        if ($playId == null) {
            $playId = $this->playid;
        }

        return ItemRemindersModel::model()->with('item')->findAll(
            'play_id = :playId',
            array(':playId' => $playId)
        );
    }

    public function getItemReminders($itemId)
    {

        return ItemRemindersModel::model()
            ->with('item')
            ->findAllByAttributes(
                array('item_id' => $itemId)
            );
    }

    /**
     * Retrieve all items and the related data based on certain item type
     * If $type is false, the query would return all posible data types
     *
     * @param $type
     * @return array|mixed|null|static[]
     */
    public function getRemindersByType($type = 'routine')
    {
        $timestamp = time();

        $criteria = new \CDbCriteria;
        $criteria->alias = 'item';
        $criteria->condition = "item.play_id = $this->playid";
        $criteria->condition .= " AND
			(
				item.type = 'routine' AND (
					reminder.date >= $timestamp OR
					$timestamp <= reminder.end_date
				) XOR item.type <> 'routine'
			)
		";

        if ($type) {
            $criteria->condition .= " AND item.type = '$type'";
        }

        $items = ItemModel::model()->with(array(
            'reminders' => array(
                'with' => 'pattern',
                'alias' => 'reminder',
            )
        ))->findAll($criteria);
        
        if (empty($items)) {
            return [];
        }
        
        $output = [];

        foreach ($items as $item) {

            if (!isset($item->reminders[0])) {
                continue;
            }

            $reminder = $item->reminders[0];

            $date = $reminder->date;

            if (!empty($reminder->pattern)) {
                $date = $this->getNextValidDate($reminder);
            }

            $item->next_date = $date;

            $output[] = $item;
        }

        usort($output, array($this, 'nextDateSort'));

        return $output;
    }

    /**
     * Retrieve a single event ( routine ) item by its ID
     *
     * @param $id
     * @return array|mixed|null|static[]
     */
    public function getReminderByID(int $id)
    {
        return ItemModel::model()->with(array(
            'reminders' => array(
                'with' => 'pattern',
                'alias' => 'reminder',
            )
        ))->findByPk($id);
    }

    public function getAllReminders()
    {

        $timestamp = time();

        $criteria = new \CDbCriteria;
        $criteria->alias = 'reminder';
        $criteria->order = 'item.id DESC';
        $criteria->condition = "item.play_id = $this->playid";
        $criteria->condition .= " AND
			(
				item.type = 'routine' AND (
					reminder.date >= $timestamp OR
					$timestamp <= reminder.end_date
				) XOR item.type <> 'routine'
			)
		";
        $criteria->condition .= " AND is_completed <> 1";

        return ItemRemindersModel::model()->with(array(
            'item' => array(
                'alias' => 'item'
            ),
            'pattern' => array(
                'alias' => 'pattern'
            )
        ))->findAll($criteria);
    }

    public function validateReminder()
    {
        $submittedVariables = $this->getAllSubmittedVariablesByName();

        foreach ($submittedVariables as $key => $value) {

            foreach ($this->reminderRequiredVars as $var => $validation_type) {

                if ($key != $var) {
                    continue;
                }

                $validations = explode('|', $validation_type);
                foreach ($validations as $validation) {

                    if ($validation == 'empty' AND empty($value)) {
                        $this->validation_errors[$key] = 'The ' . $key . ' field is required';
                    }

                }
            }

        }

    }

    public function saveReminder($id = null)
    {

        if ($id == null) {
            $reminder = new ItemRemindersModel();
        } else {
            $reminder = ItemRemindersModel::model()->findByPk($id);

            if (!is_object($reminder)) {
                $reminder = new ItemRemindersModel();
                $id = null;
            }
        }

        $submittedVariables = $this->getAllSubmittedVariablesByName();

        foreach ($submittedVariables as $key => $value) {

            if (property_exists('ItemRemindersModel', $key)) {
                $reminder->{$key} = $value;
            }
        }

        if ($id == null) {
            $reminder->insert();
        } else {
            $reminder->update();
        }
    }

    public function deleteReminder($id)
    {
        return ItemRemindersModel::model()->deleteByPk($id);
    }

}