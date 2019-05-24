<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\search\AeExtTickerNotification $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-ticker-notification-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'trade_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'notification_id') ?>

		<?= $form->field($model, 'date') ?>

		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
