<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuiz $model
 * @var $questions
 * @var $questions_json
 */

$this->title = Yii::t('backend', 'Quiz');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Quizzes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-quiz-create">

    <h1>
        <?= Yii::t('backend', 'Quiz') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a(
                Yii::t('backend', 'Cancel'),
                \yii\helpers\Url::previous(),
                ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'questions' => $questions,
        'questions_json' => $questions_json,
    ]); ?>

</div>
