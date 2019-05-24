<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\models\AeExtMtask $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-mtask-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtMtask',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute owner_id -->
			<?= $form->field($model, 'owner_id')->textInput() ?>

<!-- attribute invitation_id -->
			<?= $form->field($model, 'invitation_id')->textInput() ?>

<!-- attribute assignee_id -->
			<?= $form->field($model, 'assignee_id')->textInput() ?>

<!-- attribute category_id -->
			<?= $form->field($model, 'category_id')->textInput() ?>

<!-- attribute created_time -->
			<?= $form->field($model, 'created_time')->textInput() ?>

<!-- attribute start_time -->
			<?= $form->field($model, 'start_time')->textInput() ?>

<!-- attribute deadline -->
			<?= $form->field($model, 'deadline')->textInput() ?>

<!-- attribute repeat_frequency -->
			<?= $form->field($model, 'repeat_frequency')->textInput() ?>

<!-- attribute times_frequency -->
			<?= $form->field($model, 'times_frequency')->textInput() ?>

<!-- attribute title -->
			<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<!-- attribute description -->
			<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<!-- attribute picture -->
			<?= $form->field($model, 'picture')->textInput(['maxlength' => true]) ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

<!-- attribute completion -->
			<?= $form->field($model, 'completion')->textInput() ?>

<!-- attribute comments -->
			<?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtMtask'),
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

