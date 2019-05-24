<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\models\AeExtTicker $model
 * @var $exchanges
 */

$this->title = Yii::t('backend', 'Ticker');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Tickers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud ae-ext-ticker-create">

    <h1>
        <?= Yii::t('backend', 'Ticker') ?>
        <small>
            <?= $model->id ?>
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

    <?= $this->render('_form_us', [
        'model' => $model,
    ]); ?>

</div>