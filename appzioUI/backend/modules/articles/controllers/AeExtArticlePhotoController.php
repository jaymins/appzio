<?php

namespace backend\modules\articles\controllers;

use backend\components\Helper;
use backend\modules\articles\models\AeExtArticlePhoto;
use yii;

/**
 * This is the class for controller "AeExtArticlePhotoController".
 */
class AeExtArticlePhotoController extends \backend\modules\articles\controllers\base\AeExtArticlePhotoController
{

    /**
     * Creates a new AeExtArticlePhoto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtArticlePhoto;

        try {

            if (Yii::$app->request->isPost AND $model->load($_POST)) {
                $model = Helper::addImageUploads($model, 'AeExtArticlePhoto');

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
     * Updates an existing AeExtArticlePhoto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

}
