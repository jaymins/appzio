<?php

use backend\modules\tatjack\models\AeGameVariable;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

use backend\components\Helper;

use kartik\export\ExportMenu;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
* @var backend\models\UsergroupsUserSearch $searchModel
*/

$this->title = Yii::t('backend', 'User Export');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Application Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$home_url = Url::home();
$real_url = Url::to( $home_url . 'users/usergroups-user/update' );

$model = new AeGameVariable();
$home = Url::to( $home_url . 'users/usergroups-user/index' );


if (isset($actionColumnTemplates)) {
$actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplateString = $actionColumnTemplate;
} else {
Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . 'New', ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplateString = "{view} {update} {delete}";
}
$actionColumnTemplateString = '<div class="action-buttons">'.$actionColumnTemplateString.'</div>';
?>

<div class="giiant-crud usergroups-user-index">

    <h1>
        <?= Yii::t('backend', 'Export Users') ?>

        <div style="display: inline-block; padding-left: 30px;">
        </div>

    </h1>

    <div>

            Export requested, you should recive it in your email shortly.<br><br>

            <a href="<?php echo($home); ?>" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Home</a>

        </form>

    </div>


</div>
