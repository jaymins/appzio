<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseComponent $model
*/

$this->title = Yii::t('backend', 'Ae Ext Fit Exercise Component');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Fit Exercise Components'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-exercise-component-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Fit Exercise Component') ?>
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
