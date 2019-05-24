<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\modules\fitness\models\AeExtFitProgram $model
 * @var $weeks
 * @var $exercise
 * @var $exercise_json
 * @var $recipes_json
 * @var $recipes
 */

$this->title = Yii::t('backend', 'Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Program'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend', 'Edit');
?>

<div class="giiant-crud ae-ext-fit-program-update">

    <h1>
        <?= Yii::t('backend', 'Program') ?>
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
        'weeks' => $weeks,
        'recipes' =>$recipes,
        'exercise' => $exercise,
        'exercise_json' => $exercise_json,
        'recipes_json' => $recipes_json
    ]); ?>

</div>
