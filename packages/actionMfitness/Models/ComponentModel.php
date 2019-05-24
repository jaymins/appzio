<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ComponentModel extends CActiveRecord
{

    public $id;
    public $name;
    public $timer_type;
    public $background_image;
    public $timer;
    public $rounds;
    public $total_time;

    public function tableName()
    {
        return 'ae_ext_fit_component';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [
            'movementsjoin' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ComponentMovementModel','component_id')
            /*'component_id_details' => [
                self::HAS_ONE, 'packages\actionMfitness\Models\ComponentModel', 'component_id',
            ],*/
/*            'movements_joindata' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ComponentMovementModel','component_id'),
            'movements' => array(self::MANY_MANY, 'packages\actionMfitness\Models\MovementModel', 'ae_ext_fit_component_movement(component_id,movement_id)'),*/
        ];
    }

}