<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
    * @var backend\modules\tatjack\search\AeGame $searchModel
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
                'url' => ['/tatjack/ae-ext-item/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Item'),
            ],
                                [
                'url' => ['/tatjack/ae-ext-items-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Ext Items Category'),
            ],
                                [
                'url' => ['/tatjack/ae-category/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Ae Category'),
            ],
                                [
                'url' => ['/tatjack/usergroups-user/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-left"></i> ' . Yii::t('backend', 'Usergroups User'),
            ],
                                [
                'url' => ['/tatjack/ae-game-play/index'],
                'label' => '<i class="glyphicon glyphicon-arrow-right"></i> ' . Yii::t('backend', 'Ae Game Play'),
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
			            return Html::a($rel->id, ['/tatjack/usergroups-user/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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
			            return Html::a($rel->name, ['/tatjack/ae-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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
			/*'show_logo',*/
			/*'show_social',*/
			/*'custom_colors',*/
			/*'show_branches',*/
			/*'api_enabled',*/
			/*'keen_api_enabled',*/
			/*'google_api_enabled',*/
			/*'fb_api_enabled',*/
			/*'fb_invite_points',*/
			/*'hide_points',*/
			/*'nickname_variable_id',*/
			/*'show_toplist_points',*/
			/*'show_toplist_entries',*/
			/*'profilepic_variable_id',*/
			/*'notifications_enabled',*/
			/*'cookie_lifetime:datetime',*/
			/*'perm_can_reset',*/
			/*'perm_can_delete',*/
			/*'lang_show',*/
			/*'secondary_points',*/
			/*'tertiary_points',*/
			/*'template',*/
			/*'game_wide',*/
			/*'asset_migration',*/
			/*'background_image_landscape',*/
			/*'background_image_portrait',*/
			/*'logo',*/
			/*'length',*/
			/*'description:ntext',*/
			/*'alert',*/
			/*'custom_css',*/
			/*'shorturl:url',*/
			/*'custom_domain',*/
			/*'home_instructions:ntext',*/
			/*'social_share_url:url',*/
			/*'social_share_description',*/
			/*'social_force_to_canvas_url:url',*/
			/*'app_fb_hash',*/
			/*'colors:ntext',*/
			/*'api_key',*/
			/*'api_secret_key',*/
			/*'api_callback_url:url',*/
			/*'api_application_id',*/
			/*'keen_api_master_key',*/
			/*'keen_api_write_key:ntext',*/
			/*'keen_api_read_key:ntext',*/
			/*'keen_api_config:ntext',*/
			/*'google_api_code',*/
			/*'google_api_config:ntext',*/
			/*'fb_api_id',*/
			/*'fb_api_secret',*/
			/*'game_password',*/
			/*'notification_config:ntext',*/
			/*'secondary_points_title',*/
			/*'tertiary_points_title',*/
			/*'primary_points_shortname',*/
			/*'primary_points_title',*/
			/*'secondary_points_shortname',*/
			/*'tertiary_points_shortname',*/
			/*'thirdparty_api_config:ntext',*/
			/*'auth_config:ntext',*/
			/*'visual_config:ntext',*/
			/*'visual_config_params:ntext',*/
			/*'last_update',*/
			/*'name',*/
			/*'icon',*/
			/*'headboard_portrait',*/
			/*'headboard_landscape',*/
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


