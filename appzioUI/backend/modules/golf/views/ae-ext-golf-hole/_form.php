<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\golf\models\AeExtGolfHole $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-golf-hole-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtGolfHole',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>

<!-- attribute id -->
            <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

<!-- attribute place_id -->
			<?= $form->field($model, 'place_id')->textInput(['maxlength' => true]) ?>

<!-- attribute number -->
			<?= $form->field($model, 'number')->textInput() ?>

<!-- attribute par -->
			<?= $form->field($model, 'par')->textInput() ?>

<!-- attribute hcp -->
			<?= $form->field($model, 'hcp')->textInput() ?>

<!-- attribute length_pro -->
			<?= $form->field($model, 'length_pro')->textInput() ?>

<!-- attribute length_men -->
			<?= $form->field($model, 'length_men')->textInput() ?>

<!-- attribute length_women -->
			<?= $form->field($model, 'length_women')->textInput() ?>

<!-- attribute length_junior -->
			<?= $form->field($model, 'length_junior')->textInput() ?>

<!-- attribute type -->
			<?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

<!-- attribute tee_lat -->
			<?= $form->field($model, 'tee_lat')->textInput(['maxlength' => true]) ?>

<!-- attribute tee_lon -->
			<?= $form->field($model, 'tee_lon')->textInput(['maxlength' => true]) ?>

<!-- attribute flag_lat -->
			<?= $form->field($model, 'flag_lat')->textInput(['maxlength' => true]) ?>

<!-- attribute flag_lon -->
			<?= $form->field($model, 'flag_lon')->textInput(['maxlength' => true]) ?>

<!-- attribute beacon_id -->
			<?= $form->field($model, 'beacon_id')->textInput(['maxlength' => true]) ?>

<!-- attribute map -->
			<?= $form->field($model, 'map')->textInput(['maxlength' => true]) ?>

<!-- attribute map_approach -->
			<?= $form->field($model, 'map_approach')->textInput(['maxlength' => true]) ?>

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
    'label'   => Yii::t('backend', 'AeExtGolfHole'),
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

