<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\search\AeExtUserBid $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-user-bid-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'bid_item_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'price') ?>

		<?= $form->field($model, 'message') ?>

		<?php // echo $form->field($model, 'created_date') ?>

		<?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
