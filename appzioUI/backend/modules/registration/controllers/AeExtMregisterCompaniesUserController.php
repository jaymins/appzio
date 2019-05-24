<?php
/**
 * /Users/trailo/dev/aecore/app/appzioUI/console/runtime/giiant/49eb2de82346bc30092f584268252ed2
 *
 * @package default
 */


namespace backend\modules\registration\controllers;

use backend\modules\registration\models\AeExtMregisterCompaniesUser;
use backend\modules\registration\search\AeExtMregisterCompaniesUser as AeExtMregisterCompaniesUserSearch;
use boundstate\importexport\ExcelWriter;
use dmstr\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the class for controller "AeExtMregisterCompaniesUserController".
 */
class AeExtMregisterCompaniesUserController extends \backend\modules\registration\controllers\base\AeExtMregisterCompaniesUserController
{

    public $importFile;

    public function actionExport(){
        $writer = new ExcelWriter(['source' => AeExtMregisterCompaniesUser::className()]);
        $filename = $writer->write('Excel2007');
        \Yii::$app->response->sendFile($filename, 'contacts.xlsx')->send();
    }



    public function actionImport(){

        $model = new AeExtMregisterCompaniesUser();
        $status = false;

        if (\Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            $status = $model->upload();
        }

        $searchModel  = new AeExtMregisterCompaniesUserSearch;
        $dataProvider = $searchModel->search($_GET);

        Tabs::clearLocalStorage();

        Url::remember();
        \Yii::$app->session['__crudReturnUrl'] = null;

        return $this->render('import', [
            'dataProvider' => $dataProvider,
            'error' => $model->getErrors(),
            'uploadstatus' => $status,
            'searchModel' => $searchModel,
        ]);

    }


}
