<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtArticle $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="ae-ext-article-form">

    <?php $form = ActiveForm::begin([
    'id' => 'AeExtArticle',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
             'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
             'horizontalCssClasses' => [
                 'label' => 'col-sm-2',
                 #'offset' => 'col-sm-offset-4',
                 'wrapper' => 'col-sm-8',
                 'error' => '',
                 'hint' => '',
             ],
         ],
    ]
    );
    ?>

    <div class="">
        <?php $this->beginBlock('main'); ?>

        <p>
            

<!-- attribute app_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'app_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeGame::find()->all(), 'id', 'name'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['app_id'])),
    ]
); ?>

<!-- attribute title -->
			<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<!-- attribute header -->
			<?= $form->field($model, 'header')->textarea(['rows' => 6]) ?>

<!-- attribute content -->
			<?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

<!-- attribute link -->
			<?= $form->field($model, 'link')->textarea(['rows' => 6]) ?>

<!-- attribute category_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'category_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeExtArticleCategory::find()->all(), 'id', 'title'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['category_id'])),
    ]
); ?>

<!-- attribute play_id -->
			<?= // generated by schmunk42\giiant\generators\crud\providers\core\RelationProvider::activeField
$form->field($model, 'play_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(backend\modules\fitness\models\AeGamePlay::find()->all(), 'id', 'id'),
    [
        'prompt' => Yii::t('backend', 'Select'),
        'disabled' => (isset($relAttributes) && isset($relAttributes['play_id'])),
    ]
); ?>

<!-- attribute rating -->
			<?= $form->field($model, 'rating')->textInput() ?>

<!-- attribute featured -->
			<?= $form->field($model, 'featured')->textInput() ?>

<!-- attribute article_date -->
			<?= $form->field($model, 'article_date')->textInput() ?>
        </p>
        <?php $this->endBlock(); ?>
        
        <?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
    'label'   => Yii::t('backend', 'AeExtArticle'),
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

