<?php

use backend\components\Helper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\items\search\AeExtItem $searchModel
 * @var $allowed_fields
 */

$this->title = Yii::t('backend', 'Items');
$this->params['breadcrumbs'][] = $this->title;

if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
    Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'), ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">' . $actionColumnTemplateString . '</div>';
?>
    <div class="giiant-crud ae-ext-item-index">

        <?php
        //             echo $this->render('_search', ['model' =>$searchModel]);
        ?>

        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-main', 'enableReplaceState' => false, 'linkSelector' => '#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>

        <h1>
            <?= Yii::t('backend', 'Items') ?>
            <small>
                List
            </small>
        </h1>
        <div class="clearfix crud-navigation">
            <div class="pull-left">
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <div class="pull-right">

                <?=
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
                                    'url' => ['/items/ae-game-play/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Game Play'),
                                ],
                                [
                                    'url' => ['/items/ae-game/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Game'),
                                ],
                                [
                                    'url' => ['/items/ae-ext-items-category/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Ext Items Category'),
                                ],
                                [
                                    'url' => ['/items/ae-ext-items-category/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Ext Items Category'),
                                ],
                                [
                                    'url' => ['/items/ae-ext-items-category-item/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Items Category Item'),
                                ],
                                [
                                    'url' => ['/items/ae-ext-items-image/index'],
                                    'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Items Image'),
                                ],

                            ]
                        ],
                        'options' => [
                            'class' => 'btn-default'
                        ]
                    ]
                );
                ?>
            </div>
        </div>

        <hr/>

        <?php

        $row_controls = [
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
            'urlCreator' => function ($action, $model, $key, $index) {
                // using the column name as key, not mapping to 'id' like the standard generator
                $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
                $params[0] = \Yii::$app->controller->id ? \Yii::$app->controller->id . '/' . $action : $action;
                return Url::toRoute($params);
            },
            'contentOptions' => ['nowrap' => 'nowrap']
        ];

        $view_fields = Helper::adjustWidgetFields([
            [
                'class' => yii\grid\DataColumn::className(),
                'attribute' => 'play_id',
                'value' => function ($model) {
                    if ($rel = $model->play) {
                        return Html::a($rel->id, ['/items/ae-game-play/view', 'id' => $rel->id,], ['data-pjax' => 0]);
                    } else {
                        return '';
                    }
                },
                'format' => 'raw',
            ],
            [
                'class' => yii\grid\DataColumn::className(),
                'attribute' => 'game_id',
                'value' => function ($model) {
                    if ($rel = $model->game) {
                        return Html::a($rel->name, ['/items/ae-game/view', 'id' => $rel->id,], ['data-pjax' => 0]);
                    } else {
                        return '';
                    }
                },
                'format' => 'raw',
            ],
            [
                'class' => yii\grid\DataColumn::className(),
                'attribute' => 'category_id',
                'value' => function ($model) {
                    if ($rel = $model->category) {
                        return Html::a($rel->name, ['/items/ae-ext-items-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
                    } else {
                        return '';
                    }
                },
                'format' => 'raw',
            ],
            //'place_id',
            'date_added',
            'featured',
            'external',
            'buyer_play_id',
            'importa_date',
            'type',
            'name',
            'price',
            'lat',
            'lon',
            'images:ntext',
            'extra_data:ntext',
            'description',
            'time',
            'status',
            'city',
            'country',
            'source',
            'external_id',
            'slug',
        ], $allowed_fields);

        array_unshift($view_fields, $row_controls);

        ?>

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
                'headerRowOptions' => ['class' => 'x'],
                'columns' =>  $view_fields,
            ]); ?>
        </div>

    </div>

<?php \yii\widgets\Pjax::end() ?>