<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtCalendarEntry $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-calendar-entry-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'type_id') ?>

		<?= $form->field($model, 'exercise_id') ?>

		<?= $form->field($model, 'program_id') ?>

		<?php // echo $form->field($model, 'recipe_id') ?>

		<?php // echo $form->field($model, 'notes') ?>

		<?php // echo $form->field($model, 'points') ?>

		<?php // echo $form->field($model, 'time') ?>

		<?php // echo $form->field($model, 'completion') ?>

		<?php // echo $form->field($model, 'is_completed') ?>

		<?php // echo $form->field($model, 'completed_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
