<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFoodRecipe $model
 */

$this->title = Yii::t('backend', 'Food Recipe');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Food Recipes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-food-recipe-create">

    <h1>
        <?= Yii::t('backend', 'Food Recipe') ?>
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
        'ingredients' => $ingredients,
        'steps' => $steps,
        'current_selection_ingredients' => $ingredients_json,
        'current_selection_steps' => $steps_json
    ]); ?>

</div>
