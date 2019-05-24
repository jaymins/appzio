<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtArticleCategory $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-article-category-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'parent_id') ?>

		<?= $form->field($model, 'sorting') ?>

		<?= $form->field($model, 'title') ?>

		<?php // echo $form->field($model, 'headertext') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'picture') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
