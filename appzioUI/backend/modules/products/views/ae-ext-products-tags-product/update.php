<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\products\models\AeExtProductsTagsProduct $model
*/
    
$this->title = Yii::t('backend', 'Products Tags Product') . " " . $model->tag_id . ', ' . Yii::t('backend', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Products Tags Product'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->tag_id, 'url' => ['view', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-products-tags-product-update">

    <h1>
        <?= Yii::t('backend', 'Products Tags Product') ?>
        <small>
                        <?= $model->tag_id ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
