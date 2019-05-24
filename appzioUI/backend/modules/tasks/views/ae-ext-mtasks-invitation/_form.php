<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\models\AeExtMtasksInvitation $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-mtasks-invitation-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtMtasksInvitation',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput() ?>

<!-- attribute name -->
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!-- attribute email -->
			<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<!-- attribute nickname -->
			<?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

<!-- attribute invited_play_id -->
			<?= $form->field($model, 'invited_play_id')->textInput() ?>

<!-- attribute primary_contact -->
			<?= $form->field($model, 'primary_contact')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtMtasksInvitation'),
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

