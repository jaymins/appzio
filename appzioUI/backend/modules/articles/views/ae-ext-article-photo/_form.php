<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var backend\modules\articles\models\AeExtArticlePhoto $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ae-ext-article-photo-form">

    <?php $form = ActiveForm::begin([
            'id' => 'Article Photo',
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

            <!-- attribute article_id -->
            <?= $form->field($model, 'article_id')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(backend\modules\articles\models\AeExtArticle::find()->all(), 'id', 'title'),
                'language' => 'en',
                'options' => ['placeholder' => 'Select an article ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <!-- attribute title -->
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <!-- attribute description -->
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <!-- attribute photo -->
            <?= $form->field($model, 'photo')->fileInput(['accept' => 'image/*']) ?>

            <!-- attribute position -->
            <?= $form->field($model, 'position')->textInput([
                    'maxlength' => true,
                    'placeholder' => ' You could use one of the following options "featured", "listing" or custom one',
            ]) ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Article Photo'),
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