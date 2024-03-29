<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitProgram $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-fit-program-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Program') ?>
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

            <div class="btn-group ">
                <button type="button" class="btn btn-success">
                    <span class="glyphicon glyphicon-plus"></span>
                    New program</button>
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New weekly based program'),
                            ['create','cat'=>'weekly_based']) ?></li>
                    <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New non weekly based program'),
                            ['create','cat'=>'non_weekly_based']) ?></li>
                    <li><?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New food program'),
                            ['create','cat'=>'food']) ?></li>
                </ul>
            </div>
        </div>

        <div class="pull-right">
            <?= Html::a('<span class="glyphicon glyphicon-list"></span> '
            . Yii::t('backend', 'Full list'), ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php $this->beginBlock('backend\modules\fitness\models\AeExtFitProgram'); ?>


    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
    // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'app_id',
    'value' => ($model->app ?
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-game/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->app->name, ['/fitness/ae-game/view', 'id' => $model->app->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitProgram'=>['app_id' => $model->app_id]])
        :
        '<span class="label label-warning">?</span>'),
],
        'name',
        'program_type',
        [
            'class' => yii\grid\DataColumn::className(),
            'attribute' => 'program_sub_type',
            'value' => function ($model) {
                if ($model->program_sub_type == 'weekly_based') {
                    return 'Weekly based';
                } else {
                    return 'Daily based';
                }
            },
            'format' => 'raw',
        ],
        [
            'class' => yii\grid\DataColumn::className(),
            'attribute' => 'is_challenge',
            'value' => function ($model) {
                if ($model->is_challenge) {
                    return 'true';
                } else {
                    return false;
                }
            },
            'format' => 'raw',
        ],

// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'category_id',
    'value' => ($model->category ?
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-program-category/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->category->name, ['/fitness/ae-ext-fit-program-category/view', 'id' => $model->category->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitProgram'=>['category_id' => $model->category_id]])
        :
        '<span class="label label-warning">?</span>'),
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'subcategory_id',
    'value' => ($model->subcategory ?
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-fit-program-subcategory/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->subcategory->name, ['/fitness/ae-ext-fit-program-subcategory/view', 'id' => $model->subcategory->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitProgram'=>['subcategory_id' => $model->subcategory_id]])
        :
        '<span class="label label-warning">?</span>'),
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'article_id',
    'value' => ($model->article ?
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/fitness/ae-ext-article/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->article->title, ['/fitness/ae-ext-article/view', 'id' => $model->article->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtFitProgram'=>['article_id' => $model->article_id]])
        :
        '<span class="label label-warning">?</span>'),
],
        'is_challenge',
        'exercises_per_day',
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



<?php $this->beginBlock('AeExtCalendarEntries'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Calendar Entries',
            ['/fitness/ae-ext-calendar-entry/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Calendar Entry',
            ['/fitness/ae-ext-calendar-entry/create', 'AeExtCalendarEntry' => ['program_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtCalendarEntries', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtCalendarEntries ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtCalendarEntries(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextcalendarentries',
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
        $params[0] = '/fitness/ae-ext-calendar-entry' . '/' . $action;
        $params['AeExtCalendarEntry'] = ['program_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [

    ],
    'controller' => '/fitness/ae-ext-calendar-entry'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'play_id',
    'value' => function ($model) {
        if ($rel = $model->play) {
            return Html::a($rel->id, ['/fitness/ae-game-play/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'type_id',
    'value' => function ($model) {
        if ($rel = $model->type) {
            return Html::a($rel->name, ['/fitness/ae-ext-fit-program-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
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
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'recipe_id',
    'value' => function ($model) {
        if ($rel = $model->recipe) {
            return Html::a($rel->name, ['/fitness/ae-ext-food-recipe/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
        'notes',
        'points',
        'time',
        'completion',
]
])
 . '</div>'
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('AeExtFitProgramExercises'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Program Exercises',
            ['/fitness/ae-ext-fit-program-exercise/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Program Exercise',
            ['/fitness/ae-ext-fit-program-exercise/create', 'AeExtFitProgramExercise' => ['program_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitProgramExercises', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitProgramExercises ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitProgramExercises(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitprogramexercises',
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
        $params[0] = '/fitness/ae-ext-fit-program-exercise' . '/' . $action;
        $params['AeExtFitProgramExercise'] = ['program_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [

    ],
    'controller' => '/fitness/ae-ext-fit-program-exercise'
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
        'week',
        'day',
        'priority',
        'time',
        'repeat_days',
]
])
 . '</div>'
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('AeExtFitProgramRecipes'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Program Recipes',
            ['/fitness/ae-ext-fit-program-recipe/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Program Recipe',
            ['/fitness/ae-ext-fit-program-recipe/create', 'AeExtFitProgramRecipe' => ['program_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtFitProgramRecipes', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtFitProgramRecipes ul.pagination a, th a']) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtFitProgramRecipes(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextfitprogramrecipes',
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
        $params[0] = '/fitness/ae-ext-fit-program-recipe' . '/' . $action;
        $params['AeExtFitProgramRecipe'] = ['program_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [

    ],
    'controller' => '/fitness/ae-ext-fit-program-recipe'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'recipe_id',
    'value' => function ($model) {
        if ($rel = $model->recipe) {
            return Html::a($rel->name, ['/fitness/ae-ext-food-recipe/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
        'week',
        'recipe_order',
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
    'label'   => '<b class="">Program Details</b>',
    'content' => $this->blocks['backend\modules\fitness\models\AeExtFitProgram'],
    'active'  => true,
],
/*[
    'content' => $this->blocks['AeExtCalendarEntries'],
    'label'   => '<small>Calendar Entries <span class="badge badge-default">'. $model->getAeExtCalendarEntries()->count() . '</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeExtFitProgramExercises'],
    'label'   => '<small>Program Exercises <span class="badge badge-default">'. $model->getAeExtFitProgramExercises()->count() . '</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeExtFitProgramRecipes'],
    'label'   => '<small>Program Recipes <span class="badge badge-default">'. $model->getAeExtFitProgramRecipes()->count() . '</span></small>',
    'active'  => false,
],*/
 ]
                 ]
    );
    ?>
</div>
