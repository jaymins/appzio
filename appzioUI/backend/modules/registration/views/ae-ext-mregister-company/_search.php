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
 * @var backend\modules\registration\search\AeExtMregisterCompany $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-mregister-company-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

    		<?php echo $form->field($model, 'id') ?>

		<?php echo $form->field($model, 'app_id') ?>

		<?php echo $form->field($model, 'name') ?>

		<?php echo $form->field($model, 'subscription_active') ?>

		<?php echo $form->field($model, 'subscription_expires') ?>

		<?php // echo $form->field($model, 'user_limit') ?>

		<?php // echo $form->field($model, 'notes') ?>

		<?php // echo $form->field($model, 'admit_by_domain') ?>

		<?php // echo $form->field($model, 'domain') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
