<?php

use backend\components\Helper;
use dmstr\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var backend\modules\items\models\AeExtItem $model
 * @var $allowed_fields
 */
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Item');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-item-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Item') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-pencil"></span> ' . Yii::t('backend', 'Edit'),
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-info']) ?>

            <?= Html::a(
                '<span class="glyphicon glyphicon-copy"></span> ' . Yii::t('backend', 'Copy'),
                ['create', 'id' => $model->id, 'AeExtItem' => $copyParams],
                ['class' => 'btn btn-success']) ?>

            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New'),
                ['create'],
                ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?= Html::a('<span class="glyphicon glyphicon-list"></span> '
                . Yii::t('backend', 'Full list'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php $this->beginBlock('backend\modules\items\models\AeExtItem'); ?>

    <?php

    $view_fields = [
        [
            'format' => 'html',
            'attribute' => 'play_id',
            'value' => ($model->play ?
                Html::a('<i class="glyphicon glyphicon-list"></i>', ['/items/ae-game-play/index']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> ' . $model->play->id, ['/items/ae-game-play/view', 'id' => $model->play->id,]) . ' ' .
                Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtItem' => ['play_id' => $model->play_id]])
                :
                '<span class="label label-warning">?</span>'),
        ],
        [
            'format' => 'html',
            'attribute' => 'game_id',
            'value' => ($model->game ?
                Html::a('<i class="glyphicon glyphicon-list"></i>', ['/items/ae-game/index']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> ' . $model->game->name, ['/items/ae-game/view', 'id' => $model->game->id,]) . ' ' .
                Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtItem' => ['game_id' => $model->game_id]])
                :
                '<span class="label label-warning">?</span>'),
        ],
        [
            'format' => 'html',
            'attribute' => 'category_id',
            'value' => ($model->category ?
                Html::a('<i class="glyphicon glyphicon-list"></i>', ['/items/ae-ext-items-category/index']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> ' . $model->category->name, ['/items/ae-ext-items-category/view', 'id' => $model->category->id,]) . ' ' .
                Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeExtItem' => ['category_id' => $model->category_id]])
                :
                '<span class="label label-warning">?</span>'),
        ],
       // 'place_id',
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
    ];
    
    ?>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => Helper::adjustWidgetFields($view_fields, $allowed_fields),
    ]); ?>


    <hr/>

    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id],
        [
            'class' => 'btn btn-danger',
            'data-confirm' => '' . Yii::t('backend', 'Are you sure to delete this item?') . '',
            'data-method' => 'post',
        ]); ?>
    <?php $this->endBlock(); ?>



    <?php $this->beginBlock('AeExtItemsCategoryItems'); ?>
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Items Category Items',
                ['/items/ae-ext-items-category-item/index'],
                ['class' => 'btn text-muted btn-xs']
            ) ?>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Items Category Item',
                ['/items/ae-ext-items-category-item/create', 'AeExtItemsCategoryItem' => ['item_id' => $model->id]],
                ['class' => 'btn btn-success btn-xs']
            ); ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'pjax-AeExtItemsCategoryItems', 'enableReplaceState' => false, 'linkSelector' => '#pjax-AeExtItemsCategoryItems ul.pagination a, th a']) ?>
    <?=
    '<div class="table-responsive">'
    . \yii\grid\GridView::widget([
        'layout' => '{summary}{pager}<br/>{items}{pager}',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAeExtItemsCategoryItems(),
            'pagination' => [
                'pageSize' => 20,
                'pageParam' => 'page-aeextitemscategoryitems',
            ]
        ]),
        'pager' => [
            'class' => yii\widgets\LinkPager::className(),
            'firstPageLabel' => Yii::t('backend', 'First'),
            'lastPageLabel' => Yii::t('backend', 'Last')
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'contentOptions' => ['nowrap' => 'nowrap'],
                'urlCreator' => function ($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
                    $params[0] = '/items/ae-ext-items-category-item' . '/' . $action;
                    $params['AeExtItemsCategoryItem'] = ['item_id' => $model->primaryKey()[0]];
                    return $params;
                },
                'buttons' => [

                ],
                'controller' => '/items/ae-ext-items-category-item'
            ],
            'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
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
            'description:ntext',
        ]
    ])
    . '</div>'
    ?>
    <?php Pjax::end() ?>
    <?php $this->endBlock() ?>


    <?php $this->beginBlock('AeExtItemsImages'); ?>
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Items Images',
                ['/items/ae-ext-items-image/index'],
                ['class' => 'btn text-muted btn-xs']
            ) ?>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Items Image',
                ['/items/ae-ext-items-image/create', 'AeExtItemsImage' => ['item_id' => $model->id]],
                ['class' => 'btn btn-success btn-xs']
            ); ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'pjax-AeExtItemsImages', 'enableReplaceState' => false, 'linkSelector' => '#pjax-AeExtItemsImages ul.pagination a, th a']) ?>
    <?=
    '<div class="table-responsive">'
    . \yii\grid\GridView::widget([
        'layout' => '{summary}{pager}<br/>{items}{pager}',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAeExtItemsImages(),
            'pagination' => [
                'pageSize' => 20,
                'pageParam' => 'page-aeextitemsimages',
            ]
        ]),
        'pager' => [
            'class' => yii\widgets\LinkPager::className(),
            'firstPageLabel' => Yii::t('backend', 'First'),
            'lastPageLabel' => Yii::t('backend', 'Last')
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'contentOptions' => ['nowrap' => 'nowrap'],
                'urlCreator' => function ($action, $model, $key, $index) {
                    // using the column name as key, not mapping to 'id' like the standard generator
                    $params = is_array($key) ? $key : [$model->primaryKey()[0] => (string)$key];
                    $params[0] = '/items/ae-ext-items-image' . '/' . $action;
                    $params['AeExtItemsImage'] = ['item_id' => $model->primaryKey()[0]];
                    return $params;
                },
                'buttons' => [

                ],
                'controller' => '/items/ae-ext-items-image'
            ],
            'id',
            'date',
            'image',
            'featured',
            'image_order',
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
                    'label' => '<b class=""># ' . Html::encode($model->id) . '</b>',
                    'content' => $this->blocks['backend\modules\items\models\AeExtItem'],
                    'active' => true,
                ],
                [
                    'content' => $this->blocks['AeExtItemsCategoryItems'],
                    'label' => '<small>Ae Ext Items Category Items <span class="badge badge-default">' . $model->getAeExtItemsCategoryItems()->count() . '</span></small>',
                    'active' => false,
                ],
                [
                    'content' => $this->blocks['AeExtItemsImages'],
                    'label' => '<small>Ae Ext Items Images <span class="badge badge-default">' . $model->getAeExtItemsImages()->count() . '</span></small>',
                    'active' => false,
                ],
            ]
        ]
    );
    ?>
</div>
