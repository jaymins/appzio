<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtFitProgramExercise $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-fit-program-exercise-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'program_id') ?>

		<?= $form->field($model, 'exercise_id') ?>

		<?= $form->field($model, 'week') ?>

		<?= $form->field($model, 'day') ?>

		<?php // echo $form->field($model, 'time') ?>

		<?php // echo $form->field($model, 'repeat_days') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
