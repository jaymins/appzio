<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\search\AeExtQuizQuestion $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-quiz-question-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'app_id') ?>

		<?= $form->field($model, 'variable_name') ?>

		<?= $form->field($model, 'title') ?>

		<?= $form->field($model, 'question') ?>

		<?php // echo $form->field($model, 'active') ?>

		<?php // echo $form->field($model, 'picture') ?>

		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'allow_multiple') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
