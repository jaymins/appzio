<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\search\AeGameVariable $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-game-variable-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'game_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'used_by_actions') ?>

		<?= $form->field($model, 'set_on_players') ?>

		<?php // echo $form->field($model, 'value_type') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
