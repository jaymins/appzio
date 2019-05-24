<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\golf\search\UsergroupsUser $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="usergroups-user-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'group_id') ?>

		<?= $form->field($model, 'language') ?>

		<?= $form->field($model, 'username') ?>

		<?= $form->field($model, 'password') ?>

		<?php // echo $form->field($model, 'email') ?>

		<?php // echo $form->field($model, 'firstname') ?>

		<?php // echo $form->field($model, 'lastname') ?>

		<?php // echo $form->field($model, 'home') ?>

		<?php // echo $form->field($model, 'status') ?>

		<?php // echo $form->field($model, 'question') ?>

		<?php // echo $form->field($model, 'answer') ?>

		<?php // echo $form->field($model, 'creation_date') ?>

		<?php // echo $form->field($model, 'activation_code') ?>

		<?php // echo $form->field($model, 'activation_time') ?>

		<?php // echo $form->field($model, 'last_login') ?>

		<?php // echo $form->field($model, 'ban') ?>

		<?php // echo $form->field($model, 'ban_reason') ?>

		<?php // echo $form->field($model, 'developer_phone') ?>

		<?php // echo $form->field($model, 'developer_verification') ?>

		<?php // echo $form->field($model, 'developer_karmapoints') ?>

		<?php // echo $form->field($model, 'alert') ?>

		<?php // echo $form->field($model, 'phone') ?>

		<?php // echo $form->field($model, 'timezone') ?>

		<?php // echo $form->field($model, 'twitter') ?>

		<?php // echo $form->field($model, 'skype') ?>

		<?php // echo $form->field($model, 'fbid') ?>

		<?php // echo $form->field($model, 'fbtoken') ?>

		<?php // echo $form->field($model, 'fbtoken_long') ?>

		<?php // echo $form->field($model, 'nickname') ?>

		<?php // echo $form->field($model, 'creator_api_key') ?>

		<?php // echo $form->field($model, 'temp_user') ?>

		<?php // echo $form->field($model, 'source') ?>

		<?php // echo $form->field($model, 'last_push') ?>

		<?php // echo $form->field($model, 'play_id') ?>

		<?php // echo $form->field($model, 'laratoken') ?>

		<?php // echo $form->field($model, 'active_app_id') ?>

		<?php // echo $form->field($model, 'sftp_username') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
