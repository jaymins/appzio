<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/eeda5c365686c9888dbc13dbc58f89a1
 *
 * @package default
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\registration\search\AeExtMregisterCompaniesUser $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-mregister-companies-user-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

    		<?php echo $form->field($model, 'id') ?>

		<?php echo $form->field($model, 'app_id') ?>

		<?php echo $form->field($model, 'company_id') ?>

		<?php echo $form->field($model, 'play_id') ?>

		<?php echo $form->field($model, 'registered') ?>

		<?php // echo $form->field($model, 'firstname') ?>

		<?php // echo $form->field($model, 'lastname') ?>

		<?php // echo $form->field($model, 'department') ?>

		<?php // echo $form->field($model, 'email') ?>

		<?php // echo $form->field($model, 'phone') ?>

		<?php // echo $form->field($model, 'registered_date') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
