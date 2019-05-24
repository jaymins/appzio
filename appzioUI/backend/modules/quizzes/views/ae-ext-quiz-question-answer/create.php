<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/fccccf4deb34aed738291a9c38e87215
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuizQuestionAnswer $model
 */
$this->title = Yii::t('backend', 'Ae Ext Quiz Question Answer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Quiz Question Answers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-quiz-question-answer-create">

    <h1>
        <?php echo Yii::t('backend', 'Ae Ext Quiz Question Answer') ?>
        <small>
                        <?php echo Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?php echo             Html::a(
	'Cancel',
	\yii\helpers\Url::previous(),
	['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
