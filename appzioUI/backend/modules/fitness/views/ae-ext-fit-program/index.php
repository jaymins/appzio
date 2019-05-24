<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
    * @var backend\modules\fitness\search\AeExtFitProgram $searchModel
*/

$this->title = Yii::t('backend', 'Programs');
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
$actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'), ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>
<div class="giiant-crud ae-ext-fit-program-index">

    <?php
//             echo $this->render('_search', ['model' =>$searchModel]);
        ?>


    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('backend', 'Programs') ?>
        <small>
            List
        </small>
    </h1>
    <div class="clearfix crud-navigation">
        <div class="btn-group pull-left">
            <button type="button" class="btn btn-success">New program</button>
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
       <!-- <div class="pull-left">
            <?/*= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'), ['create','fitness'], ['class' => 'btn btn-success']) */?>
        </div>-->

        <!--<div class="pull-right">


            <?/*=
            \yii\bootstrap\ButtonDropdown::widget(
            [
            'id' => 'giiant-relations',
            'encodeLabel' => false,
            'label' => '<span class="glyphicon glyphicon-paperclip"></span> ' . Yii::t('backend', 'Relations'),
            'dropdown' => [
            'options' => [
            'class' => 'dropdown-menu-right'
            ],
            'encodeLabels' => false,
            'items' => [
            [
                'url' => ['/fitness/ae-ext-calendar-entry/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Calendar Entry'),
            ],
                                [
                'url' => ['/fitness/ae-game/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Game'),
            ],
                                [
                'url' => ['/fitness/ae-ext-article/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Ext Article'),
            ],
                                [
                'url' => ['/fitness/ae-ext-fit-program-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Program Category'),
            ],
                                [
                'url' => ['/fitness/ae-ext-fit-program-subcategory/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Program Subcategory'),
            ],
                                [
                'url' => ['/fitness/ae-ext-fit-program-exercise/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Program Exercise'),
            ],
                                [
                'url' => ['/fitness/ae-ext-fit-program-recipe/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Program Recipe'),
            ],

]
            ],
            'options' => [
            'class' => 'btn-default'
            ]
            ]
            );
            */?>
        </div>-->
    </div>

    <hr />

    <div class="table-responsive">
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
        'class' => yii\widgets\LinkPager::className(),
        'firstPageLabel' => Yii::t('backend', 'First'),
        'lastPageLabel' => Yii::t('backend', 'Last'),
        ],
                    'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'headerRowOptions' => ['class'=>'x'],
        'columns' => [
                [
            'class' => 'yii\grid\ActionColumn',
            'template' => $actionColumnTemplateString,
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('backend', 'View'),
                        'aria-label' => Yii::t('backend', 'View'),
                        'data-pjax' => '0',
                    ];
                    return Html::a('<span class="glyphicon glyphicon-file"></span>', $url, $options);
                }
            ],
            'urlCreator' => function($action, $model, $key, $index) {
                // using the column name as key, not mapping to 'id' like the standard generator
                $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string) $key];
                $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                return Url::toRoute($params);
            },
            'contentOptions' => ['nowrap'=>'nowrap']
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
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'category_id',
			    'value' => function ($model) {
			        if ($rel = $model->category) {
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
			    'attribute' => 'subcategory_id',
			    'value' => function ($model) {
			        if ($rel = $model->subcategory) {
			            return Html::a($rel->name, ['/fitness/ae-ext-fit-program-subcategory/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'article_id',
			    'value' => function ($model) {
			        if ($rel = $model->article) {
			            return Html::a($rel->title, ['/fitness/ae-ext-article/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
            'is_challenge',
            'exercises_per_day',
        ],
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


