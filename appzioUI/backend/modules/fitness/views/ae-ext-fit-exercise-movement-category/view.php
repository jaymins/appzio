<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMovementCategory $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Exercise Movement Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Exercise Movement Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-fit-exercise-movement-category-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Exercise Movement Category') ?>
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
            ['create', 'id' => $model->id, 'AeExtFitExerciseMovementCategory'=>$copyParams],
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

    <?php $this->beginBlock('backend\modules\fitness\models\AeExtFitExerciseMovementCategory'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
    // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'exercise_id',
    'value' => ($model->exercise ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-exercise/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->exercise->name, ['/fitness/ae-ext-fit-exercise/view', 'id' => $model->exercise->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitExerciseMovementCategory'=>['exercise_id' => $model->exercise_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'movement_category',
    'value' => ($model->movementCategory ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-movement-category/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->movementCategory->name, ['/fitness/ae-ext-fit-movement-category/view', 'id' => $model->movementCategory->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitExerciseMovementCategory'=>['movement_category' => $model->movement_category]])
        : 
        '<span class="label label-warning">?</span>'),
],
        'timer_type',
        'rounds',
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


    
<?php $this->beginBlock('AeExtFitExerciseMcategoryMovements'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Exercise Mcategory Movements',
            ['/fitness/ae-ext-fit-exercise-mcategory-movement/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Exercise Mcategory Movement',
            ['/fitness/ae-ext-fit-exercise-mcategory-movement/create', 'AeExtFitExerciseMcategoryMovement' => ['exercise_movement_cat_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitExerciseMcategoryMovements', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitExerciseMcategoryMovements ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitExerciseMcategoryMovements(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitexercisemcategorymovements',
        ]
    ]),
    'pager'        => [
        'class'          => yii\widgets\LinkPager::className(),
        'firstPageLabel' => Yii::t('backend', 'First'),
        'lastPageLabel'  => Yii::t('backend', 'Last')
    ],
    'columns' => [
 [
    'class'      => 'yii\grid\ActionColumn',
    'template'   => '{view} {update}',
    'contentOptions' => ['nowrap'=>'nowrap'],
    'urlCreator' => function ($action, $model, $key, $index) {
        // using the column name as key, not mapping to 'id' like the standard generator
        $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
        $params[0] = '/fitness/ae-ext-fit-exercise-mcategory-movement' . '/' . $action;
        $params['AeExtFitExerciseMcategoryMovement'] = ['exercise_movement_cat_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/fitness/ae-ext-fit-exercise-mcategory-movement'
],
        'id',
        'weight',
        'reps',
        'rest',
        'pionts',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'movement_id',
    'value' => function ($model) {
        if ($rel = $model->movement) {
            return Html::a($rel->name, ['/fitness/ae-ext-fit-movement/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
]
])
 . '</div>' 
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


    <?= Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [
 [
    'label'   => '<b class=""># '.Html::encode($model->id).'</b>',
    'content' => $this->blocks['backend\modules\fitness\models\AeExtFitExerciseMovementCategory'],
    'active'  => true,
],
[
    'content' => $this->blocks['AeExtFitExerciseMcategoryMovements'],
    'label'   => '<small>Exercise Mcategory Movements <span class="badge badge-default">'. $model->getAeExtFitExerciseMcategoryMovements()->count() . '</span></small>',
    'active'  => false,
],
 ]
                 ]
    );
    ?>
</div>
