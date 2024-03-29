<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\users\models\AeGamePlay $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-game-play-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeGamePlay',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger'
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute role_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'role_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\users\models\AeRole::find()->all(), 'id', 'title'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['role_id'])),
    ]
); ?>

<!-- attribute game_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'game_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\users\models\AeGame::find()->all(), 'id', 'name'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['game_id'])),
    ]
); ?>

<!-- attribute user_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'user_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\users\models\UsergroupsUser::find()->all(), 'id', 'id'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['user_id'])),
    ]
); ?>

<!-- attribute progress -->
			<?= $form->field($model, 'progress')->textInput() ?>

<!-- attribute status -->
			<?= $form->field($model, 'status')->textInput() ?>

<!-- attribute level -->
			<?= $form->field($model, 'level')->textInput() ?>

<!-- attribute current_level_id -->
			<?= $form->field($model, 'current_level_id')->textInput(['maxlength' => true]) ?>

<!-- attribute priority -->
			<?= $form->field($model, 'priority')->textInput() ?>

<!-- attribute branch_starttime -->
			<?= $form->field($model, 'branch_starttime')->textInput() ?>

<!-- attribute last_action_update -->
			<?= $form->field($model, 'last_action_update')->textInput() ?>

<!-- attribute last_update -->
			<?= $form->field($model, 'last_update')->textInput() ?>

<!-- attribute created -->
			<?= $form->field($model, 'created')->textInput() ?>

<!-- attribute alert -->
			<?= $form->field($model, 'alert')->textInput(['maxlength' => true]) ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeGamePlay'),
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

