<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFoodRecipeType $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ae-ext-food-recipe-type-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtFoodRecipeType',
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
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'app_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeGame::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'disabled' => ($model->app_id ? true : false),
                ]
            ); ?>

            <!-- attribute name -->
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Food Recipe Type'),
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

