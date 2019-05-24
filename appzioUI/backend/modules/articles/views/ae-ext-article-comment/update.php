<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\articles\models\AeExtArticleComment $model
*/

$this->title = Yii::t('backend', 'Article Comment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Article Comment'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-article-comment-update">

    <h1>
        <?= Yii::t('backend', 'Article Comment') ?>
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
