<?php

namespace backend\modules\fitness\controllers;

use backend\components\Helper;
use backend\modules\fitness\models\AeExtFitComponent;
use backend\modules\fitness\models\AeExtFitComponentMovement;
use backend\modules\fitness\search\AeExtFitMovement;
use backend\modules\fitness\search\AeExtFitPr;
use Yii;
use yii\helpers\Url;
/**
* This is the class for controller "AeExtFitComponentController".
*/
class AeExtFitComponentController extends \backend\modules\fitness\controllers\base\AeExtFitComponentController
{

    /**
     * Creates a new AeExtFitComponent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtFitComponent;

        try {
            if ($model->load($_POST)) {

                $model = Helper::addImageUploads($model, 'AeExtFitComponent');
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
            'movements' => AeExtFitMovement::getAllMovements(),
            'pr' => AeExtFitPr::getAllPr(),
            'relations_json' => null
        ]);
    }

    /**
     * Updates an existing AeExtFitComponent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post('AeExtFitComponent');
        if ($post['background_image']) {
            $photo = Helper::addImageUploads($model, 'AeExtFitComponent');
        } elseif ($model->background_image) {
            $photo = $model->background_image;
        } else {
            $photo = '';
        }
        if ($model->load($_POST) ) {
            $model->background_image = $photo;
            $model = Helper::addImageUploads($model, 'AeExtFitComponent');
            if (Yii::$app->request->post('background_image')) {
                $photo = Helper::addImageUploads($model, 'AeExtFitComponent');
            } elseif ($model->background_image) {
                $photo = $model->background_image;
            } else {
                $photo = '';
            }
            $model->save();
            if ($relations = Yii::$app->request->post('outer-group')) {
                $this->relationEntries($model, true);
            }
            //return $this->redirect(Url::previous());
        }

            $relations = AeExtFitComponent::getRelationsByID($id);

            return $this->render('update', [
                'model' => $model,
                'movements' => AeExtFitMovement::getAllMovements(),
                'pr' => AeExtFitPr::getAllPr(),
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
            $output[$i]['field-select-relation-id'] = $relation->movement_id;
            $output[$i]['field-select-weight'] = $relation->weight;
            $output[$i]['field-select-unit'] = $relation->unit;
            $output[$i]['field-select-reps'] = $relation->reps;
            $output[$i]['field-select-pr'] = $relation->pr_id;
            $output[$i]['field-select-movement_time'] = $relation->movement_time;
            $output[$i]['field-select-time'] = $relation->movement_time;
        }
        return json_encode($output);
    }
    private function relationEntries($model, $do_update)
    {
        $post = Yii::$app->request->post('outer-group');
        $ex_id = $model->id;
        if ($post) {
            AeExtFitComponentMovement::addOrUpdateRelations($post, $ex_id, $do_update);
        }
    }
}
