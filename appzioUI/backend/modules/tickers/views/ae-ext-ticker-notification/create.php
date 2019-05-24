<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tickers\models\AeExtTickerNotification $model
*/

$this->title = Yii::t('backend', 'Ae Ext Ticker Notification');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Ticker Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-ticker-notification-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Ticker Notification') ?>
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
