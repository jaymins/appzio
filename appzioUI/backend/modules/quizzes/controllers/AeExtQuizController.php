<?php

namespace backend\modules\quizzes\controllers;

use backend\modules\quizzes\models\AeExtQuiz;
use backend\modules\quizzes\models\AeExtQuizSet;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtQuizController".
 */
class AeExtQuizController extends \backend\modules\quizzes\controllers\base\AeExtQuizController
{

    /**
     * Creates a new AeExtQuiz model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtQuiz;

        try {
            if ($model->load($_POST) && $model->save()) {

                if ($questions = Yii::$app->request->post('outer-group')) {
                    $app_id = $model->app_id;
                    $quiz_id = $model->id;
                    AeExtQuizSet::addOrUpdateRelations($questions, $app_id, $quiz_id, false);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }

        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
            'questions' => AeExtQuizQuestionController::getAllQuestions(),
            'questions_json' => '',
        ]);
    }

    /**
     * Updates an existing AeExtQuiz model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Get the updated relations
        if ($relations = Yii::$app->request->post('outer-group')) {
            $app_id = $model->app_id;
            AeExtQuizSet::addOrUpdateRelations($relations, $app_id, $id, true);
        }

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {

            // Preload the relations set
            $relations = AeExtQuizSet::getRelationsByID($id);

            return $this->render('update', [
                'model' => $model,
                'questions' => AeExtQuizQuestionController::getAllQuestions(),
                'questions_json' => $this->getFormattedRelations($relations),
            ]);
        }
    }

    private function getFormattedRelations(array $relations)
    {

        if (empty($relations)) {
            return [];
        }

        $output = [];

        foreach ($relations as $i => $relation) {
            $output[$i]['field-input-id'] = $relation->id;
            $output[$i]['field-select-question'] = $relation->question_id;
        }

        return json_encode($output);
    }

}