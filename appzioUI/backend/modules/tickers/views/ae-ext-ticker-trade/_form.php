<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\models\AeExtTickerTrade $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ae-ext-ticker-trade-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtTickerTrade',
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

            <!-- attribute ticker_id -->
            <?= $form->field($model, 'ticker_id')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(backend\modules\tickers\models\AeExtTicker::find()->all(), 'id', 'ticker'),
                'language' => 'en',
                'options' => ['placeholder' => 'Select a ticker ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <!-- attribute buy_range_from -->
            <?= $form->field($model, 'buy_range_from')->textInput() ?>

            <!-- attribute buy_range_to -->
            <?= $form->field($model, 'buy_range_to')->textInput() ?>

            <!-- attribute sell_range_from -->
            <?= $form->field($model, 'sell_range_from')->textInput() ?>

            <!-- attribute sell_range_to -->
            <?= $form->field($model, 'sell_range_to')->textInput() ?>

            <!-- attribute term -->
            <?= $form->field($model, 'term')->dropDownList([
                'longterm' => 'Longterm',
                'shortterm' => 'Shortterm',
                'midterm' => 'Midterm',
            ]) ?>

            <!-- attribute trade_notes -->
            <?= $form->field($model, 'trade_notes')->textarea(['rows' => 6]) ?>

            <?php if ( isset($model->active) AND $model->active == 0 ) : ?>

            <?php else : ?>

                <!-- attribute active -->
                <?= $form->field($model, 'active')->dropDownList([
                    '1' => 'Yes',
                    '0' => 'No',
                ], [
                    'disabled' => (!isset($model->active) ? true : false)
                ]) ?>

            <?php endif; ?>

            <!-- attribute trade_date -->
            <? /*= $form->field($model, 'trade_date')->textInput() */ ?>

            <!-- attribute stop_date -->
            <? /*= $form->field($model, 'stop_date')->textInput() */ ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Trade Data'),
                        'content' => $this->blocks['main'],
                        'active' => true,
                    ],
                ]
            ]
        );
        ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?php

            if ( isset($model->active) AND $model->active == 0 ) {
                echo '<p style="text-align: center; font-size: 18px;">This trade is already disabled. You may create a new one.</p>';
            } else {
                echo Html::submitButton(
                    '<span class="glyphicon glyphicon-check"></span> ' .
                    ($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Save')),
                    [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-success'
                    ]
                );
            }
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>