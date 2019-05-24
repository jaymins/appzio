<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\AeExtGolfHole $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-golf-hole-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'place_id') ?>

		<?= $form->field($model, 'number') ?>

		<?= $form->field($model, 'par') ?>

		<?= $form->field($model, 'hcp') ?>

		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'tee_lat') ?>

		<?php // echo $form->field($model, 'tee_lon') ?>

		<?php // echo $form->field($model, 'flag_lat') ?>

		<?php // echo $form->field($model, 'flag_lon') ?>

		<?php // echo $form->field($model, 'beacon_id') ?>

		<?php // echo $form->field($model, 'length_pro') ?>

		<?php // echo $form->field($model, 'length_men') ?>

		<?php // echo $form->field($model, 'length_women') ?>

		<?php // echo $form->field($model, 'length_junior') ?>

		<?php // echo $form->field($model, 'map') ?>

		<?php // echo $form->field($model, 'map_approach') ?>

		<?php // echo $form->field($model, 'comments') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
