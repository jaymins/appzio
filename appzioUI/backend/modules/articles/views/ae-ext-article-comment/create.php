<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\articles\models\AeExtArticleComment $model
*/

$this->title = Yii::t('backend', 'Article Comment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Comments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-article-comment-create">

    <h1>
        <?= Yii::t('backend', 'Article Comment') ?>
        <small>
                        <?= $model->id ?>
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
