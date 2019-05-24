<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\models\AeExtTicker $model
 * @var yii\widgets\ActiveForm $form
 * @var $exchanges
 */

?>

<div class="ae-ext-ticker-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtTicker',
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

            <?php if ($model->exchange AND $model->ticker) : ?>

                <?= $form->field($model, 'ticker')->textInput([
                    'disabled' => true,
                ]) ?>

                <?= $form->field($model, 'exchange_name')->textInput([
                    'disabled' => true,
                ]) ?>

                <?= $form->field($model, 'company')->textInput([
                    'disabled' => true,
                ]) ?>

                <!-- attribute currency -->
                <?= $form->field($model, 'currency')->textInput([
                    'disabled' => true,
                ]) ?>

            <?php else : ?>

                <!-- attribute ticker -->
                <?= $form->field($model, 'ticker')->textInput([
                    'id' => 'tickers-select',
                    'prompt' => 'Enter your exchange',
                ]) ?>

            <?php endif; ?>

            <!-- attribute ticker_date -->
            <? /*= $form->field($model, 'ticker_date')->textInput() */ ?>

            <? /*= $form->field($model, 'ticker_date')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99/99/9999',
            ]); */ ?>

            <!-- attribute overall -->
            <?= $form->field($model, 'overall')->dropDownList([
                '0' => 'No',
                '1' => 'Yes',
            ]) ?>

            <!-- attribute last_update -->
            <? /*= $form->field($model, 'last_update')->textInput() */ ?>
        </p>

        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Ticker'),
                        'content' => $this->blocks['main'],
                        'active' => true,
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