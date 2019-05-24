<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramExerciseModel extends CActiveRecord
{

    public $id;
    public $program_id;
    public $exercise_id;
    public $week;
    public $day;
    public $priority;
    public $time;
    public $repeat_days;

    public function tableName()
    {
        return 'ae_ext_fit_program_exercise';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'programs' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ProgramModel', 'program_id'),
            'programexercises' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ExerciseModel', 'exercise_id'),
        );
    }

    public static function getExercisesByProgramID(int $program_id) {
        $criteria = new \CDbCriteria(array(
            'condition' => 'program_id = :program_id',
            'params' => array(
                ':program_id' => $program_id,
            ),
            'order' => 'id ASC'
        ));

        $exercises = self::model()->findAll($criteria);

        if (empty($exercises)) {
            return false;
        }

        return $exercises;
    }

}