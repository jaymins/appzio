<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitComponentMovement $model
*/

$this->title = Yii::t('backend', 'Ae Ext Fit Component Movement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Fit Component Movements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-component-movement-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Fit Component Movement') ?>
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
