<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExerciseMcategoryMovement $model
*/

$this->title = Yii::t('backend', 'Exercise Mcategory Movement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Exercise Mcategory Movement'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-fit-exercise-mcategory-movement-update">

    <h1>
        <?= Yii::t('backend', 'Exercise Mcategory Movement') ?>
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
