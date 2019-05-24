<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\golf\models\AeExtGolfHoleUser $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-golf-hole-user-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtGolfHoleUser',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute hole_id -->
			<?= $form->field($model, 'hole_id')->textInput(['maxlength' => true]) ?>

<!-- attribute event_id -->
			<?= $form->field($model, 'event_id')->textInput(['maxlength' => true]) ?>

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput(['maxlength' => true]) ?>

<!-- attribute strokes -->
			<?= $form->field($model, 'strokes')->textInput() ?>

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
    'label'   => Yii::t('backend', 'AeExtGolfHoleUser'),
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

