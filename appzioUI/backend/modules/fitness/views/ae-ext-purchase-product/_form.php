<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/4b7e79a8340461fe629a6ac612644d03
 *
 * @package default
 */


use backend\modules\fitness\models\AeExtPurchaseProduct;
use backend\modules\fitness\models\AeGame;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;
use yii\web\View;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtPurchaseProduct $model
 * @var \yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-purchase-product-form">

    <?php $form = ActiveForm::begin([
            'id' => 'AeExtPurchaseProduct',
            'layout' => 'horizontal',
            'enableClientValidation' => true,
            'errorSummaryCssClass' => 'error-summary alert alert-danger',
            'fieldConfig' => [
                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label' => 'col-sm-2',
                    //'offset' => 'col-sm-offset-4',
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
            <?php
            $model->app_id = $_SESSION['app_id'];
            echo // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField


            $form->field($model, 'app_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeGame::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'disabled' => false,
                ]
            ); ?>


            <!-- attribute name -->
            <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <!-- attribute name -->
            <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

            <!-- attribute type -->
            <?= $form->field($model, 'type')->dropDownList([
                'monthly_subscription' => 'Monthly Subscription (inApp Purchase)',
                'yearly_subscription' => 'Yearly Subscription (inApp Purchase)',
                'monthly_subscription_no_renew' => 'Non-renewable Monthly Subscription (inApp Purchase)',
                'yearly_subscription_no_renew' => 'Non-renewable Yearly Subscription (inApp Purchase)',
                'consumable' => 'Consumable (inApp Purchase)',
                'non_consumable' => 'Non Consumable (inApp Purchase)',
                'other' => 'Other (not inapp)',
            ]) ?>

        <p style="text-align:center;">
            Note that price and currency symbol are only indicative prices. Final price will be
        displayed by Apple / Google in the local currency.
        </div>

            <!-- attribute price -->
            <?php echo $form->field($model, 'price')->textInput() ?>

            <!-- attribute price -->
            <?php echo $form->field($model, 'currency')->textInput() ?>



            <!-- attribute code_ios -->
            <?php echo $form->field($model, 'code_ios')->textInput(['maxlength' => true]) ?>

            <!-- attribute code_android -->
            <?php echo $form->field($model, 'code_android')->textInput(['maxlength' => true]) ?>

            <!-- attribute description -->
            <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <!-- attribute image -->
            <!-- attribute icon -->
            <?= $form->field($model, 'image', [
            ])->fileInput(['accept' => 'image/*','class'=>'col-sm-6']);
            ?>
            <?= Html::img($model->icon)?>

            <!-- attribute icon -->
            <!-- attribute icon -->
            <?= $form->field($model, 'icon', [
            ])->fileInput(['accept' => 'image/*','class'=>'col-sm-6']);
            ?>
            <?= Html::img($model->icon)?>
        </p>
        <?php $this->endBlock(); ?>

        <?php echo
        Tabs::widget(
            [
                'encodeLabels' => false,
                'items' => [
                    [
                        'label'   => Yii::t('backend', 'AeExtPurchaseProduct'),
                        'content' => $this->blocks['main'],
                        'active'  => true,
                    ],
                ]
            ]
        );
        ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?php echo Html::submitButton(
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
