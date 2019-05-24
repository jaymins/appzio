<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\AeExtMobileplace $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mobileplace-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'lat') ?>

		<?= $form->field($model, 'lon') ?>

		<?= $form->field($model, 'last_update') ?>

		<?php // echo $form->field($model, 'name') ?>

		<?php // echo $form->field($model, 'address') ?>

		<?php // echo $form->field($model, 'zip') ?>

		<?php // echo $form->field($model, 'city') ?>

		<?php // echo $form->field($model, 'county') ?>

		<?php // echo $form->field($model, 'country') ?>

		<?php // echo $form->field($model, 'info') ?>

		<?php // echo $form->field($model, 'logo') ?>

		<?php // echo $form->field($model, 'images') ?>

		<?php // echo $form->field($model, 'premium') ?>

		<?php // echo $form->field($model, 'headerimage1') ?>

		<?php // echo $form->field($model, 'headerimage2') ?>

		<?php // echo $form->field($model, 'headerimage3') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
