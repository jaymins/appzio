<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\articles\search\AeExtArticlePhoto $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-article-photo-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'article_id') ?>

		<?= $form->field($model, 'title') ?>

		<?= $form->field($model, 'description') ?>

		<?= $form->field($model, 'photo') ?>

		<?php // echo $form->field($model, 'position') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
