<?php

use dmstr\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\models\AeExtTicker $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Ticker');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Tickers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-ticker-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Ticker') ?>
        <small>
            <?= $model->id ?>
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
                ['create', 'id' => $model->id, 'AeExtTicker' => $copyParams],
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

    <br />

    <div>
        <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ticker Trade',
            ['/tickers/ae-ext-ticker-trade/create', 'AeExtTickerTrade' => ['ticker_id' => $model->id]],
            ['class' => 'btn btn-success btn-xs']
        ); ?>
    </div>

    <br />

    <?php $this->beginBlock('backend\modules\tickers\models\AeExtTicker'); ?>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ticker',
            'company',
            'exchange',
            'exchange_name',
            'currency',
            'ticker_date:datetime',
            /*'log:ntext',*/
            [
                'label' => 'Overall',
                'value' => ($model->overall ? 'Yes' : 'No'),
            ],
            'last_update:datetime',
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



    <?php $this->beginBlock('AeExtTickerDailies'); ?>
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ticker Daily Data',
                ['/tickers/ae-ext-ticker-daily/index'],
                ['class' => 'btn text-muted btn-xs']
            ) ?>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ticker Daily',
                ['/tickers/ae-ext-ticker-daily/create', 'Data' => ['ticker_id' => $model->id]],
                ['class' => 'btn btn-success btn-xs']
            ); ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'pjax-AeExtTickerDailies', 'enableReplaceState' => false, 'linkSelector' => '#pjax-AeExtTickerDailies ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>
    <?=
    '<div class="table-responsive">'
    . \yii\grid\GridView::widget([
        'layout' => '{summary}{pager}<br/>{items}{pager}',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAeExtTickerDailies(),
            'pagination' => [
                'pageSize' => 20,
                'pageParam' => 'page-aeexttickerdailies',
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
                    $params[0] = '/tickers/ae-ext-ticker-daily' . '/' . $action;
                    $params['AeExtTickerDaily'] = ['ticker_id' => $model->primaryKey()[0]];
                    return $params;
                },
                'buttons' => [

                ],
                'controller' => '/tickers/ae-ext-ticker-daily'
            ],
            'id',
            'ticker',
            'timestamp:datetime',
            'open',
            'high',
            'low',
            'value',
            'volume',
            'previousclose',
        ]
    ])
    . '</div>'
    ?>
    <?php Pjax::end() ?>
    <?php $this->endBlock() ?>


    <?php $this->beginBlock('AeExtTickerDatas'); ?>
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ticker Data',
                ['/tickers/ae-ext-ticker-datum/index'],
                ['class' => 'btn text-muted btn-xs']
            ) ?>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ticker Data',
                ['/tickers/ae-ext-ticker-datum/create', 'AeExtTickerDatum' => ['ticker_id' => $model->id]],
                ['class' => 'btn btn-success btn-xs']
            ); ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'pjax-AeExtTickerDatas', 'enableReplaceState' => false, 'linkSelector' => '#pjax-AeExtTickerDatas ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>
    <?=
    '<div class="table-responsive">'
    . \yii\grid\GridView::widget([
        'layout' => '{summary}{pager}<br/>{items}{pager}',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAeExtTickerData(),
            'pagination' => [
                'pageSize' => 20,
                'pageParam' => 'page-aeexttickerdatas',
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
                    $params[0] = '/tickers/ae-ext-ticker-datum' . '/' . $action;
                    $params['AeExtTickerDatum'] = ['ticker_id' => $model->primaryKey()[0]];
                    return $params;
                },
                'buttons' => [

                ],
                'controller' => '/tickers/ae-ext-ticker-datum'
            ],
            'id',
            'date',
            'open',
            'hight',
            'low',
            'close',
            'volume',
            'update_date',
        ]
    ])
    . '</div>'
    ?>
    <?php Pjax::end() ?>
    <?php $this->endBlock() ?>

    <?php $this->beginBlock('AeExtTickerTrades'); ?>
    <div style='position: relative'>
        <div style='position:absolute; right: 0px; top: 0px;'>
            <?= Html::a(
                '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ticker Trades',
                ['/tickers/ae-ext-ticker-trade/index'],
                ['class' => 'btn text-muted btn-xs']
            ) ?>
            <?= Html::a(
                '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ticker Trade',
                ['/tickers/ae-ext-ticker-trade/create', 'AeExtTickerTrade' => ['ticker_id' => $model->id]],
                ['class' => 'btn btn-success btn-xs']
            ); ?>
        </div>
    </div>
    <?php Pjax::begin(['id' => 'pjax-AeExtTickerTrades', 'enableReplaceState' => false, 'linkSelector' => '#pjax-AeExtTickerTrades ul.pagination a, th a', 'clientOptions' => ['pjax:success' => 'function(){alert("yo")}']]) ?>
    <?=
    '<div class="table-responsive">'
    . \yii\grid\GridView::widget([
        'layout' => '{summary}{pager}<br/>{items}{pager}',
        'dataProvider' => new \yii\data\ActiveDataProvider([
            'query' => $model->getAeExtTickerTrades(),
            'pagination' => [
                'pageSize' => 20,
                'pageParam' => 'page-aeexttickertrades',
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
                    $params[0] = '/tickers/ae-ext-ticker-trade' . '/' . $action;
                    $params['AeExtTickerTrade'] = ['ticker_id' => $model->primaryKey()[0]];
                    return $params;
                },
                'buttons' => [

                ],
                'controller' => '/tickers/ae-ext-ticker-trade'
            ],
            'id',
            'buy_range_from',
            'buy_range_to',
            'sell_range_from',
            'sell_range_to',
            'term',
            'trade_notes:ntext',
            'trade_date:datetime',
            'active',
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
                    'label' => '<b class=""># ' . $model->id . '</b>',
                    'content' => $this->blocks['backend\modules\tickers\models\AeExtTicker'],
                    'active' => true,
                ],
                [
                    'content' => $this->blocks['AeExtTickerTrades'],
                    'label' => '<small>Ticker Trades <span class="badge badge-default">' . count($model->getAeExtTickerTrades()->asArray()->all()) . '</span></small>',
                    'active' => false,
                ],
                [
                    'content' => $this->blocks['AeExtTickerDailies'],
                    'label' => '<small>Ticker Daily Data <span class="badge badge-default">' . count($model->getAeExtTickerDailies()->asArray()->all()) . '</span></small>',
                    'active' => false,
                ],
                [
                    'content' => $this->blocks['AeExtTickerDatas'],
                    'label' => '<small>Ticker Data <span class="badge badge-default">' . count($model->getAeExtTickerData()->asArray()->all()) . '</span></small>',
                    'active' => false,
                ],
            ]
        ]
    );
    ?>
</div>
