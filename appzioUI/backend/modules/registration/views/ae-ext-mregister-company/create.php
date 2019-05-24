<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/fccccf4deb34aed738291a9c38e87215
 *
 * @package default
 */


use yii\helpers\Html;

/**
 *
 * @var yii\web\View $this
 * @var backend\modules\registration\models\AeExtMregisterCompany $model
 */
$this->title = Yii::t('backend', 'Licensed Companies');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Ae Ext Mregister Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud ae-ext-mregister-company-create">

    <h1>
        <?php echo Yii::t('backend', 'Licensed Companies') ?>
        <small>
                        <?php echo Html::encode($model->name) ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?php echo             Html::a(
	Yii::t('backend', 'Cancel'),
	\yii\helpers\Url::previous(),
	['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?php echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
