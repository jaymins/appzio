<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFitProgramSubcategory $model
*/

$this->title = Yii::t('backend', 'Program Subcategory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Program Subcategories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-fit-program-subcategory-create">

    <h1>
        <?= Yii::t('backend', 'Program Subcategory') ?>
        <small>
                        <?= Html::encode($model->name) ?>
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
