<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\search\AeExtMtasksInvitation $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mtasks-invitation-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'invited_play_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'email') ?>

		<?php // echo $form->field($model, 'nickname') ?>

		<?php // echo $form->field($model, 'primary_contact') ?>

		<?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
