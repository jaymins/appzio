<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\search\AeExtTickerDaily $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-ticker-daily-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'ticker_id') ?>

		<?= $form->field($model, 'ticker') ?>

		<?= $form->field($model, 'timestamp') ?>

		<?= $form->field($model, 'open') ?>

		<?php // echo $form->field($model, 'high') ?>

		<?php // echo $form->field($model, 'low') ?>

		<?php // echo $form->field($model, 'value') ?>

		<?php // echo $form->field($model, 'volume') ?>

		<?php // echo $form->field($model, 'previousclose') ?>

		<?php // echo $form->field($model, 'valuechange') ?>

		<?php // echo $form->field($model, 'date') ?>

		<?php // echo $form->field($model, 'ref_lock') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
