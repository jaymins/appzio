<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\fitness\models\AeExtFoodRecipe $model
*/

$this->title = Yii::t('backend', 'Food Recipe');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Food Recipe'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>
<div class="giiant-crud ae-ext-food-recipe-update">

    <h1>
        <?= Yii::t('backend', 'Food Recipe') ?>
        <small>
                        <?= Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="crud-navigation">
        <?= Html::a('<span class="glyphicon glyphicon-file"></span> ' . Yii::t('backend', 'View'), ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    </div>

    <hr />

    <?= $this->render('_form', [
        'model' => $model,
        'ingredients' => $ingredients,
        'steps' => $steps,
        'current_selection_ingredients' => $ingredients_json,
        'current_selection_steps' => $steps_json
    ]); ?>

</div>
