<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\items\models\AeExtItem $model
 * @var $allowed_fields
 * @var $images_json
 * @var $categories
 * @var $categories_json
 */

$this->title = Yii::t('backend', 'Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-item-create">

    <h1>
        <?= Yii::t('backend', 'Item') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a(
                Yii::t('backend', 'Cancel'),
                \yii\helpers\Url::previous(),
                ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'images_json' => $images_json,
        'categories' => $categories,
        'categories_json' => $categories_json,
        'allowed_fields' => $allowed_fields,
    ]); ?>

</div>