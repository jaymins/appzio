<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\models\AeExtMtasksProof $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-mtasks-proof-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtMtasksProof',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute task_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'task_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\tasks\models\AeExtMtask::find()->all(), 'id', 'title'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['task_id'])),
    ]
); ?>

<!-- attribute created_date -->
			<?= $form->field($model, 'created_date')->textInput() ?>

<!-- attribute description -->
			<?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

<!-- attribute comment -->
			<?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!-- attribute photo -->
			<?= $form->field($model, 'photo')->textInput(['maxlength' => true]) ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtMtasksProof'),
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

