<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\items\search\AeExtItemsImage $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-items-image-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'item_id') ?>

		<?= $form->field($model, 'date') ?>

		<?= $form->field($model, 'image') ?>

		<?= $form->field($model, 'featured') ?>

		<?php // echo $form->field($model, 'image_order') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
