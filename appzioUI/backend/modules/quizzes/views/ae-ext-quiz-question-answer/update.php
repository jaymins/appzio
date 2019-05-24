<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/fcd70a9bfdf8de75128d795dfc948a74
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Quiz Question Answer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="giiant-crud ae-ext-quiz-question-answer-update">

    <h1>
        <?php echo Yii::t('backend', 'Ae Ext Quiz Question Answer') ?>
        <small>
                        <?php echo Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?php echo Html::a('<span class="glyphicon glyphicon-file"></span> ' . 'View', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
