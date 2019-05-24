<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramCategoriesModel extends CActiveRecord
{

    public $id;
    public $name;
    public $icon;
    public $color;
    public $category_order;
    public $app_id;

    public function tableName()
    {
        return 'ae_ext_fit_program_category';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}