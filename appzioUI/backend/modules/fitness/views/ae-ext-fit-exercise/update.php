<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitExercise $model
*/

$this->title = Yii::t('backend', 'Ae Ext Fit Exercise');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Fit Exercise'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-fit-exercise-update">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Fit Exercise') ?>
        <small>
                        <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
        'model' => $model,
        'component'=>$component,
        'relations_json'=>$relations_json
    ]); ?>

</div>
