<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\articles\models\AeExtArticleTemplate $model
 */

$this->title = Yii::t('backend', 'Article Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-article-template-create">

    <h1>
        <?= Yii::t('backend', 'Article Template') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a(
                Yii::t('backend', 'Cancel'),
                \yii\helpers\Url::previous(),
                ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
