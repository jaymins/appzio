<?php

namespace backend\modules\fitness\controllers;

use backend\modules\fitness\models\AeExtFitProgramCategory;
use backend\components\Helper;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtFitProgramCategoryController".
 */
class AeExtFitProgramCategoryController extends \backend\modules\fitness\controllers\base\AeExtFitProgramCategoryController
{
    /**
     * Creates a new AeExtFitProgramCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtFitProgramCategory;
        $session = \Yii::$app->session;

        if (isset($session['app_id'])) {
            $model->app_id = $session['app_id'];
        }
        try {
            if ($model->load($_POST)) {
                $model = Helper::addImageUploads($model, 'AeExtFitProgramCategory');

                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $model->load($_GET);
                }

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
     * Updates an existing AeExtFitProgramCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post('AeExtFitProgramCategory');
        if ($post['icon']) {
            $icon = Helper::addImageUploads($model, 'AeExtFitProgramCategory');
        } elseif ($model->icon) {
            $icon = $model->icon;
        } else {
            $icon = '';
        }

        if ($model->load($_POST)) {
            $model->icon = $icon;
            $model = Helper::addImageUploads($model, 'AeExtFitProgramCategory');
            if ($model->save()) {
                return $this->redirect(Url::previous());
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            \Yii::$app->getSession()->addFlash('error', $msg);
            return $this->redirect(Url::previous());
        }

        $isPivot = strstr('$id', ',');
        if ($isPivot == true) {
            return $this->redirect(Url::previous());
        } elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
            Url::remember(null);
            $url = \Yii::$app->session['__crudReturnUrl'];
            \Yii::$app->session['__crudReturnUrl'] = null;

            return $this->redirect(['index']);
        } else {
            return $this->redirect(['index']);
        }
    }
}
