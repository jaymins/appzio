<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\golf\models\AeLocationLog $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-location-log-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeLocationLog',
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

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput(['maxlength' => true]) ?>

<!-- attribute beacon_id -->
			<?= $form->field($model, 'beacon_id')->textInput(['maxlength' => true]) ?>

<!-- attribute place_id -->
			<?= $form->field($model, 'place_id')->textInput(['maxlength' => true]) ?>

<!-- attribute date -->
			<?= $form->field($model, 'date')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeLocationLog'),
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

