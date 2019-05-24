<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\users\search\AeExtMobilematching $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-mobilematching-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'lat') ?>

		<?= $form->field($model, 'lon') ?>

		<?php // echo $form->field($model, 'last_update') ?>

		<?php // echo $form->field($model, 'gender') ?>

		<?php // echo $form->field($model, 'match_always') ?>

		<?php // echo $form->field($model, 'score') ?>

		<?php // echo $form->field($model, 'flag') ?>

		<?php // echo $form->field($model, 'education') ?>

		<?php // echo $form->field($model, 'hindu_caste') ?>

		<?php // echo $form->field($model, 'role') ?>

		<?php // echo $form->field($model, 'is_boosted') ?>

		<?php // echo $form->field($model, 'boosted_timestamp') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
