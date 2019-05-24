<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;
use backend\components\Helper;

/**
* @var yii\web\View $this
* @var backend\modules\places\models\AeExtMobileplace $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('backend', 'Ae Ext Mobileplace');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Mobileplaces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'View');
?>
<div class="giiant-crud ae-ext-mobileplace-view">

    <!-- flash message -->
    <?php if (\Yii::$app->session->getFlash('deleteError') !== null) : ?>
        <span class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <?= \Yii::$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>

    <h1>
        <?= Yii::t('backend', 'Ae Ext Mobileplace') ?>
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
            ['create', 'id' => $model->id, 'AeExtMobileplace'=>$copyParams],
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

    <?php $this->beginBlock('backend\modules\places\models\AeExtMobileplace'); ?>

    
    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'lat',
        'lon',
        'name',
	    [
		    'label' => 'Logo',
		    'format' => 'html',
		    'value' => function($model) {
			    if ( $model->logo ) {
				    return Html::img(Helper::getUploadURL() . $model->logo, ['width' => '150']);
			    }

			    return false;
		    },
	    ],
	    [
		    'label' => 'Image 1',
		    'format' => 'html',
		    'value' => function($model) {
			    if ( $model->headerimage1 ) {
				    return Html::img(Helper::getUploadURL() . $model->headerimage1, ['width' => '180']);
			    }

			    return false;
		    },
	    ],
	    [
		    'label' => 'Image 2',
		    'format' => 'html',
		    'value' => function($model) {
			    if ( $model->headerimage2 ) {
				    return Html::img(Helper::getUploadURL() . $model->headerimage2, ['width' => '180']);
			    }

			    return false;
		    },
	    ],
	    [
		    'label' => 'Image 3',
		    'format' => 'html',
		    'value' => function($model) {
			    if ( $model->headerimage3 ) {
				    return Html::img(Helper::getUploadURL() . $model->headerimage3, ['width' => '180']);
			    }

			    return false;
		    },
	    ],
	    'hex_color',
	    'code',
	    'address',
	    'zip',
	    'city',
	    'info:ntext',
	    'last_update',
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
    'label'   => '<b class=""># '.$model->id.'</b>',
    'content' => $this->blocks['backend\modules\places\models\AeExtMobileplace'],
    'active'  => true,
],
 ]
                 ]
    );
    ?>
</div>
