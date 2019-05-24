<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\search\AeExtQuizQuestionOption $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-quiz-question-option-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'question_id') ?>

		<?= $form->field($model, 'answer') ?>

		<?= $form->field($model, 'answer_order') ?>

		<?= $form->field($model, 'is_correct') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
