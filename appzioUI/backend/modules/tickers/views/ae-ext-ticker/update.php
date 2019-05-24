<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\tickers\models\AeExtTicker $model
 * @var $exchanges
 */

$this->title = Yii::t('backend', 'Ticker');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ticker'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>

<div class="giiant-crud ae-ext-ticker-update">

    <h1>
        <?= Yii::t('backend', 'Ticker') ?>
        <small>
            <?= $model->id ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form_us', [
        'model' => $model,
    ]); ?>

</div>