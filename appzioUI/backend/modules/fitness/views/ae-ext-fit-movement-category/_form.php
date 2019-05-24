<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitMovementCategory $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-fit-movement-category-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtFitMovementCategory',
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
<!-- attribute name -->
            <?= $form->field($model, 'admin_name')->textInput(['maxlength' => true]) ?>

<!-- attribute timer_type -->
            <?=
            $form->field($model, 'timer_type')->dropDownList(
                    [
                            'hiit'=>'HIit timer',
                            'count_down'=>'Count down timer',
                            'count_up'=>'Count up timer',
                    ],
                [
                    'prompt' => Yii::t('backend', 'Select'),
                ]
            ); ?>

  <?= $form->field($model, 'rounds')->textInput(['maxlength' => true]) ?>

<!-- attribute background_image -->
			<?= $form->field($model, 'background_image')->textInput(['maxlength' => true]) ?>

            <?= Yii::$app->controller->renderPartial('movements-fields', [
                'movements'=>$movements,
                'relations_json' => []
            ]); ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'Component'),
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

