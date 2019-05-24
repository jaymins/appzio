<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitComponent $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Ae Ext Fit Component');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Fit Components'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-fit-component-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Ae Ext Fit Component') ?>
        <small>
            <?= Html::encode($model->name) ?>
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
            ['create', 'id' => $model->id, 'AeExtFitComponent'=>$copyParams],
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

    <?php $this->beginBlock('backend\modules\fitness\models\AeExtFitComponent'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
            'name',
        'admin_name',
        'rounds',
        'timer',
        'background_image',
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


    
<?php $this->beginBlock('AeExtFitComponentMovements'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Fit Component Movements',
            ['/fitness/ae-ext-fit-component-movement/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Fit Component Movement',
            ['/fitness/ae-ext-fit-component-movement/create', 'AeExtFitComponentMovement' => ['component_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitComponentMovements', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitComponentMovements ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitComponentMovements(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitcomponentmovements',
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
        $params[0] = '/fitness/ae-ext-fit-component-movement' . '/' . $action;
        $params['AeExtFitComponentMovement'] = ['component_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/fitness/ae-ext-fit-component-movement'
],
        'id',
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
        'weight',
        'unit',
        'reps',
        'movement_time:datetime',
        'points',
]
])
 . '</div>' 
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('AeExtFitExerciseComponents'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Fit Exercise Components',
            ['/fitness/ae-ext-fit-exercise-component/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Fit Exercise Component',
            ['/fitness/ae-ext-fit-exercise-component/create', 'AeExtFitExerciseComponent' => ['component_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitExerciseComponents', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitExerciseComponents ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitExerciseComponents(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitexercisecomponents',
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
        $params[0] = '/fitness/ae-ext-fit-exercise-component' . '/' . $action;
        $params['AeExtFitExerciseComponent'] = ['component_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/fitness/ae-ext-fit-exercise-component'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'exercise_id',
    'value' => function ($model) {
        if ($rel = $model->exercise) {
            return Html::a($rel->name, ['/fitness/ae-ext-fit-exercise/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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


<?php $this->beginBlock('AeExtFitExerciseComponents0s'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Fit Exercise Components0s',
            ['/fitness/ae-ext-fit-exercise-component/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Fit Exercise Components0',
            ['/fitness/ae-ext-fit-exercise-component/create', 'AeExtFitExerciseComponent' => ['component_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitExerciseComponents0s', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitExerciseComponents0s ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitExerciseComponents(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitexercisecomponents0s',
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
        $params[0] = '/fitness/ae-ext-fit-exercise-component' . '/' . $action;
        $params['AeExtFitExerciseComponent'] = ['component_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/fitness/ae-ext-fit-exercise-component'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'exercise_id',
    'value' => function ($model) {
        if ($rel = $model->exercise) {
            return Html::a($rel->name, ['/fitness/ae-ext-fit-exercise/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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
    'content' => $this->blocks['backend\modules\fitness\models\AeExtFitComponent'],
    'active'  => true,
],
[
    'content' => $this->blocks['AeExtFitComponentMovements'],
    'label'   => '<small>Ae Ext Fit Component Movements <span class="badge badge-default">'. $model->getAeExtFitComponentMovements()->count() . '</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeExtFitExerciseComponents'],
    'label'   => '<small>Ae Ext Fit Exercise Components <span class="badge badge-default">'. $model->getAeExtFitExerciseComponents()->count() . '</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeExtFitExerciseComponents0s'],
    'label'   => '<small>Ae Ext Fit Exercise Components0s <span class="badge badge-default">'. $model->getAeExtFitExerciseComponents()->count() . '</span></small>',
    'active'  => false,
],
 ]
                 ]
    );
    ?>
</div>
