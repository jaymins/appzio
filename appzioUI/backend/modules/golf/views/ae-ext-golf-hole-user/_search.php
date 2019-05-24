<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\AeExtGolfHoleUser $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-ext-golf-hole-user-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'hole_id') ?>

		<?= $form->field($model, 'event_id') ?>

		<?= $form->field($model, 'play_id') ?>

		<?= $form->field($model, 'strokes') ?>

		<?php // echo $form->field($model, 'comments') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
