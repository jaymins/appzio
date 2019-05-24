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
 * @var backend\modules\fitness\search\AeExtPurchase $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-purchase-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

    		<?php echo $form->field($model, 'id') ?>

		<?php echo $form->field($model, 'app_id') ?>

		<?php echo $form->field($model, 'play_id') ?>

		<?php echo $form->field($model, 'product_id') ?>

		<?php echo $form->field($model, 'price') ?>

		<?php // echo $form->field($model, 'currency') ?>

		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'date') ?>

		<?php // echo $form->field($model, 'store_id') ?>

		<?php // echo $form->field($model, 'receipt') ?>

		<?php // echo $form->field($model, 'subject') ?>

		<?php // echo $form->field($model, 'subscription') ?>

		<?php // echo $form->field($model, 'yearly') ?>

		<?php // echo $form->field($model, 'monthly') ?>

		<?php // echo $form->field($model, 'expiry') ?>

		<?php // echo $form->field($model, 'email') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
