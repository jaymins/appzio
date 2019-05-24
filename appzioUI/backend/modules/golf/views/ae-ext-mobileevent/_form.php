<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\golf\models\AeExtMobileevent $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-mobileevent-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtMobileevent',
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

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput(['maxlength' => true]) ?>

<!-- attribute place_id -->
			<?= $form->field($model, 'place_id')->textInput(['maxlength' => true]) ?>

<!-- attribute title -->
			<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<!-- attribute date -->
			<?= $form->field($model, 'date')->textInput() ?>

<!-- attribute time -->
			<?= $form->field($model, 'time')->textInput() ?>

<!-- attribute time_of_day -->
			<?= $form->field($model, 'time_of_day')->textInput(['maxlength' => true]) ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

<!-- attribute notes -->
			<?= $form->field($model, 'notes')->textInput(['maxlength' => true]) ?>

<!-- attribute starting_time -->
			<?= $form->field($model, 'starting_time')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtMobileevent'),
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

