<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\search\AeExtQuiz $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-quiz-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'description') ?>

		<?= $form->field($model, 'valid_from') ?>

		<?php // echo $form->field($model, 'valid_to') ?>

		<?php // echo $form->field($model, 'active') ?>

		<?php // echo $form->field($model, 'show_in_list') ?>

		<?php // echo $form->field($model, 'image') ?>

		<?php // echo $form->field($model, 'save_to_database') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
