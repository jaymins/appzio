<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeExtBooking $model
*/

$this->title = Yii::t('backend', 'Ae Ext Booking');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Bookings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-booking-create">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Booking') ?>
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
