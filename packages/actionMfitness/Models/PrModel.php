<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class PrModel extends CActiveRecord
{

    public $id;
    public $app_id;
    public $title;
    public $unit;

    public function tableName()
    {
        return 'ae_ext_fit_pr';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'pr_user' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\PrUserModel', [
                'id' => 'pr_id'
            ],'order' => 'date DESC'),
        );
    }

}