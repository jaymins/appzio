<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\AeLocationLog $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-location-log-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'beacon_id') ?>

		<?= $form->field($model, 'place_id') ?>

		<?php // echo $form->field($model, 'lat') ?>

		<?php // echo $form->field($model, 'lon') ?>

		<?php // echo $form->field($model, 'date') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
