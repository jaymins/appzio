<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuiz $model
 * @var $questions
 * @var $questions_json
 */

$this->title = Yii::t('backend', 'Quiz');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Quiz'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>

<div class="giiant-crud ae-ext-quiz-update">

    <h1>
        <?= Yii::t('backend', 'Quiz') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form', [
        'model' => $model,
        'questions' => $questions,
        'questions_json' => $questions_json,
    ]); ?>

</div>
