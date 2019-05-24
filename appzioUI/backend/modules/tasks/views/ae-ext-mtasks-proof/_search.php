<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\search\AeExtMtasksProof $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mtasks-proof-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'task_id') ?>

		<?= $form->field($model, 'created_date') ?>

		<?= $form->field($model, 'description') ?>

		<?= $form->field($model, 'comment') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'photo') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
