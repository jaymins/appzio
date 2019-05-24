<?php

class QuizModelDB extends CActiveRecord
{
    public $id;
    public $app_id;
    public $name;
    public $description;
    public $valid_from;
    public $valid_to;
    public $active;
    public $show_in_list;
    public $image;

    public function tableName()
    {
        return 'ae_ext_quiz';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'category_relations' => array(self::HAS_MANY, 'packages\actionMitems\Models\ItemCategoryRelationModel', 'category_id', 'joinType'=>'LEFT JOIN')
        );
    }
}