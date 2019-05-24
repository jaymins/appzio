<?php

use dmstr\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\items\models\AeExtItemsImage $model
 * @var yii\widgets\ActiveForm $form
 */

?>

<div class="ae-ext-items-image-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtItemsImage',
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


            <!-- attribute item_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'item_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\items\models\AeExtItem::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['item_id'])),
                ]
            ); ?>

            <!-- attribute date -->
            <?= $form->field($model, 'date')->textInput() ?>

            <!-- attribute image -->
            <?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>

            <!-- attribute featured -->
            <?= $form->field($model, 'featured')->textInput() ?>

            <!-- attribute image_order -->
            <?= $form->field($model, 'image_order')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>

        <?=
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'AeExtItemsImage'),
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

