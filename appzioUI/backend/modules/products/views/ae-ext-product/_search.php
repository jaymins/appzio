<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\products\search\AeExtProduct $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-product-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'amazon_product_id') ?>

		<?php // echo $form->field($model, 'photo') ?>

		<?php // echo $form->field($model, 'additional_photos') ?>

		<?php // echo $form->field($model, 'title') ?>

		<?php // echo $form->field($model, 'header') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'link') ?>

		<?php // echo $form->field($model, 'rating') ?>

		<?php // echo $form->field($model, 'price') ?>

		<?php // echo $form->field($model, 'points_value') ?>

		<?php // echo $form->field($model, 'featured') ?>

		<?php // echo $form->field($model, 'sorting') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
