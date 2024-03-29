<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-fit-exercise-mcategory-movement-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtFitExerciseMcategoryMovement',
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
            

<!-- attribute exercise_movement_cat_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'exercise_movement_cat_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtFitExerciseMovementCategory::find()->all(), 'id', 'id'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['exercise_movement_cat_id'])),
    ]
); ?>

<!-- attribute rest -->
			<?= $form->field($model, 'rest')->textInput() ?>

<!-- attribute pionts -->
			<?= $form->field($model, 'pionts')->textInput() ?>

<!-- attribute movement_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'movement_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtFitMovement::find()->all(), 'id', 'name'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['movement_id'])),
    ]
); ?>

<!-- attribute weight -->
			<?= $form->field($model, 'weight')->textInput() ?>

<!-- attribute reps -->
			<?= $form->field($model, 'reps')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtFitExerciseMcategoryMovement'),
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

