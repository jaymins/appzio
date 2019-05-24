<?php

namespace backend\modules\fitness\controllers;

use backend\modules\fitness\models\AeExtFitMovement;

/**
 * This is the class for controller "AeExtFitMovementController".
 */
class AeExtFitMovementController extends \backend\modules\fitness\controllers\base\AeExtFitMovementController
{

    /**
     * Creates a new AeExtFitMovement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtFitMovement;

        $session = \Yii::$app->session;

        if (isset($session['app_id'])) {
            $model->app_id = $session['app_id'];
        }

        try {
            if ($model->load($_POST) && $model->save()) {
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

}