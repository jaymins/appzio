<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\search\AeExtTicker $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-ticker-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ticker') ?>

    <?= $form->field($model, 'company') ?>

    <?= $form->field($model, 'currency') ?>

    <?= $form->field($model, 'exchange') ?>

    <?php // echo $form->field($model, 'exchange_name') ?>

    <?php // echo $form->field($model, 'ticker_date') ?>

    <?php // echo $form->field($model, 'overall') ?>

    <?php // echo $form->field($model, 'last_update') ?>

    <?php // echo $form->field($model, 'log') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>