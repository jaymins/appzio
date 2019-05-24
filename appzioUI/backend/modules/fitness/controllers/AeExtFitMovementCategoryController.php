<?php

namespace backend\modules\fitness\controllers;

use backend\modules\fitness\models\AeExtFitExercise;
use backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement;
use backend\modules\fitness\models\AeExtFitMovementCategory;
use Yii;
use yii\helpers\Url;
/**
* This is the class for controller "AeExtFitMovementCategoryController".
*/
class AeExtFitMovementCategoryController extends \backend\modules\fitness\controllers\base\AeExtFitMovementCategoryController
{
    public function actionCreate()
    {
        $model = new AeExtFitMovementCategory;
        try {
            if ($model->load($_POST) ) {
                if ($relations = Yii::$app->request->post('outer-group') && $model->save()) {
                    $this->relationEntries($model, false);
                    //return $this->redirect(['view', 'id' => $model->id]);
                }
            } elseif (!\Yii::$app->request->isPost) {
                $model->load($_GET);
            }
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
            'movements' => AeExtFitExercise::getAllMovements(),
            'relations_json' => null
        ]);
    }

    /**
     * Updates an existing AeExtFitExerciseMovementCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {
            return $this->render('create', [
                'model' => $model,
                'movements' => AeExtFitExercise::getAllMovements(),
                'relations_json' => null
            ]);
        }
    }
    private function relationEntries($model, $do_update)
    {

        $post = Yii::$app->request->post('outer-group');
        $ex_id = $model->id;
        if ($post) {
            AeExtFitExerciseMcategoryMovement::addOrUpdateRelations($post, $ex_id, $do_update);
        }
    }
}
