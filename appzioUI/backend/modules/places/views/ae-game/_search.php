<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var backend\modules\places\search\AeGame $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="ae-game-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    ]); ?>

    		<?= $form->field($model, 'id') ?>

		<?= $form->field($model, 'user_id') ?>

		<?= $form->field($model, 'category_id') ?>

		<?= $form->field($model, 'name') ?>

		<?= $form->field($model, 'active') ?>

		<?php // echo $form->field($model, 'icon') ?>

		<?php // echo $form->field($model, 'headboard_portrait') ?>

		<?php // echo $form->field($model, 'headboard_landscape') ?>

		<?php // echo $form->field($model, 'background_image_landscape') ?>

		<?php // echo $form->field($model, 'background_image_portrait') ?>

		<?php // echo $form->field($model, 'logo') ?>

		<?php // echo $form->field($model, 'length') ?>

		<?php // echo $form->field($model, 'timelimit') ?>

		<?php // echo $form->field($model, 'description') ?>

		<?php // echo $form->field($model, 'levels') ?>

		<?php // echo $form->field($model, 'alert') ?>

		<?php // echo $form->field($model, 'featured') ?>

		<?php // echo $form->field($model, 'last_update') ?>

		<?php // echo $form->field($model, 'max_actions') ?>

		<?php // echo $form->field($model, 'custom_css') ?>

		<?php // echo $form->field($model, 'show_toplist') ?>

		<?php // echo $form->field($model, 'register_email') ?>

		<?php // echo $form->field($model, 'register_sms') ?>

		<?php // echo $form->field($model, 'start_without_registration') ?>

		<?php // echo $form->field($model, 'show_homepage') ?>

		<?php // echo $form->field($model, 'choose_playername') ?>

		<?php // echo $form->field($model, 'choose_avatar') ?>

		<?php // echo $form->field($model, 'shorturl') ?>

		<?php // echo $form->field($model, 'custom_domain') ?>

		<?php // echo $form->field($model, 'home_instructions') ?>

		<?php // echo $form->field($model, 'notifyme') ?>

		<?php // echo $form->field($model, 'skin') ?>

		<?php // echo $form->field($model, 'show_logo') ?>

		<?php // echo $form->field($model, 'show_social') ?>

		<?php // echo $form->field($model, 'social_share_url') ?>

		<?php // echo $form->field($model, 'social_share_description') ?>

		<?php // echo $form->field($model, 'social_force_to_canvas_url') ?>

		<?php // echo $form->field($model, 'app_fb_hash') ?>

		<?php // echo $form->field($model, 'custom_colors') ?>

		<?php // echo $form->field($model, 'colors') ?>

		<?php // echo $form->field($model, 'show_branches') ?>

		<?php // echo $form->field($model, 'api_key') ?>

		<?php // echo $form->field($model, 'api_secret_key') ?>

		<?php // echo $form->field($model, 'api_callback_url') ?>

		<?php // echo $form->field($model, 'api_application_id') ?>

		<?php // echo $form->field($model, 'api_enabled') ?>

		<?php // echo $form->field($model, 'keen_api_enabled') ?>

		<?php // echo $form->field($model, 'keen_api_master_key') ?>

		<?php // echo $form->field($model, 'keen_api_write_key') ?>

		<?php // echo $form->field($model, 'keen_api_read_key') ?>

		<?php // echo $form->field($model, 'keen_api_config') ?>

		<?php // echo $form->field($model, 'google_api_enabled') ?>

		<?php // echo $form->field($model, 'google_api_code') ?>

		<?php // echo $form->field($model, 'google_api_config') ?>

		<?php // echo $form->field($model, 'fb_api_enabled') ?>

		<?php // echo $form->field($model, 'fb_api_id') ?>

		<?php // echo $form->field($model, 'fb_api_secret') ?>

		<?php // echo $form->field($model, 'fb_invite_points') ?>

		<?php // echo $form->field($model, 'hide_points') ?>

		<?php // echo $form->field($model, 'game_password') ?>

		<?php // echo $form->field($model, 'nickname_variable_id') ?>

		<?php // echo $form->field($model, 'show_toplist_points') ?>

		<?php // echo $form->field($model, 'show_toplist_entries') ?>

		<?php // echo $form->field($model, 'profilepic_variable_id') ?>

		<?php // echo $form->field($model, 'notifications_enabled') ?>

		<?php // echo $form->field($model, 'cookie_lifetime') ?>

		<?php // echo $form->field($model, 'notification_config') ?>

		<?php // echo $form->field($model, 'perm_can_reset') ?>

		<?php // echo $form->field($model, 'perm_can_delete') ?>

		<?php // echo $form->field($model, 'lang_show') ?>

		<?php // echo $form->field($model, 'lang_default') ?>

		<?php // echo $form->field($model, 'secondary_points') ?>

		<?php // echo $form->field($model, 'secondary_points_title') ?>

		<?php // echo $form->field($model, 'tertiary_points') ?>

		<?php // echo $form->field($model, 'tertiary_points_title') ?>

		<?php // echo $form->field($model, 'primary_points_shortname') ?>

		<?php // echo $form->field($model, 'primary_points_title') ?>

		<?php // echo $form->field($model, 'secondary_points_shortname') ?>

		<?php // echo $form->field($model, 'tertiary_points_shortname') ?>

		<?php // echo $form->field($model, 'icon_primary_points') ?>

		<?php // echo $form->field($model, 'icon_secondary_points') ?>

		<?php // echo $form->field($model, 'icon_tertiary_points') ?>

		<?php // echo $form->field($model, 'template') ?>

		<?php // echo $form->field($model, 'game_wide') ?>

		<?php // echo $form->field($model, 'asset_migration') ?>

		<?php // echo $form->field($model, 'thirdparty_api_config') ?>

		<?php // echo $form->field($model, 'auth_config') ?>

		<?php // echo $form->field($model, 'visual_config') ?>

		<?php // echo $form->field($model, 'visual_config_params') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
