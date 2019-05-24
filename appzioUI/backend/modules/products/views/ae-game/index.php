<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
    * @var backend\modules\products\search\AeGame $searchModel
*/

$this->title = Yii::t('backend', 'Ae Games');
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
<div class="giiant-crud ae-game-index">

    <?php
//             echo $this->render('_search', ['model' =>$searchModel]);
        ?>

    
    <?php \yii\widgets\Pjax::begin(['id'=>'pjax-main', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-main ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>

    <h1>
        <?= Yii::t('backend', 'Ae Games') ?>
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
                'url' => ['/products/ae-ext-product/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Product'),
            ],
                                [
                'url' => ['/products/ae-ext-products-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Category'),
            ],
                                [
                'url' => ['/products/ae-ext-products-tag/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Products Tag'),
            ],
                                [
                'url' => ['/products/ae-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Category'),
            ],
                                [
                'url' => ['/products/usergroups-user/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Usergroups User'),
            ],
                                [
                'url' => ['/products/ae-game-play/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Game Play'),
            ],
                                [
                'url' => ['/products/ae-game-role/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Game Role'),
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
			    'attribute' => 'user_id',
			    'value' => function ($model) {
			        if ($rel = $model->getUser()->one()) {
			            return Html::a($rel->id, ['/products/usergroups-user/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
			[
			    'class' => yii\grid\DataColumn::className(),
			    'attribute' => 'category_id',
			    'value' => function ($model) {
			        if ($rel = $model->getCategory()->one()) {
			            return Html::a($rel->name, ['/products/ae-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
			        } else {
			            return '';
			        }
			    },
			    'format' => 'raw',
			],
			'active',
			'timelimit:datetime',
			'levels',
			'featured',
			'max_actions',
			/*'show_toplist',*/
			/*'register_email:email',*/
			/*'register_sms',*/
			/*'start_without_registration',*/
			/*'show_homepage',*/
			/*'choose_playername',*/
			/*'choose_avatar',*/
			/*'notifyme',*/
			/*'api_enabled',*/
			/*'keen_api_enabled',*/
			/*'google_api_enabled',*/
			/*'fb_api_enabled',*/
			/*'api_application_id',*/
			/*'fb_invite_points',*/
			/*'hide_points',*/
			/*'nickname_variable_id',*/
			/*'show_toplist_points',*/
			/*'show_toplist_entries',*/
			/*'profilepic_variable_id',*/
			/*'show_branches',*/
			/*'notifications_enabled',*/
			/*'cookie_lifetime:datetime',*/
			/*'perm_can_reset',*/
			/*'perm_can_delete',*/
			/*'lang_show',*/
			/*'secondary_points',*/
			/*'tertiary_points',*/
			/*'template',*/
			/*'show_logo',*/
			/*'show_social',*/
			/*'custom_colors',*/
			/*'game_wide',*/
			/*'zip_period',*/
			/*'asset_migration',*/
			/*'length',*/
			/*'description:ntext',*/
			/*'alert',*/
			/*'custom_css',*/
			/*'home_instructions:ntext',*/
			/*'shorturl:url',*/
			/*'custom_domain',*/
			/*'api_key',*/
			/*'api_secret_key',*/
			/*'api_callback_url:url',*/
			/*'keen_api_master_key',*/
			/*'keen_api_write_key:ntext',*/
			/*'keen_api_read_key:ntext',*/
			/*'keen_api_config:ntext',*/
			/*'google_api_code',*/
			/*'google_api_config:ntext',*/
			/*'fb_api_id',*/
			/*'fb_api_secret',*/
			/*'app_fb_hash',*/
			/*'game_password',*/
			/*'notification_config:ntext',*/
			/*'secondary_points_title',*/
			/*'tertiary_points_title',*/
			/*'primary_points_title',*/
			/*'primary_points_shortname',*/
			/*'secondary_points_shortname',*/
			/*'tertiary_points_shortname',*/
			/*'social_share_url:url',*/
			/*'social_share_description',*/
			/*'social_force_to_canvas_url:url',*/
			/*'colors:ntext',*/
			/*'thirdparty_api_config:ntext',*/
			/*'auth_config:ntext',*/
			/*'visual_config:ntext',*/
			/*'visual_config_params:ntext',*/
			/*'headboard_portrait',*/
			/*'headboard_landscape',*/
			/*'background_image_landscape',*/
			/*'background_image_portrait',*/
			/*'logo',*/
			/*'last_update',*/
			/*'name',*/
			/*'icon',*/
			/*'skin',*/
			/*'icon_primary_points',*/
			/*'icon_secondary_points',*/
			/*'icon_tertiary_points',*/
			/*'lang_default',*/
        ],
        ]); ?>
    </div>

</div>


<?php \yii\widgets\Pjax::end() ?>


