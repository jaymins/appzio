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
        <form action="/appzioUI/backend/web/users/usergroups-user/doexport" method="post" id="export">

            The export file will be emailed to you.<br><br>

            Email where to send the export to:<br>
            <input type="text" name="send_email" style="min-width: 250px;font-size:16px;"><br>  <br>
            <input type="checkbox" name="param_all_variables" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1">All variables<br><br>
            <input type="checkbox" name="param_name" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Name<br>
            <input type="checkbox" name="param_email" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Email<br>
            <input type="checkbox" name="param_bu" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Business Unit<br>
            <input type="checkbox" name="param_regdate" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Registration date<br>
            <input type="checkbox" name="param_posted_notes" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Number of posted notes<br>
            <input type="checkbox" name="param_pd_visits" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Number of posted PD visits<br>
            <input type="checkbox" name="param_routines" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Number of posted routines<br>
                                           <br>
                        <input type="checkbox" name="param_only_finished_registrations" style="font-size:16px;margin-right:10px;margin-left:5px;" value="1" checked>Only include finished registrations<br>

            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
            <br><br>
            <button type="submit" id="save-AeExtQuiz" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Send</button>

        </form>

        <?php

        if($error){
            echo('<br><br><div class="error-summary alert alert-danger"><p>Please fix the following errors:</p><ul>
               '.$error.'
</ul></div>
');
        }

        
        ?>

    </div>


</div>
