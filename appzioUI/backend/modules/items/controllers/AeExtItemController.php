<?php

namespace backend\modules\items\controllers;

use backend\components\Helper;
use backend\modules\items\models\AeExtItem;
use backend\modules\items\models\AeExtItemsImage;
use backend\modules\items\models\AeExtItemsCategory;
use backend\modules\items\models\AeExtItemsCategoryItem;
use backend\modules\items\search\AeExtItem as AeExtItemSearch;
use dmstr\bootstrap\Tabs;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtItemController".
 */
class AeExtItemController extends \backend\modules\items\controllers\base\AeExtItemController
{

    /**
     * Lists all AeExtItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AeExtItemSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'allowed_fields' => $this->getAllowedFields()
        ]);
    }

    /**
     * Displays a single AeExtItem model.
     *
     * @param string $id
     * @return mixed
     * @throws
     */
    public function actionView($id)
    {
        \Yii::$app->session['__crudReturnUrl'] = Url::previous();
        Url::remember();
        Tabs::rememberActiveState();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'allowed_fields' => $this->getAllowedFields()
        ]);
    }

    /**
     * Creates a new AeExtItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtItem;

        try {
            if ($model->load($_POST) && $model->save()) {

                AeExtItem::addUploadedImages(new AeExtItemsImage, $model->id);

                if ($categories = Yii::$app->request->post('categories-group')) {
                    AeExtItemsCategoryItem::addOrUpdateRelations($categories, $model->id, false);
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
            'images_json' => '',
            'categories' => AeExtItemsCategory::getAllCategories(Yii::$app->session['app_id']),
            'categories_json' => '',
            'allowed_fields' => $this->getAllowedFields()
        ]);
    }

    /**
     * Updates an existing AeExtItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     * @return mixed
     * @throws
     */
    public function actionUpdate($id)
    {
        $item = new AeExtItem();

        $model = $item::find()
            ->joinWith([
                'aeExtItemsImages',
                'aeExtItemsCategoryItems'
            ], true, 'LEFT OUTER JOIN')
            ->where([
                'ae_ext_items.id' => $id
            ])->one();

        if (Yii::$app->request->isPost AND $model->load($_POST)) {
            AeExtItem::addOrUpdateUploadedImages(new AeExtItemsImage, $model);

            if ($categories = Yii::$app->request->post('categories-group')) {
                AeExtItemsCategoryItem::addOrUpdateRelations($categories, $model->id, true);
            }
        }

        if ($model->load($_POST) && $model->save()) {
            return $this->redirect(Url::previous());
        } else {
            return $this->render('update', [
                'model' => $model,
                'images_json' => $this->getFormattedImages($model->aeExtItemsImages),
                'categories' => AeExtItemsCategory::getAllCategories(Yii::$app->session['app_id']),
                'categories_json' => $this->getFormattedCategories($model->aeExtItemsCategoryItems),
                'allowed_fields' => $this->getAllowedFields()
            ]);
        }
    }

    private function getFormattedCategories(array $relations)
    {

        if (empty($relations)) {
            return [];
        }

        $output = [];

        foreach ($relations as $i => $relation) {
            $output[$i]['field-select-category'] = $relation->category_id;
            $output[$i]['field-input-id'] = $relation->id;
        }

        return json_encode($output);
    }

    private function getFormattedImages(array $images)
    {

        if (empty($images)) {
            return [];
        }

        $output = [];

        foreach ($images as $i => $image) {
            $output[$i]['field-image-id'] = $image->id;
            $output[$i]['field-image-name'] = $image->image;
            $output[$i]['field-image-link'] = Helper::getUploadURL() . $image->image;
        }

        return json_encode($output);
    }

    private function getAllowedFields()
    {
        return [
            'game_id',
            // 'date_added',
            'type' => [
                'field' => 'select',
                'options' => [
                    'event'
                ]
            ],
            'name',
            'description',
            'time',
            'status',
            'lat',
            'lon',
            /*'extra_data' => [
                'label' => 'Event Address'
            ],*/
        ];
    }

}