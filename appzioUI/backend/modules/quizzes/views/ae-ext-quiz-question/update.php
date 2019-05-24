<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuizQuestion $model
 * @var $answers
 */

$this->title = Yii::t('backend', 'Quiz Question');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Quiz Question'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>

<div class="giiant-crud ae-ext-quiz-question-update">

    <h1>
        <?= Yii::t('backend', 'Quiz Question') ?>
        <small>
            <?= Html::encode($model->title) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form', [
        'model' => $model,
        'answers' => $answers,
    ]); ?>

</div>