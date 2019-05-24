<?php

namespace backend\modules\quizzes\models;

use backend\modules\quizzes\models\base\AeExtQuizQuestionOption as BaseAeExtQuizQuestionOption;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_quiz_question_option".
 */
class AeExtQuizQuestionOption extends BaseAeExtQuizQuestionOption
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }

    public static function getQuestionOptions($question_id)
    {
        $answers = AeExtQuizQuestionOption::find()
            ->where([
                'question_id' => $question_id
            ])
            ->orderBy([
                'answer_order' => SORT_ASC,
                'id' => SORT_ASC
            ])
            ->all();

        if ($answers) {
            return $answers;
        }

        return [];
    }

    public static function addOrUpdateAnswers($answers, $question_id, $check_for_deleted)
    {

        if (empty($answers)) {
            return false;
        }

        $count = 0;
        $ids = [];
        $current_answer_ids = [];

        if ( $check_for_deleted ) {
            $original_answers = self::find()
                ->select('id')
                ->where([
                    'question_id' => $question_id
                ])
                ->asArray()
                ->all();

            $ids = array_column($original_answers, 'id');
        }

        foreach ($answers as $answer) {

            $do_update = false;
            $answer_id = $answer['field-input-id'];
            $text = $answer['field-input'];
            $is_correct = (isset($answer['field-input-status'][0]) ? $answer['field-input-status'][0] : false);

            if ($answer_id AND $answers_model = AeExtQuizQuestionOption::findOne($answer_id)) {
                $current_answer_ids[] = $answer_id;
                $do_update = true;
            } else {
                $answers_model = new AeExtQuizQuestionOption();
            }

            $answers_model->question_id = $question_id;
            $answers_model->answer = $text;
            $answers_model->is_correct = ($is_correct ? 1 : 0);
            $answers_model->answer_order = $count;

            if ($do_update) {
                $answers_model->update();
            } else {
                $answers_model->save();
            }

            $count++;
        }

        if ( $ids AND $current_answer_ids ) {
            $deleted_items = array_diff($ids, $current_answer_ids);

            if ( $deleted_items ) {
                foreach ($deleted_items as $item_id) {
                    AeExtQuizQuestionOption::deleteAll([
                        'id' => $item_id
                    ]);
                }
            }

        }

        return true;
    }

}