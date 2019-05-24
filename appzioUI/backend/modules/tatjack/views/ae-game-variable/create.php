<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeGameVariable $model
*/

$this->title = Yii::t('backend', 'Ae Game Variable');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Game Variables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-game-variable-create">

    <h1>
        <?= Yii::t('backend', 'Ae Game Variable') ?>
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
