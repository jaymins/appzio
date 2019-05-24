<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMovementCategory $model
*/

$this->title = Yii::t('backend', 'Exercise Movement Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Exercise Movement Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-exercise-movement-category-create">

    <h1>
        <?= Yii::t('backend', 'Exercise Movement Category') ?>
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
