<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtFitExercise $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-fit-exercise-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'article_id') ?>

		<?php // echo $form->field($model, 'points') ?>

		<?php // echo $form->field($model, 'duration') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
