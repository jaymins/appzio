<?php

namespace packages\actionMcalendar\Models;

use CActiveRecord;

class CalendarModel extends CActiveRecord
{

    public $id;
    public $play_id;
    public $type_id;
    public $exercise_id;
    public $program_id;
    public $recipe_id;
    public $notes;
    public $points;
    public $time;
    public $completion;
    public $is_completed;
    public $completed_at;

    public function tableName()
    {
        return 'ae_ext_calendar_entry';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'exercise' => [
                self::BELONGS_TO, 'packages\actionMfitness\Models\ExerciseModel', 'exercise_id'
            ],
            'program' => [
                self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramModel', 'program_id'
            ],
            'recipe' => [
                self::BELONGS_TO, 'packages\actionMfood\Models\RecipeModel', 'recipe_id'
            ],
            'category' => [
                self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramCategoriesModel', 'type_id'
            ],
        ];
    }

    public static function getCurrentUserCalendarData(int $play_id, int $program_id)
    {
        $criteria = new \CDbCriteria;
        $criteria->alias = 'calendar';
        $criteria->with = [
            'exercise',
            'recipe'
        ];
        $criteria->condition = 'calendar.play_id = :play_id AND calendar.program_id <> :program_id AND calendar.time >= :timestamp';
        $criteria->params = [
            ':play_id' => $play_id,
            ':program_id' => $program_id, // Fail-safe
            ':timestamp' => time(),
        ];

        $entries = self::model()->findAll($criteria);

        if (empty($entries)) {
            return [];
        }

        $stamps = [];

        /** @var CalendarModel $entry */
        foreach ($entries as $entry) {
            $stamps[$entry->time] = $entry;
        }

        return $stamps;
    }

    public static function markCompleted(int $entry_id)
    {
        if (empty($entry_id)) {
            return false;
        }

        $entry = CalendarModel::model()->findByPk($entry_id);

        if (empty($entry)) {
            return false;
        }

        $entry->is_completed = 1;
        $entry->completed_at = time();
        $entry->save();

        return true;
    }

    public static function unmarkCompleted($entry_id)
    {
        if (empty($entry_id)) {
            return false;
        }

        $entry = CalendarModel::model()->findByPk($entry_id);

        if (empty($entry)) {
            return false;
        }

        $entry->is_completed = 0;
        $entry->completed_at = null;
        $entry->save();

        return true;
    }

    public static function insertEntries(array $data)
    {
        try {
            $builder = \Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand('ae_ext_calendar_entry', $data);
            $command->execute();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public static function deleteAllUserEntries(int $play_id)
    {
        CalendarModel::model()->deleteAllByAttributes([
            'play_id' => $play_id
        ]);

        return true;
    }

}