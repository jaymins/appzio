<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFoodIngredient $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ae-ext-food-ingredient-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtFoodIngredient',
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


            <!-- attribute name -->
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <!-- attribute category_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'category_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtFoodIngredientCategory::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['category_id'])),
                ]
            ); ?>

            <!-- attribute unit -->

            <!-- attribute difficult -->
            <?= $form->field($model, 'unit')->dropDownList([
                'an' => 'An',
                'a' => 'A',
                'a whole' => 'A whole',
                'a half' => 'A half',
                'pint' => 'Pint',
                'quart' => 'Quart',
                'gal' => 'Gallon',
                'ml' => 'Milliliter',
                'l' => 'Liter',
                'dl' => 'Deciliter',
                'lb' => 'Pound',
                'oz' => 'Ounce',
                'mg' => 'Milligram',
                'g' => 'Gram',
                'kg' => 'Kilogram',
            ], [
                'prompt' => Yii::t('backend', 'Select'),
                'disabled' => (isset($relAttributes) && isset($relAttributes['type_id'])),
            ]); ?>

        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Food Ingredient'),
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