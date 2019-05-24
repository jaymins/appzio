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
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Item'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-item-update">

    <h1>
        <?= Yii::t('backend', 'Item') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form', [
        'model' => $model,
        'images_json' => $images_json,
        'categories' => $categories,
        'categories_json' => $categories_json,
        'allowed_fields' => $allowed_fields,
    ]); ?>

</div>