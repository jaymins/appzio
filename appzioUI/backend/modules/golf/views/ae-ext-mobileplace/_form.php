<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\golf\models\AeExtMobileplace $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-mobileplace-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtMobileplace',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute game_id -->
			<?= $form->field($model, 'game_id')->textInput(['maxlength' => true]) ?>

<!-- attribute lat -->
			<?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

<!-- attribute lon -->
			<?= $form->field($model, 'lon')->textInput(['maxlength' => true]) ?>

<!-- attribute name -->
			<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!-- attribute address -->
			<?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

<!-- attribute zip -->
			<?= $form->field($model, 'zip')->textInput() ?>

<!-- attribute city -->
			<?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

<!-- attribute country -->
			<?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

<!-- attribute info -->
			<?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

<!-- attribute images -->
			<?= $form->field($model, 'images')->textarea(['rows' => 6]) ?>

<!-- attribute premium -->
			<?= $form->field($model, 'premium')->textInput() ?>

<!-- attribute headerimage1 -->
			<?= $form->field($model, 'headerimage1')->textInput(['maxlength' => true]) ?>

<!-- attribute headerimage2 -->
			<?= $form->field($model, 'headerimage2')->textInput(['maxlength' => true]) ?>

<!-- attribute headerimage3 -->
			<?= $form->field($model, 'headerimage3')->textInput(['maxlength' => true]) ?>

<!-- attribute last_update -->
			<?= $form->field($model, 'last_update')->textInput() ?>

<!-- attribute county -->
			<?= $form->field($model, 'county')->textInput(['maxlength' => true]) ?>

<!-- attribute logo -->
			<?= $form->field($model, 'logo')->fileInput(['accept' => 'image/*']) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtMobileplace'),
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

