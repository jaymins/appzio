<?php

use backend\components\Helper;
use dmstr\bootstrap\Tabs;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\modules\articles\models\AeExtArticlePhoto $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Article Photo');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Photos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-article-photo-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Article Photo') ?>
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
                ['create', 'id' => $model->id, 'Article Photo' => $copyParams],
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

    <?php $this->beginBlock('backend\modules\articles\models\AeExtArticlePhoto'); ?>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::attributeFormat
            [
                'format' => 'html',
                'attribute' => 'article_id',
                'value' => ($model->getArticle()->one() ?
                    Html::a('<i class="glyphicon glyphicon-list"></i>', ['/articles/ae-ext-article/index']) . ' ' .
                    Html::a('<i class="glyphicon glyphicon-circle-arrow-right"></i> ' . $model->getArticle()->one()->title, ['/articles/ae-ext-article/view', 'id' => $model->getArticle()->one()->id,]) . ' ' .
                    Html::a('<i class="glyphicon glyphicon-paperclip"></i>', ['create', 'Article Photo' => ['article_id' => $model->article_id]])
                    :
                    '<span class="label label-warning">?</span>'),
            ],
            'title',
            'description:ntext',
            [
                'label' => 'Photo',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->photo) {
                        return Html::img(Helper::getUploadURL() . $model->photo, ['width' => '180']);
                    }

                    return false;
                },
            ],
            'position',
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



    <?= Tabs::widget(
        [
            'id' => 'relation-tabs',
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => '<b class=""># ' . $model->id . '</b>',
                    'content' => $this->blocks['backend\modules\articles\models\AeExtArticlePhoto'],
                    'active' => true,
                ],
            ]
        ]
    );
    ?>
</div>
