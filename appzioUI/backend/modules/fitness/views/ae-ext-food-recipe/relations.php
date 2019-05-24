<?php

/**
 * @var backend\modules\fitness\models\AeExtFitExercise $model
 * @var $movements
 * @var $movement_category
 * @var $current_selection
 */
?>

<div class="form-group field-answers-content required">
    <?= Yii::$app->controller->renderPartial('ingredients-fields', [
        'ingredients' => $ingredients,
        'current_selection' => $current_selection_ingredients,
    ]); ?>

    <?= Yii::$app->controller->renderPartial('step-fields', [
        'steps' => $steps,
        'current_selection' => $current_selection_steps,
    ]); ?>
</div>