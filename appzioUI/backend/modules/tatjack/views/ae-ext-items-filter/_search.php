<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\search\AeExtItemsFilter $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-items-filter-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'price_from') ?>

		<?= $form->field($model, 'price_to') ?>

		<?php // echo $form->field($model, 'tags') ?>

		<?php // echo $form->field($model, 'category') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
