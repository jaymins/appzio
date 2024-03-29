<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeGame $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Ae Game');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Games'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-game-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Ae Game') ?>
        <small>
            <?= $model->name ?>
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
            ['create', 'id' => $model->id, 'AeGame'=>$copyParams],
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

    <hr />

    <?php $this->beginBlock('backend\modules\tatjack\models\AeGame'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
    // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'user_id',
    'value' => ($model->getUser()->one() ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/tatjack/usergroups-user/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->getUser()->one()->id, ['/tatjack/usergroups-user/view', 'id' => $model->getUser()->one()->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeGame'=>['user_id' => $model->user_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
[
    'format' => 'html',
    'attribute' => 'category_id',
    'value' => ($model->getCategory()->one() ? 
        Html::a('<i class="glyphicon glyphicon-list"></i>', ['/tatjack/ae-category/index']).' '.
        Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> '.$model->getCategory()->one()->name, ['/tatjack/ae-category/view', 'id' => $model->getCategory()->one()->id,]).' '.
        Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'AeGame'=>['category_id' => $model->category_id]])
        : 
        '<span class="label label-warning">?</span>'),
],
        'active',
        'timelimit:datetime',
        'levels',
        'featured',
        'max_actions',
        'show_toplist',
        'register_email:email',
        'register_sms',
        'start_without_registration',
        'show_homepage',
        'choose_playername',
        'choose_avatar',
        'notifyme',
        'show_logo',
        'show_social',
        'custom_colors',
        'show_branches',
        'api_enabled',
        'keen_api_enabled',
        'google_api_enabled',
        'fb_api_enabled',
        'fb_invite_points',
        'hide_points',
        'nickname_variable_id',
        'show_toplist_points',
        'show_toplist_entries',
        'profilepic_variable_id',
        'notifications_enabled',
        'cookie_lifetime:datetime',
        'perm_can_reset',
        'perm_can_delete',
        'lang_show',
        'secondary_points',
        'tertiary_points',
        'template',
        'game_wide',
        'asset_migration',
        'background_image_landscape',
        'background_image_portrait',
        'logo',
        'length',
        'description:ntext',
        'alert',
        'custom_css',
        'shorturl:url',
        'custom_domain',
        'home_instructions:ntext',
        'social_share_url:url',
        'social_share_description',
        'social_force_to_canvas_url:url',
        'app_fb_hash',
        'colors:ntext',
        'api_key',
        'api_secret_key',
        'api_callback_url:url',
        'api_application_id',
        'keen_api_master_key',
        'keen_api_write_key:ntext',
        'keen_api_read_key:ntext',
        'keen_api_config:ntext',
        'google_api_code',
        'google_api_config:ntext',
        'fb_api_id',
        'fb_api_secret',
        'game_password',
        'notification_config:ntext',
        'secondary_points_title',
        'tertiary_points_title',
        'primary_points_shortname',
        'primary_points_title',
        'secondary_points_shortname',
        'tertiary_points_shortname',
        'thirdparty_api_config:ntext',
        'auth_config:ntext',
        'visual_config:ntext',
        'visual_config_params:ntext',
        'last_update',
        'name',
        'icon',
        'headboard_portrait',
        'headboard_landscape',
        'skin',
        'icon_primary_points',
        'icon_secondary_points',
        'icon_tertiary_points',
        'lang_default',
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


    
<?php $this->beginBlock('AeExtItems'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Items',
            ['/tatjack/ae-ext-item/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Item',
            ['/tatjack/ae-ext-item/create', 'AeExtItem' => ['game_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtItems', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtItems ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtItems(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextitems',
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
        $params[0] = '/tatjack/ae-ext-item' . '/' . $action;
        $params['AeExtItem'] = ['game_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/tatjack/ae-ext-item'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'play_id',
    'value' => function ($model) {
        if ($rel = $model->getPlay()->one()) {
            return Html::a($rel->id, ['/tatjack/ae-game-play/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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
            return Html::a($rel->name, ['/tatjack/ae-ext-items-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
        'name',
        'description',
        'price',
        'time',
        'images:ntext',
        'date_added',
]
])
 . '</div>' 
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('AeExtItemsCategories'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Ext Items Categories',
            ['/tatjack/ae-ext-items-category/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Ext Items Category',
            ['/tatjack/ae-ext-items-category/create', 'AeExtItemsCategory' => ['app_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeExtItemsCategories', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeExtItemsCategories ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeExtItemsCategories(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aeextitemscategories',
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
        $params[0] = '/tatjack/ae-ext-items-category' . '/' . $action;
        $params['AeExtItemsCategory'] = ['app_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/tatjack/ae-ext-items-category'
],
        'id',
        'parent_id',
        'picture',
        'name',
]
])
 . '</div>' 
?>
<?php Pjax::end() ?>
<?php $this->endBlock() ?>


<?php $this->beginBlock('AeGamePlays'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Game Plays',
            ['/tatjack/ae-game-play/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Game Play',
            ['/tatjack/ae-game-play/create', 'AeGamePlay' => ['game_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeGamePlays', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeGamePlays ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeGamePlays(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aegameplays',
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
        $params[0] = '/tatjack/ae-game-play' . '/' . $action;
        $params['AeGamePlay'] = ['game_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/tatjack/ae-game-play'
],
        'id',
        'role_id',
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
        'last_update',
        'created',
        'progress',
        'status',
        'alert',
        'level',
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
    'label'   => '<b class=""># '.$model->id.'</b>',
    'content' => $this->blocks['backend\modules\tatjack\models\AeGame'],
    'active'  => true,
],
[
    'content' => $this->blocks['AeExtItems'],
    'label'   => '<small>Ae Ext Items <span class="badge badge-default">'.count($model->getAeExtItems()->asArray()->all()).'</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeExtItemsCategories'],
    'label'   => '<small>Ae Ext Items Categories <span class="badge badge-default">'.count($model->getAeExtItemsCategories()->asArray()->all()).'</span></small>',
    'active'  => false,
],
[
    'content' => $this->blocks['AeGamePlays'],
    'label'   => '<small>Ae Game Plays <span class="badge badge-default">'.count($model->getAeGamePlays()->asArray()->all()).'</span></small>',
    'active'  => false,
],
 ]
                 ]
    );
    ?>
</div>
