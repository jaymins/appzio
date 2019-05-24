<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMovementCategory $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-fit-exercise-movement-category-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtFitExerciseMovementCategory',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
             'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
             'horizontalCssClasses' => [
                 'label' => 'col-sm-2',
                 #'offset' => 'col-sm-offset-4',
                 'wrapper' => 'col-sm-8',
                 'error' => '',
                 'hint' => '',
             ],
         ],
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute exercise_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'exercise_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtFitExercise::find()->all(), 'id', 'name'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['exercise_id'])),
    ]
); ?>

<!-- attribute movement_category -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'movement_category')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtFitMovementCategory::find()->all(), 'id', 'name'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['movement_category'])),
    ]
); ?>

<!-- attribute timer_type -->
			<?= $form->field($model, 'timer_type')->textInput(['maxlength' => true]) ?>

<!-- attribute rounds -->
			<?= $form->field($model, 'rounds')->textInput() ?>

        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtFitExerciseMovementCategory'),
    'content' => $this->blocks['main'],
    'active'  => true,
],
                    ]
                 ]
    );
    ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> ' .
        ($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Save')),
        [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success'
        ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>

