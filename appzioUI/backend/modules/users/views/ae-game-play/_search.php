<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\users\search\AeGamePlay $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-game-play-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'role_id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'user_id') ?>

		<?= $form->field($model, 'last_update') ?>

		<?php // echo $form->field($model, 'created') ?>

		<?php // echo $form->field($model, 'progress') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'alert') ?>

		<?php // echo $form->field($model, 'level') ?>

		<?php // echo $form->field($model, 'current_level_id') ?>

		<?php // echo $form->field($model, 'priority') ?>

		<?php // echo $form->field($model, 'branch_starttime') ?>

		<?php // echo $form->field($model, 'last_action_update') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
