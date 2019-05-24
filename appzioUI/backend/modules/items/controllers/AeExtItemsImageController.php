<?php

namespace backend\modules\items\controllers;

use backend\modules\items\models\AeExtItemsImage;
use yii;
use backend\components\Helper;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtItemsImageController".
 */
class AeExtItemsImageController extends \backend\modules\items\controllers\base\AeExtItemsImageController
{

    /**
     * Creates a new AeExtItemsImage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtItemsImage;

        try {

            if ( Yii::$app->request->isPost AND $model->load($_POST) ) {
                
                $model = Helper::addImageUploads($model, 'AeExtItemsImage');

                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $model->load($_GET);
                }

            } else {
                $model->load($_GET);
            }

        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing AeExtItemsImage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ( Yii::$app->request->isPost AND $model->load($_POST) ) {

            $model = Helper::addImageUploads($model, 'AeExtItemsImage');

            if ($model->save()) {
                return $this->redirect(Url::previous());
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

    }

}
