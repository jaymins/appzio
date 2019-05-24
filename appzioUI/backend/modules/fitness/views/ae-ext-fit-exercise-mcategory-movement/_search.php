<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtFitExerciseMcategoryMovement $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-fit-exercise-mcategory-movement-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'exercise_movement_cat_id') ?>

		<?= $form->field($model, 'weight') ?>

		<?= $form->field($model, 'reps') ?>

		<?= $form->field($model, 'rest') ?>

		<?php // echo $form->field($model, 'pionts') ?>

		<?php // echo $form->field($model, 'movement_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
