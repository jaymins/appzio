<?php


namespace packages\actionMquiz\Models;

use CActiveRecord;

class QuizQuestionAnswerModel extends CActiveRecord
{
    public $id;
    public $question_id;
    public $answer_id;
    public $answer;
    public $play_id;
    public $comment;
    public $date_created;

    public function tableName()
    {
        return 'ae_ext_quiz_question_answer';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'quiz' => array(self::HAS_MANY, 'packages\actionMquiz\Models\QuizModel', 'question_id', 'joinType'=>'LEFT JOIN')
        );
    }
}