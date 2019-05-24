<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/eeda5c365686c9888dbc13dbc58f89a1
 *
 * @package default
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\quizzes\search\AeExtQuizQuestionAnswer $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-quiz-question-answer-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

    		<?php echo $form->field($model, 'id') ?>

		<?php echo $form->field($model, 'question_id') ?>

		<?php echo $form->field($model, 'answer_id') ?>

		<?php echo $form->field($model, 'answer') ?>

		<?php echo $form->field($model, 'play_id') ?>

		<?php // echo $form->field($model, 'comment') ?>

		<?php // echo $form->field($model, 'date_created') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
