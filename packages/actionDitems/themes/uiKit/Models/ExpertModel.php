<?php

namespace packages\actionDitems\themes\uiKit\Models;

use CActiveRecord;

class ExpertModel extends CActiveRecord
{

    public $id;
    public $name;
    public $email;
    public $position;
    public $business_unit;
    public $country;

    public function tableName()
    {
        return 'ae_ext_first_choice_experts';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array();
    }

}