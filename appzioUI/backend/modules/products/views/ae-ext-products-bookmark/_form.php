<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\products\models\AeExtProductsBookmark $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-products-bookmark-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtProductsBookmark',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute play_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'play_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\products\models\AeGamePlay::find()->all(), 'id', 'id'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['play_id'])),
    ]
); ?>

<!-- attribute product_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'product_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\products\models\AeExtProduct::find()->all(), 'id', 'title'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['product_id'])),
    ]
); ?>

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
    'label'   => Yii::t('backend', 'AeExtProductsBookmark'),
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

