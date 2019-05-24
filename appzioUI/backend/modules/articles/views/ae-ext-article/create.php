<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\articles\models\AeExtArticle $model
 * @var $images
 * @var $images_tags
 * @var $article_templates
 */

$this->title = Yii::t('backend', 'Article');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Articles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud ae-ext-article-create">

    <h1>
        <?= Yii::t('backend', 'Article') ?>
        <small>
            <?= $model->title ?>
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
        'images' => $images,
        'images_tags' => $images_tags,
        'article_templates' => $article_templates,
    ]); ?>

</div>