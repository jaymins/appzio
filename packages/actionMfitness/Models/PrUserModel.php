<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class PrUserModel extends CActiveRecord
{

    public $id;
    public $play_id;
    public $pr_id;
    public $value;
    public $date;

    public function tableName()
    {
        return 'ae_ext_fit_pr_user';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'pr' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\PrModel', [
                'pr_id' => 'id'
            ],'joinType' => 'LEFT JOIN'),
        );
    }

}