<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\articles\models\AeExtArticleTemplate $model
 */

$this->title = Yii::t('backend', 'Article Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Template'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-article-template-update">

    <h1>
        <?= Yii::t('backend', 'Article Template') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr/>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
