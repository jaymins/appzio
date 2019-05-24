<?php

namespace backend\modules\quizzes\controllers;

use backend\modules\quizzes\models\AeExtQuizQuestion;
use backend\modules\quizzes\models\AeExtQuizQuestionOption;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtQuizQuestionController".
 */
class AeExtQuizQuestionController extends \backend\modules\quizzes\controllers\base\AeExtQuizQuestionController
{

    /**
     * Creates a new AeExtQuizQuestion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtQuizQuestion;

        try {
            if ($model->load($_POST) && $model->save()) {

                if ($answers = Yii::$app->request->post('outer-group')) {
                    $question_id = $model->id;
                    AeExtQuizQuestionOption::addOrUpdateAnswers($answers, $question_id, false);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }

        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing AeExtQuizQuestion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Get the updated answers
        if ($answers = Yii::$app->request->post('outer-group')) {
            AeExtQuizQuestionOption::addOrUpdateAnswers($answers, $id, true);
        }

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {

            // Preload the question's answers
            $answers = AeExtQuizQuestionOption::getQuestionOptions($id);

            return $this->render('update', [
                'model' => $model,
                'answers' => $this->getFormattedAnswers($answers)
            ]);
        }
    }

    private function getFormattedAnswers(array $answers)
    {

        if (empty($answers)) {
            return [];
        }

        $output = [];

        foreach ($answers as $i => $answer) {
            $output[$i]['field-input-id'] = $answer->id;
            $output[$i]['field-input'] = $answer->answer;
            $output[$i]['field-input-status'] = $answer->is_correct;
        }

        return json_encode($output);
    }

    public static function getAllQuestions()
    {
        $questions = AeExtQuizQuestion::find()
            ->asArray() // optional
            ->all();

        if ($questions) {
            return $questions;
        }

        return [];
    }

}