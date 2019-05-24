<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Exercise Mcategory Movement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Exercise Mcategory Movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-fit-exercise-mcategory-movement-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Exercise Mcategory Movement') ?>
        <small>
            <?= Html::encode($model->id) ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('backend', 'Edit'),
            [ 'update', 'id' => $model->id],
            ['class' => 'btn btn-info']) ?>

            <?= Html::a(
            '<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('backend', 'Copy'),
            ['create', 'id' => $model->id, 'AeExtFitExerciseMcategoryMovement'=>$copyParams],
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

    <hr/>

    <?php $this->beginBlock('backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
    // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'exercise_movement_cat_id',
    'value' => ($model->exerciseMovementCat ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-exercise-movement-category/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->exerciseMovementCat->id, ['/fitness/ae-ext-fit-exercise-movement-category/view', 'id' => $model->exerciseMovementCat->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitExerciseMcategoryMovement'=>['exercise_movement_cat_id' => $model->exercise_movement_cat_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
        'rest',
        'pionts',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'movement_id',
    'value' => ($model->movement ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-movement/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->movement->name, ['/fitness/ae-ext-fit-movement/view', 'id' => $model->movement->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitExerciseMcategoryMovement'=>['movement_id' => $model->movement_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
        'weight',
        'reps',
    ],
    ]); ?>

    
    <hr/>

    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id],
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
    'label'   => '<b class=""># '.Html::encode($model->id).'</b>',
    'content' => $this->blocks['backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
