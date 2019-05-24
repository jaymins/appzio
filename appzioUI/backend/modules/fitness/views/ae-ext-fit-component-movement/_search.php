<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtFitComponentMovement $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-fit-component-movement-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'component_id') ?>

		<?= $form->field($model, 'movement_id') ?>

		<?= $form->field($model, 'weight') ?>

		<?= $form->field($model, 'unit') ?>

		<?php // echo $form->field($model, 'reps') ?>

		<?php // echo $form->field($model, 'movement_time') ?>

		<?php // echo $form->field($model, 'points') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
