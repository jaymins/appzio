<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ExerciseComponentModel extends CActiveRecord
{

    public $id;
    public $exercise_id;
    public $component_id;
    public $sorting;

    public function tableName()
    {
        return 'ae_ext_fit_exercise_component';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function relations()
    {
        return array(
            'movementsjoin' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ComponentMovementModel',
                'component_id','order' => 'movementsjoin.sorting ASC'),
            'component' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ComponentModel', 'component_id'),
            'category_details' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ComponentModel', 'component_id'),
            //'movements' => array(self::HAS_MANY, 'packages\actionMfitness\Models\MovementModel', 'ae_ext_fit_component_movement(component_id, movement_id)'),
        );
    }

}