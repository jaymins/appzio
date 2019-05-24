<?php

use backend\modules\registration\models\AeExtMregisterCompaniesUser;
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
 * @var $uploadstatus
 * @var backend\models\UsergroupsUsersearch $searchModel
 */

$this->title = Yii::t('backend', 'Company Employee Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Company Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$home_url = Url::home();
$real_url = Url::to($home_url . 'users/usergroups-user/update');

$model = new AeExtMregisterCompaniesUser();

if (isset($actionColumnTemplates)) {
    $actionColumnTemplate = implode(' ', $actionColumnTemplates);
    $actionColumnTemplatestring = $actionColumnTemplate;
} else {
    Yii::$app->view->params['pageButtons'] = Html::a('<span class="glyphicon glyphicon-plus"></span> ' . 'New', ['create'], ['class' => 'btn btn-success']);
    $actionColumnTemplatestring = "{view} {update} {delete}";
}
$actionColumnTemplatestring = '<div class="action-buttons">' . $actionColumnTemplatestring . '</div>';
?>

<div class="giiant-crud usergroups-user-index">

    <h1>
        <?= Yii::t('backend', 'Import Company Employees') ?>

        <div style="display: inline-block; padding-left: 30px;">
        </div>

    </h1>

    <div>

        <?php

        if (is_array($uploadstatus)) {
            echo('<div style="font-size:22px;font-weight: bold;">');
            echo('Imported rows: <span style="color:green;">'.$uploadstatus['success'] .'</span>');
            if($uploadstatus['failure']){
                echo(' with <span style="color:red;">'.$uploadstatus['failure'] .' errors</span>');
            }

            echo('</div>');

        }

        ?>

        <br>
        <table>
            <tr>
                <td><b>Column</b></td>
                <td>&nbsp;&nbsp;</td>
                <td><b>Type</b></td>
            </tr>

            <?php $db = AeExtMregisterCompaniesUser::getTableschema();

            foreach ($db->columns as $column) {
                if ($column->name != 'id'
                    AND $column->name != 'app_id'
                    AND $column->name != 'play_id'
                    AND $column->name != 'registered'
                ) {
                    echo('<tr><td>' . $column->name . '</td><td>&nbsp;&nbsp;</td><td> (' . $column->type . ')</td></tr>');
                }
            }

            ?>

        </table>


        <br>
        IMPORTANT: Import file should be formatted as CsV, where item separator is ;<br>
        and first line is a header of column names
        <br><br>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'importFile')->fileInput() ?>

        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
        <br><br>
        <button type="submit" id="save-AeExtQuiz" class="btn btn-success"><span
                    class="glyphicon glyphicon-check"></span>Import
        </button>

        <?php ActiveForm::end() ?>

        <?php

        if ($error) {
            echo('<br><br><div class="error-summary alert alert-danger"><p>Please fix the following errors:</p>');

            foreach ($error as $one) {
                if (is_array($one)) {
                    foreach ($one as $single) {
                        echo('<ul>' . $single . '</ul>');
                    }
                } else {
                    print_r('<ul>' . $one . '</ul>');
                }
            }

            echo('</div>');
        }


        ?>

    </div>


</div>
