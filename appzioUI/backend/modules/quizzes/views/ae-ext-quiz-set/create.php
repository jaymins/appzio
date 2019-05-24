<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\quizzes\models\AeExtQuizSet $model
*/

$this->title = Yii::t('backend', 'Ae Ext Quiz Set');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Quiz Sets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-quiz-set-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Quiz Set') ?>
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
