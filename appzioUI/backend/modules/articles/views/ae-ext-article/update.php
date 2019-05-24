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
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>

<div class="giiant-crud ae-ext-article-update">

    <h1>
        <?= Yii::t('backend', 'Article') ?>
        <small>
            <?= $model->title ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form', [
        'model' => $model,
        'images' => $images,
        'images_tags' => $images_tags,
        'article_templates' => $article_templates,
    ]); ?>

</div>