<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ExerciseRelationTimerModel extends CActiveRecord
{

    public $id;
    public $calendar_entry_id;
    public $component_id;
    public $movement_id;
    public $action;
    public $timer_type;
    public $timer_status;
    public $timestamp;

    public function tableName()
    {
        return 'ae_ext_exercise_timer_relations';
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