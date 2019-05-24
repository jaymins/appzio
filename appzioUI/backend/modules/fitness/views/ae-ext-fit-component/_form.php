<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitComponent $model
* @var yii\widgets\ActiveForm $form
*/

?>

<? $use_validation = true;

if (isset($_GET['id'])) {
    $use_validation = false;
}

?>
<div class="ae-ext-fit-component-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtFitComponent',
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

<!-- attribute admin_name -->
			<?= $form->field($model, 'admin_name')->textInput(['maxlength' => true]) ?>

<!-- attribute rounds -->
			<?= $form->field($model, 'rounds')->textInput() ?>


<!-- attribute timer -->
            <?=
            $form->field($model, 'timer')->dropDownList(
                [
                    'count_down'=>'Count down timer',
                    'count_up'=>'Count up timer',
                    'hiit'=>'Hiit timer',
                    'rest'=>'Rest timer',
                    'notimer'=>'NO TIMER',
                ],
                [
                    'prompt' => Yii::t('backend', '-- Select'),
                ]
            ); ?>


            <!-- attribute rounds -->

            <div style="text-align:center;font-weight: bold;margin-bottom: 10px;">
            Total time is used only for countdown & display purposes — if set to 0, time from movements is used</div>
            <?=
            $form->field($model, 'total_time')->textInput() ?>


            <!-- attribute background_image -->
            <?= $form->field($model, 'background_image', [
                'enableClientValidation' => $use_validation,
                // 'enableAjaxValidation' => $use_validation
            ])->fileInput(['accept' => 'image/*'])

            ?>
            <?= Yii::$app->controller->renderPartial('movements-fields', [
                'movements' => $movements,
                'pr' => $pr,
                'relations_json'=>$relations_json
            ]); ?>

        </p>
        <?php $this->endBlock(); ?>

        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [
                        [
    'label'   => Yii::t('backend', 'AeExtFitComponent'),
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

