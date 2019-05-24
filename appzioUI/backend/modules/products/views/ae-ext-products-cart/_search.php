<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\products\search\AeExtProductsCart $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-products-cart-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'product_id') ?>

		<?= $form->field($model, 'task_id') ?>

		<?= $form->field($model, 'date_added') ?>

		<?php // echo $form->field($model, 'quantity') ?>

		<?php // echo $form->field($model, 'cart_status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
