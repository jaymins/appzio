<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tatjack\models\AeExtItemsFilter $model
*/
    
$this->title = Yii::t('backend', 'Ae Ext Items Filter') . " " . $model->id . ', ' . Yii::t('backend', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Items Filter'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-items-filter-update">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Items Filter') ?>
        <small>
                        <?= $model->id ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?php echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
