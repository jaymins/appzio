<?php


namespace packages\actionMquiz\Models;

use CActiveRecord;

class QuizSetModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $quiz_id;
    public $question_id;
    public $sorting;

    public function tableName()
    {
        return 'ae_ext_quiz_sets';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'quiz' => array(self::BELONGS_TO, 'packages\actionMquiz\Models\QuizModel', 'quiz_id', 'joinType'=>'LEFT JOIN'),
            'question' => array(self::BELONGS_TO, 'packages\actionMquiz\Models\QuizQuestionModel', 'question_id', 'joinType'=>'LEFT JOIN','order' => 'sorting ASC'),
        );
    }

    public function scopes() {
        return array(
            'inorder' => array('order' => 'sorting ASC'),
        );
    }

}