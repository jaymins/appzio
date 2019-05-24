<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\search\AeExtFitProgram $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-fit-program-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'subcategory_id') ?>

		<?php // echo $form->field($model, 'article_id') ?>

		<?php // echo $form->field($model, 'program_type') ?>

		<?php // echo $form->field($model, 'program_sub_type') ?>

		<?php // echo $form->field($model, 'is_challenge') ?>

		<?php // echo $form->field($model, 'exercises_per_day') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
