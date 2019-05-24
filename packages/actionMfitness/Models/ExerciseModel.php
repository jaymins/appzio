<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ExerciseModel extends CActiveRecord
{

    public $id;
    public $name;
    public $article_id;
    public $points;
    public $app_id;
    public $category_id;
    public $duration;

    public function tableName()
    {
        return 'ae_ext_fit_exercise';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'article' => array(self::HAS_ONE, 'packages\actionMarticles\Models\ArticlesModel', 'id'),
            'componentsjoin' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ExerciseComponentModel','exercise_id',
                'order' => 'componentsjoin.sorting ASC'),

/*          'jointable' => array(self::HAS_ONE, 'packages\actionMfitness\Models\ExerciseComponentModel','exercise_id'),
            'components' => array(self::MANY_MANY,
                'packages\actionMfitness\Models\ComponentModel',
                'ae_ext_fit_exercise_component(exercise_id,component_id)',
                //'with' => 'jointable',
                'order' => 'jointable.sorting'
            )*/
        );
    }

}