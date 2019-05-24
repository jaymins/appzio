<?php

namespace backend\modules\fitness\controllers;

use backend\modules\fitness\models\AeExtFitExercise;
use backend\modules\fitness\models\AeExtFitExerciseComponent;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtFitExerciseController".
 */
class AeExtFitExerciseController extends \backend\modules\fitness\controllers\base\AeExtFitExerciseController
{
    /**
     * Creates a new AeExtFitExercise model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtFitExercise;

        $session = \Yii::$app->session;

        if (isset($session['app_id'])) {
            $model->app_id = $session['app_id'];
        }

        try {
            if ($model->load($_POST)) {
                if ($model->save()) {
                    $this->relationEntries($model, false);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
            'component' => AeExtFitExercise::getAllComponents(),
            'relations_json' => null
        ]);
    }

    /**
     * Updates an existing AeExtFitExercise model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->save()) {
            if ($relations = Yii::$app->request->post('outer-group')) {
                $this->relationEntries($model, true);
            }
            //return $this->redirect(Url::previous());
        }
            $relations = AeExtFitExercise::getRelationsByID($id);

            return $this->render('update', [
                'model' => $model,
                'component' => AeExtFitExercise::getAllComponents(),
                'relations_json' => $this->getFormattedRelations($relations)
            ]);
    }

    private function getFormattedRelations(array $relations)
    {

        if (empty($relations)) {
            return [];
        }

        $output = [];

        foreach ($relations as $i => $relation) {
            $output[$i]['field-input-id'] = $relation->id;
            $output[$i]['field-select-relation-id'] = $relation->component_id;
        }
        return json_encode($output);
    }

   /* private function getInnerGroup($content)
    {
        $output = [];
        foreach ($content as $i => $sub_relation) {
            $output[$i]['field-select-sub-relation-id'] = $sub_relation->movement_id;
            $output[$i]['field-input-sub-weight'] = $sub_relation->weight;
            $output[$i]['field-input-sub-unit'] = $sub_relation->unit;
            $output[$i]['field-input-sub-reps'] = $sub_relation->reps;
            $output[$i]['field-input-sub-rest'] = $sub_relation->rest;
            $output[$i]['field-input-sub-time'] = $sub_relation->movement_time;
            $output[$i]['field-input-sub-id'] = $sub_relation->id;
        }
        return $output;
    }*/

    private function relationEntries($model, $do_update)
    {
        $post = Yii::$app->request->post('outer-group');
        $ex_id = $model->id;
        if ($post) {
            AeExtFitExerciseComponent::addOrUpdateRelations($post, $ex_id, $do_update);
        }
    }
}