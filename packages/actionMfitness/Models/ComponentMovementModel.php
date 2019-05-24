<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ComponentMovementModel extends CActiveRecord
{

    public $id;
    public $component_id;
    public $weight;
    public $reps;
    public $sorting;
    public $points;
    public $movement_id;
    public $movement_time;

    public function tableName()
    {
        return 'ae_ext_fit_component_movement';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'movement' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\MovementModel', 'movement_id'),
            'pr' => array(self::BELONGS_TO,'packages\actionMfitness\Models\PrModel','pr_id')
        );
    }

}