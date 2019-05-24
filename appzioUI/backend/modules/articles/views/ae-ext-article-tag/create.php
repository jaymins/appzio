<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\articles\models\AeExtArticleTag $model
*/

$this->title = Yii::t('backend', 'Article Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-article-tag-create">

    <h1>
        <?= Yii::t('backend', 'Article Tag') ?>
        <small>
                        <?= $model->title ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?=             Html::a(
            Yii::t('backend', 'Cancel'),
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
