<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\articles\models\AeExtArticleCategory $model
*/

$this->title = Yii::t('backend', 'Article Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Category'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-article-category-update">

    <h1>
        <?= Yii::t('backend', 'Article Category') ?>
        <small>
                        <?= $model->title ?>
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
