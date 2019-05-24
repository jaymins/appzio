<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ExerciseMovementTimerModel extends CActiveRecord
{

    public $id;
    public $timer_relation_id;
    public $round;
    public $movement;
    public $type;
    public $step;
    public $time;
    public $start;
    public $stop;

    public function tableName()
    {
        return 'ae_ext_exercise_timer';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
        );
    }

}