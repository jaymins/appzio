<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuizQuestion $model
 * @var yii\widgets\ActiveForm $form
 * @var $answers
 */

?>

<div class="ae-ext-quiz-question-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtQuizQuestion',
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

            <!-- attribute app_id -->
            <?= $form->field($model, 'app_id')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(backend\modules\quizzes\models\AeGame::find()->all(), 'id', 'name'),
                'language' => 'en',
                'options' => ['placeholder' => 'Select an app'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <!-- attribute variable_name -->
            <?= $form->field($model, 'variable_name')->textInput(['maxlength' => true]) ?>

            <!-- attribute title -->
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <!-- attribute question -->
            <?= $form->field($model, 'question')->textarea(['rows' => 6]) ?>

            <?= Yii::$app->controller->renderPartial('quiz-question-fields', [
                'content' => $answers
            ]); ?>

            <!-- attribute active -->
            <?= $form->field($model, 'active')->textInput() ?>

            <!-- attribute picture -->
            <?= $form->field($model, 'picture')->textInput(['maxlength' => true]) ?>

            <!-- attribute allow_multiple -->
            <?= $form->field($model, 'allow_multiple')->textInput() ?>

            <!-- attribute type -->
            <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Quiz Question'),
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

