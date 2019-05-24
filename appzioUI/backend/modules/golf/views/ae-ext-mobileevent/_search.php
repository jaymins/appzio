<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\AeExtMobileevent $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mobileevent-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'place_id') ?>

		<?= $form->field($model, 'title') ?>

		<?php // echo $form->field($model, 'date') ?>

		<?php // echo $form->field($model, 'time') ?>

		<?php // echo $form->field($model, 'time_of_day') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'notes') ?>

		<?php // echo $form->field($model, 'starting_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
