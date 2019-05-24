<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\items\search\AeExtItem $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-item-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'category_id') ?>


		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'name') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'price') ?>

		<?php // echo $form->field($model, 'time') ?>

		<?php // echo $form->field($model, 'images') ?>

		<?php // echo $form->field($model, 'date_added') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'featured') ?>

		<?php // echo $form->field($model, 'external') ?>

		<?php // echo $form->field($model, 'city') ?>

		<?php // echo $form->field($model, 'country') ?>

		<?php // echo $form->field($model, 'lat') ?>

		<?php // echo $form->field($model, 'lon') ?>

		<?php // echo $form->field($model, 'buyer_play_id') ?>

		<?php // echo $form->field($model, 'source') ?>

		<?php // echo $form->field($model, 'importa_date') ?>

		<?php // echo $form->field($model, 'external_id') ?>

		<?php // echo $form->field($model, 'slug') ?>

		<?php // echo $form->field($model, 'extra_data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
