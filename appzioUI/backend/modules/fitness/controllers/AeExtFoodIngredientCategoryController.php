<?php

namespace backend\modules\fitness\controllers;

use backend\components\Helper;
use backend\modules\fitness\models\AeExtFoodIngredientCategory;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtFoodIngredientCategoryController".
 */
class AeExtFoodIngredientCategoryController extends \backend\modules\fitness\controllers\base\AeExtFoodIngredientCategoryController
{
    public function actionCreate()
    {
        $model = new AeExtFoodIngredientCategory;

        $session = \Yii::$app->session;

        if (isset($session['app_id'])) {
            $model->app_id = $session['app_id'];
        }

        try {
            if ($model->load($_POST)) {

                $model = Helper::addImageUploads($model, 'AeExtFoodIngredientCategory');

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
     * Updates an existing AeExtFoodIngredientCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post('AeExtFoodIngredientCategory');
        if ($post['icon']) {
            $icon = Helper::addImageUploads($model, 'AeExtFoodIngredientCategory');
        } elseif ($model->icon) {
            $icon = $model->icon;
        } else {
            $icon = '';
        }

        if ($model->load($_POST)) {
            $model->icon = $icon;
            $model = Helper::addImageUploads($model, 'AeExtFoodIngredientCategory');
            if ($model->save()) {
                return $this->redirect(Url::previous());
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

}