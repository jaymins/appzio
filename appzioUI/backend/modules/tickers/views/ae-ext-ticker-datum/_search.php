<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\search\AeExtTickerDatum $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-ticker-datum-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'ticker_id') ?>

		<?= $form->field($model, 'date') ?>

		<?= $form->field($model, 'open') ?>

		<?= $form->field($model, 'hight') ?>

		<?php // echo $form->field($model, 'low') ?>

		<?php // echo $form->field($model, 'close') ?>

		<?php // echo $form->field($model, 'volume') ?>

		<?php // echo $form->field($model, 'update_date') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
