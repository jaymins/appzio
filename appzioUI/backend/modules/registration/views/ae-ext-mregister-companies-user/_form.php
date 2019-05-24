<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/4b7e79a8340461fe629a6ac612644d03
 *
 * @package default
 */


use backend\modules\fitness\models\AeGame;
use backend\modules\registration\models\AeExtMregisterCompany;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\registration\models\AeExtMregisterCompaniesUser $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-mregister-companies-user-form">

    <?php $form = ActiveForm::begin([
		'id' => 'AeExtMregisterCompaniesUser',
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
            <!-- attribute app_id -->
            <?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'app_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeGame::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'value' => isset($_SESSION['app_id']) ? $_SESSION['app_id'] : '',
                    'disabled' => false,
                ]
            ); ?>

            <!-- attribute company_id -->
            <?php echo // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
            $form->field($model, 'company_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(backend\modules\registration\models\AeExtMregisterCompany::find()->all(), 'id', 'name'),
                [
                    'prompt' => Yii::t('backend', 'Select'),
                    'disabled' => (isset($relAttributes) && isset($relAttributes['company_id'])),
                ]
            ); ?>


<!-- attribute firstname -->
			<?php echo $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

<!-- attribute lastname -->
			<?php echo $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

<!-- attribute department -->
			<?php echo $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

<!-- attribute email -->
			<?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<!-- attribute phone -->
			<?php echo $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

<!-- attribute play_id -->
        </p>
        <?php $this->endBlock(); ?>

        <?php echo
Tabs::widget(
	[
		'encodeLabels' => false,
		'items' => [
			[
				'label'   => Yii::t('backend', 'AeExtMregisterCompaniesUser'),
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
