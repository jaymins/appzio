<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\search\AeNotification $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-notification-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'id_user') ?>

		<?= $form->field($model, 'id_channel') ?>

		<?= $form->field($model, 'id_playaction') ?>

		<?= $form->field($model, 'app_id') ?>

		<?php // echo $form->field($model, 'onesignal_msgid') ?>

		<?php // echo $form->field($model, 'action_id') ?>

		<?php // echo $form->field($model, 'play_id') ?>

		<?php // echo $form->field($model, 'menu_id') ?>

		<?php // echo $form->field($model, 'menuid') ?>

		<?php // echo $form->field($model, 'shown_in_app') ?>

		<?php // echo $form->field($model, 'read_in_app') ?>

		<?php // echo $form->field($model, 'type') ?>

		<?php // echo $form->field($model, 'subject') ?>

		<?php // echo $form->field($model, 'message') ?>

		<?php // echo $form->field($model, 'email_to') ?>

		<?php // echo $form->field($model, 'parameters') ?>

		<?php // echo $form->field($model, 'badge_count') ?>

		<?php // echo $form->field($model, 'manual_config') ?>

		<?php // echo $form->field($model, 'sendtime') ?>

		<?php // echo $form->field($model, 'created') ?>

		<?php // echo $form->field($model, 'updated') ?>

		<?php // echo $form->field($model, 'repeated') ?>

		<?php // echo $form->field($model, 'lastsent') ?>

		<?php // echo $form->field($model, 'expired') ?>

		<?php // echo $form->field($model, 'debug') ?>

		<?php // echo $form->field($model, 'os_success') ?>

		<?php // echo $form->field($model, 'os_failed') ?>

		<?php // echo $form->field($model, 'os_converted') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
