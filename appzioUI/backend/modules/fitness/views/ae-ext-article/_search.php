<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtArticle $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-article-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'title') ?>

		<?php // echo $form->field($model, 'header') ?>

		<?php // echo $form->field($model, 'content') ?>

		<?php // echo $form->field($model, 'link') ?>

		<?php // echo $form->field($model, 'rating') ?>

		<?php // echo $form->field($model, 'featured') ?>

		<?php // echo $form->field($model, 'article_date') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
