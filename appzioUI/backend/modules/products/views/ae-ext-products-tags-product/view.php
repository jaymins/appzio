<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\products\models\AeExtProductsTagsProduct $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Products Tags Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Products Tags Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->tag_id, 'url' => ['view', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-products-tags-product-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Products Tags Product') ?>
        <small>
            <?= $model->tag_id ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('backend', 'Edit'),
            [ 'update', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id],
            ['class' => 'btn btn-info']) ?>

            <?= Html::a(
            '<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('backend', 'Copy'),
            ['create', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id, 'AeExtProductsTagsProduct'=>$copyParams],
            ['class' => 'btn btn-success']) ?>

            <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'),
            ['create'],
            ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?= Html::a('<span class="glyphicon glyphicon-list"></span> '
            . Yii::t('backend', 'Full list'), ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr />

    <?php $this->beginBlock('backend\modules\products\models\AeExtProductsTagsProduct'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
    // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'tag_id',
    'value' => ($model->getTag()->one() ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/products/ae-ext-products-tag/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->getTag()->one()->title, ['/products/ae-ext-products-tag/view', 'id' => $model->getTag()->one()->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtProductsTagsProduct'=>['tag_id' => $model->tag_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'product_id',
    'value' => ($model->getProduct()->one() ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/products/ae-ext-product/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->getProduct()->one()->title, ['/products/ae-ext-product/view', 'id' => $model->getProduct()->one()->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtProductsTagsProduct'=>['product_id' => $model->product_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
    ],
    ]); ?>

    
    <hr/>

    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('backend', 'Delete'), ['delete', 'tag_id' => $model->tag_id, 'product_id' => $model->product_id],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => '' . Yii::t('backend', 'Are you sure to delete this item?') . '',
    'data-method' => 'post',
    ]); ?>
    <?php $this->endBlock(); ?>


    
    <?= Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [
 [
    'label'   => '<b class=""># '.$model->tag_id.'</b>',
    'content' => $this->blocks['backend\modules\products\models\AeExtProductsTagsProduct'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
