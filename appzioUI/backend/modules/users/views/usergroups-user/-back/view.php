<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var backend\modules\users\models\UsergroupsUser $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Usergroups User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Usergroups Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud usergroups-user-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Usergroups User') ?>
        <small>
            <?= $model->id ?>
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
            ['create', 'id' => $model->id, 'UsergroupsUser'=>$copyParams],
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

    <?php $this->beginBlock('backend\modules\users\models\UsergroupsUser'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
            'group_id',
        'status',
        'developer_karmapoints',
        'temp_user',
        'last_push',
        'play_id',
        'username',
        'email:email',
        'firstname',
        'lastname',
        'developer_phone',
        'developer_verification',
        'alert:ntext',
        'phone:ntext',
        'timezone',
        'twitter',
        'skype',
        'fbid',
        'fbtoken',
        'fbtoken_long',
        'nickname',
        'creator_api_key',
        'source',
        'laratoken',
        'active_app_id',
        'question:ntext',
        'answer:ntext',
        'ban_reason:ntext',
        'creation_date',
        'activation_time',
        'last_login',
        'ban',
        'language',
        'password',
        'home',
        'activation_code',
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


    
<?php $this->beginBlock('AeGames'); ?>
<div style='position: relative'>
<div style='position:absolute; right: 0px; top: 0px;'>
  <?= Html::a(
            '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('backend', 'List All') . ' Ae Games',
            ['/users/ae-game/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Game',
            ['/users/ae-game/create', 'AeGame' => ['user_id' => $model->id]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>
</div>
</div>
<?php Pjax::begin(['id'=>'pjax-AeGames', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-AeGames ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert("yo")}']]) ?>
<?=
 '<div class="table-responsive">'
 . \yii\grid\GridView::widget([
    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \yii\data\ActiveDataProvider([
        'query' => $model->getAeGames(),
        'pagination' => [
            'pageSize' => 20,
            'pageParam'=>'page-aegames',
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
        $params[0] = '/users/ae-game' . '/' . $action;
        $params['AeGame'] = ['user_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/users/ae-game'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'category_id',
    'value' => function ($model) {
        if ($rel = $model->getCategory()->one()) {
            return Html::a($rel->name, ['/users/ae-category/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
        'name',
        'active',
        'icon',
        'headboard_portrait',
        'headboard_landscape',
        'logo',
        'length',
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
            ['/users/ae-game-play/index'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>
  <?= Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('backend', 'New') . ' Ae Game Play',
            ['/users/ae-game-play/create', 'AeGamePlay' => ['user_id' => $model->id]],
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
        $params[0] = '/users/ae-game-play' . '/' . $action;
        $params['AeGamePlay'] = ['user_id' => $model->primaryKey()[0]];
        return $params;
    },
    'buttons'    => [
        
    ],
    'controller' => '/users/ae-game-play'
],
        'id',
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'role_id',
    'value' => function ($model) {
        if ($rel = $model->getRole()->one()) {
            return Html::a($rel->title, ['/users/ae-role/view', 'id' => $rel->id,], ['data-pjax' => 0]);
        } else {
            return '';
        }
    },
    'format' => 'raw',
],
// generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::columnFormat
[
    'class' => yii\grid\DataColumn::className(),
    'attribute' => 'game_id',
    'value' => function ($model) {
        if ($rel = $model->getGame()->one()) {
            return Html::a($rel->name, ['/users/ae-game/view', 'id' => $rel->id,], ['data-pjax' => 0]);
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
    'content' => $this->blocks['backend\modules\users\models\UsergroupsUser'],
    'active'  => true,
],
[
    'content' => $this->blocks['AeGames'],
    'label'   => '<small>Ae Games <span class="badge badge-default">'.count($model->getAeGames()->asArray()->all()).'</span></small>',
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
