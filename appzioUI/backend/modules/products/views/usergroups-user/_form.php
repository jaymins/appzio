<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\products\models\UsergroupsUser $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="usergroups-user-form">

    <?php $form = ActiveForm::begin([
    'id' => 'UsergroupsUser',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute group_id -->
			<?= $form->field($model, 'group_id')->textInput() ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput() ?>

<!-- attribute developer_karmapoints -->
			<?= $form->field($model, 'developer_karmapoints')->textInput() ?>

<!-- attribute temp_user -->
			<?= $form->field($model, 'temp_user')->textInput() ?>

<!-- attribute last_push -->
			<?= $form->field($model, 'last_push')->textInput() ?>

<!-- attribute play_id -->
			<?= $form->field($model, 'play_id')->textInput() ?>

<!-- attribute terms_approved -->
			<?= $form->field($model, 'terms_approved')->textInput() ?>

<!-- attribute username -->
			<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

<!-- attribute email -->
			<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<!-- attribute firstname -->
			<?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

<!-- attribute lastname -->
			<?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

<!-- attribute developer_phone -->
			<?= $form->field($model, 'developer_phone')->textInput(['maxlength' => true]) ?>

<!-- attribute developer_verification -->
			<?= $form->field($model, 'developer_verification')->textInput(['maxlength' => true]) ?>

<!-- attribute alert -->
			<?= $form->field($model, 'alert')->textarea(['rows' => 6]) ?>

<!-- attribute phone -->
			<?= $form->field($model, 'phone')->textarea(['rows' => 6]) ?>

<!-- attribute timezone -->
			<?= $form->field($model, 'timezone')->textInput(['maxlength' => true]) ?>

<!-- attribute twitter -->
			<?= $form->field($model, 'twitter')->textInput(['maxlength' => true]) ?>

<!-- attribute skype -->
			<?= $form->field($model, 'skype')->textInput(['maxlength' => true]) ?>

<!-- attribute fbid -->
			<?= $form->field($model, 'fbid')->textInput(['maxlength' => true]) ?>

<!-- attribute fbtoken -->
			<?= $form->field($model, 'fbtoken')->textInput(['maxlength' => true]) ?>

<!-- attribute fbtoken_long -->
			<?= $form->field($model, 'fbtoken_long')->textInput(['maxlength' => true]) ?>

<!-- attribute nickname -->
			<?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

<!-- attribute creator_api_key -->
			<?= $form->field($model, 'creator_api_key')->textInput(['maxlength' => true]) ?>

<!-- attribute source -->
			<?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

<!-- attribute laratoken -->
			<?= $form->field($model, 'laratoken')->textInput(['maxlength' => true]) ?>

<!-- attribute active_app_id -->
			<?= $form->field($model, 'active_app_id')->textInput(['maxlength' => true]) ?>

<!-- attribute sftp_username -->
			<?= $form->field($model, 'sftp_username')->textInput(['maxlength' => true]) ?>

<!-- attribute question -->
			<?= $form->field($model, 'question')->textarea(['rows' => 6]) ?>

<!-- attribute answer -->
			<?= $form->field($model, 'answer')->textarea(['rows' => 6]) ?>

<!-- attribute ban_reason -->
			<?= $form->field($model, 'ban_reason')->textarea(['rows' => 6]) ?>

<!-- attribute creation_date -->
			<?= $form->field($model, 'creation_date')->textInput() ?>

<!-- attribute activation_time -->
			<?= $form->field($model, 'activation_time')->textInput() ?>

<!-- attribute last_login -->
			<?= $form->field($model, 'last_login')->textInput() ?>

<!-- attribute ban -->
			<?= $form->field($model, 'ban')->textInput() ?>

<!-- attribute language -->
			<?= $form->field($model, 'language')->textInput(['maxlength' => true]) ?>

<!-- attribute password -->
			<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

<!-- attribute home -->
			<?= $form->field($model, 'home')->textInput(['maxlength' => true]) ?>

<!-- attribute activation_code -->
			<?= $form->field($model, 'activation_code')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'UsergroupsUser'),
    'content' => $this->blocks['main'],
    'active'  => true,
],
                    ]
                 ]
    );
    ?>
        <hr/>

        <?php echo $form->errorSummary($model); ?>

        <?= Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> ' .
        ($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Save')),
        [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success'
        ]
        );
        ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>

