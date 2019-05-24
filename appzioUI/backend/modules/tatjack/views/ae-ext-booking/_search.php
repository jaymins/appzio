<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\search\AeExtBooking $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-booking-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'assignee_play_id') ?>

		<?= $form->field($model, 'item_id') ?>

		<?= $form->field($model, 'date') ?>

		<?php // echo $form->field($model, 'length') ?>

		<?php // echo $form->field($model, 'notes') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'price') ?>

		<?php // echo $form->field($model, 'lat') ?>

		<?php // echo $form->field($model, 'lon') ?>

		<?php // echo $form->field($model, 'created_at') ?>

		<?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
