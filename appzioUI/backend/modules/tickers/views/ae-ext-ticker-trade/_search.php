<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\search\AeExtTickerTrade $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-ticker-trade-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'ticker_id') ?>

		<?= $form->field($model, 'buy_range_from') ?>

		<?= $form->field($model, 'buy_range_to') ?>

		<?= $form->field($model, 'sell_range_from') ?>

		<?php // echo $form->field($model, 'sell_range_to') ?>

		<?php // echo $form->field($model, 'term') ?>

		<?php // echo $form->field($model, 'trade_notes') ?>

		<?php // echo $form->field($model, 'trade_date') ?>

		<?php // echo $form->field($model, 'active') ?>

		<?php // echo $form->field($model, 'stop_date') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
