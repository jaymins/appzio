<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramModel extends CActiveRecord
{

    public $id;
    public $app_id;
    public $name;
    public $category_id;
    public $subcategory_id;
    public $article_id;
    public $program_type;
    public $program_sub_type;
    public $is_challenge;
    public $exercises_per_day;

    public function tableName()
    {
        return 'ae_ext_fit_program';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'articles' => array(self::HAS_MANY, 'packages\actionMarticles\Models\ArticlephotosModel', 'article_id'),
            'category' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramCategoriesModel', 'category_id'),
            'subcategory' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramSubcategoriesModel', 'subcategory_id'),
            'exercises' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ProgramExerciseModel', 'program_id'),
            'recipes' => array(self::HAS_MANY, 'packages\actionMfitness\Models\ProgramRecipeModel', 'program_id'),
        );
    }

    public static function getProgramDetails(int $current_program_id, array $include_relations = [])
    {
        $data = [
            'category',
            'exercises' => [
                'alias' => 'exercises'
            ],
            'recipes' => [
                'alias' => 'recipes'
            ]
        ];

        if (!empty($include_relations)) {
            $data = $include_relations;
        }

        $criteria = new \CDbCriteria;
        $criteria->select = '*';
        $criteria->with = $data;
        $criteria->condition = 't.id= ' . $current_program_id;

        if (isset($data['exercises']) AND isset($data['recipes'])) {
            $criteria->order = 'exercises.week ASC, exercises.priority ASC, recipes.week ASC, recipes.recipe_order ASC';
        }

        $program_data = self::model()->find($criteria);

        return $program_data;
    }

}