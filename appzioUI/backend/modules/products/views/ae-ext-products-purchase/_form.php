<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\products\models\AeExtProductsPurchase $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-products-purchase-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtProductsPurchase',
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
			<?= $form->field($model, 'id')->textInput() ?>

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput() ?>

<!-- attribute product_id -->
			<?= $form->field($model, 'product_id')->textInput() ?>

<!-- attribute date -->
			<?= $form->field($model, 'date')->textInput() ?>

<!-- attribute price -->
			<?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtProductsPurchase'),
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

