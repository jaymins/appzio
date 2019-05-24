<?php


namespace packages\actionMquiz\Models;

use CActiveRecord;

class QuizQuestionModel extends CActiveRecord
{
    public $id;
    public $page_id;
    public $quiz_id;
    public $variable_name;
    public $title;
    public $question;
    public $active;
    public $picture;
    public $answer_options;
    public $type;
    public $allow_multiple;
    public $correct_answer;

    public function tableName()
    {
        return 'ae_ext_quiz_question';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'option' => array(self::HAS_MANY, 'packages\actionMquiz\Models\QuizOptionModel', 'question_id', 'joinType'=>'LEFT JOIN')
        );
    }
}