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
 * @var backend\modules\fitness\search\AeExtPurchaseProduct $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="ae-ext-purchase-product-search">

    <?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

    		<?php echo $form->field($model, 'id') ?>

		<?php echo $form->field($model, 'app_id') ?>

		<?php echo $form->field($model, 'name') ?>

		<?php echo $form->field($model, 'code') ?>

		<?php echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'price') ?>

		<?php // echo $form->field($model, 'code_ios') ?>

		<?php // echo $form->field($model, 'code_android') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'image') ?>

		<?php // echo $form->field($model, 'icon') ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
