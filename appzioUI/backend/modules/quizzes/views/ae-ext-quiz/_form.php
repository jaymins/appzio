<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuiz $model
 * @var yii\widgets\ActiveForm $form
 * @var $questions
 * @var $questions_json
 */

?>

<div class="ae-ext-quiz-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtQuiz',
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

            <!-- attribute name -->
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <!-- attribute description -->
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= Yii::$app->controller->renderPartial('quiz-fields', [
                'questions' => $questions,
                'current_selection' => $questions_json,
            ]); ?>

            <!-- attribute valid_from -->
            <?= $form->field($model, 'valid_from')->textInput() ?>

            <!-- attribute valid_to -->
            <?= $form->field($model, 'valid_to')->textInput() ?>

            <!-- attribute active -->
            <?= $form->field($model, 'active')->textInput() ?>

            <!-- attribute show_in_list -->
            <?= $form->field($model, 'show_in_list')->textInput() ?>

            <!-- attribute save_to_database -->
            <?= $form->field($model, 'save_to_database')->textInput() ?>

            <!-- attribute image -->
            <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'AeExtQuiz'),
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

