<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\models\AeExtTickerDatum $model
*/

$this->title = Yii::t('backend', 'Ticker Datum');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ticker Data'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-ticker-datum-create">

    <h1>
        <?= Yii::t('backend', 'Ticker Datum') ?>
        <small>
                        <?= $model->id ?>
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
