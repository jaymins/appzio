<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitMovementCategory $model
*/

$this->title = Yii::t('backend', 'Component');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Components'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-movement-category-create">

    <h1>
        <?= Yii::t('backend', 'Component') ?>
        <small>
                        <?= Html::encode($model->name) ?>
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
        'movements'=>$movements
    ]); ?>

</div>
