<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramRecipeModel extends CActiveRecord
{

    public $id;
    public $program_id;
    public $recipe_id;
    public $week;
    public $recipe_order;

    public function tableName()
    {
        return 'ae_ext_fit_program_recipe';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'programs' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ProgramModel', 'program_id'),
        );
    }

}