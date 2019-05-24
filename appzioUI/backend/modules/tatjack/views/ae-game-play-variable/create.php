<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeGamePlayVariable $model
*/

$this->title = Yii::t('backend', 'Ae Game Play Variable');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Game Play Variables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-game-play-variable-create">

    <h1>
        <?= Yii::t('backend', 'Ae Game Play Variable') ?>
        <small>
                        <?= $model->name ?>
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
