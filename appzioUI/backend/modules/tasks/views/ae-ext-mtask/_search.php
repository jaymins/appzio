<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\search\AeExtMtask $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mtask-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'owner_id') ?>

		<?= $form->field($model, 'invitation_id') ?>

		<?= $form->field($model, 'assignee_id') ?>

		<?= $form->field($model, 'category_id') ?>

		<?php // echo $form->field($model, 'created_time') ?>

		<?php // echo $form->field($model, 'start_time') ?>

		<?php // echo $form->field($model, 'deadline') ?>

		<?php // echo $form->field($model, 'repeat_frequency') ?>

		<?php // echo $form->field($model, 'times_frequency') ?>

		<?php // echo $form->field($model, 'title') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'picture') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'completion') ?>

		<?php // echo $form->field($model, 'comments') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
