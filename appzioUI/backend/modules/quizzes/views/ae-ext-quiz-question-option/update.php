<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\models\AeExtQuizQuestionOption $model
*/

$this->title = Yii::t('backend', 'Question Option');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Question Option'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-quiz-question-option-update">

    <h1>
        <?= Yii::t('backend', 'Question Option') ?>
        <small>
                        <?= Html::encode($model->id) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
