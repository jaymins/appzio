<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFitComponent $model
 */

$this->title = Yii::t('backend', 'Ae Ext Fit Component');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Fit Components'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-component-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Fit Component') ?>
        <small>
            <?= Html::encode($model->name) ?>
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
        'movements' => $movements,
        'pr' => $pr,
        'relations_json'=>$relations_json
    ]); ?>

</div>
