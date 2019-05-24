<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\quizzes\models\AeExtQuizQuestion $model
 */

$this->title = Yii::t('backend', 'Quiz Question');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Quiz Questions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-quiz-question-create">

    <h1>
        <?= Yii::t('backend', 'Quiz Question') ?>
        <small>
            <?= Html::encode($model->title) ?>
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
        'answers' => [],
    ]); ?>

</div>
