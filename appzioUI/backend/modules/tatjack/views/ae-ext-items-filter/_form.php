<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeExtItemsFilter $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-items-filter-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtItemsFilter',
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
			<?= $form->field($model, 'play_id')->textInput(['maxlength' => true]) ?>

<!-- attribute category_id -->
			<?= $form->field($model, 'category_id')->textInput(['maxlength' => true]) ?>

<!-- attribute price_from -->
			<?= $form->field($model, 'price_from')->textInput() ?>

<!-- attribute price_to -->
			<?= $form->field($model, 'price_to')->textInput() ?>

<!-- attribute tags -->
			<?= $form->field($model, 'tags')->textarea(['rows' => 6]) ?>

<!-- attribute category -->
			<?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtItemsFilter'),
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

