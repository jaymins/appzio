<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFitProgram $model
 * @var $weeks
 * @var $recipes
 * @var $exercise
 */

$this->title = Yii::t('backend', 'Create food program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="giiant-crud ae-ext-fit-program-create">

    <h1>
        <?= Yii::t('backend', 'Create food program') ?>
        <small>
            <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= Html::a(
                Yii::t('backend', 'Cancel'),
                \yii\helpers\Url::previous(),
                ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'weeks' => $weeks,
        'recipes' => $recipes,
        'exercise' => $exercise,
        'exercise_json' => [],
        'recipes_json' => []
    ]); ?>

</div>