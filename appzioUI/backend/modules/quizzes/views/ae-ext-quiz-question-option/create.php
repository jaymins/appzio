<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\models\AeExtQuizQuestionOption $model
*/

$this->title = Yii::t('backend', 'Question Option');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Question Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-quiz-question-option-create">

    <h1>
        <?= Yii::t('backend', 'Question Option') ?>
        <small>
                        <?= Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?=             Html::a(
            Yii::t('backend', 'Cancel'),
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?= $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
