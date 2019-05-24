<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
    * @var backend\modules\products\search\AeExtProduct $searchModel
*/

$this->title = Yii::t('backend', 'Products');
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
<div class="giiant-crud ae-ext-product-index">

    <?php
//             echo $this->render('_search', ['model' =>$searchModel]);
        ?>

    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('backend', 'Products') ?>
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
                'url' => ['/products/ae-game-play/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Game Play'),
            ],
                                [
                'url' => ['/products/ae-game/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Game'),
            ],
                                [
                'url' => ['/products/ae-ext-products-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Products Category'),
            ],
                                [
                'url' => ['/products/ae-ext-products-bookmark/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Bookmark'),
            ],
                                [
                'url' => ['/products/ae-ext-products-cart/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Cart'),
            ],
                                [
                'url' => ['/products/ae-ext-products-photo/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Photo'),
            ],
                                [
                'url' => ['/products/ae-ext-products-tags-product/index'],
                'label' => '<i class="glyphicon glyphicon-random text-muted"></i> ' . Yii::t('backend', 'Products Tags Product'),
            ],
                                [
                'url' => ['/products/ae-ext-products-tag/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Tag'),
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
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
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
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'app_id',
			    'value' => function ($model) {
			        if ($rel = $model->getApp()->one()) {
			            return Html::a($rel->name, ['/products/ae-game/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			'amazon_product_id',
			[
                'label' => 'Product Photo',
                'format' => 'html',
                'value' => function($model) { return Html::img('https://earnster.appzio.com/documents/games/364/user_original_images/' . $model->photo, ['width'=>'100']); },
            ],
			'title',
			'header:ntext',
			'description:ntext',
			/*'link:ntext',*/
			/*'rating',*/
			/*'price',*/
			/*'points_value',*/
	        /*'sorting',*/
			/*// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'category_id',
			    'value' => function ($model) {
			        if ($rel = $model->getCategory()->one()) {
			            return Html::a($rel->title, ['/products/ae-ext-products-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],*/
			/*// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'play_id',
			    'value' => function ($model) {
			        if ($rel = $model->getPlay()->one()) {
			            return Html::a($rel->id, ['/products/ae-game-play/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],*/
			/*'featured',*/
        ],
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


