<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\tasks\models\AeExtMtasksInvitation $model
*/
    
$this->title = Yii::t('backend', 'Ae Ext Mtasks Invitation') . " " . $model->name . ', ' . Yii::t('backend', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Mtasks Invitation'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-mtasks-invitation-update">

    <h1>
        <?= Yii::t('backend', 'Ae Ext Mtasks Invitation') ?>
        <small>
                        <?= $model->name ?>
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
