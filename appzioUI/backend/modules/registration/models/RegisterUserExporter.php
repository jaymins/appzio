<?php

namespace backend\modules\registration\models;

use boundstate\importexport\ExportInterface;
use boundstate\importexport\ImportInterface;
use Yii;
use \backend\modules\registration\models\base\AeExtMregisterCompaniesUser as BaseAeExtMregisterCompaniesUser;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mregister_companies_users".
 */
class RegisterUserExporter extends \Yii\db\ActiveRecord implements ImportInterface, ExportInterface
{

    public function import($reader, $row, $data)
    {
        if ($row == 0) {
            if ($data != ['Email']) {
                $reader->addError($row, 'Invalid headers.');
                return false;
            }
            // Skip header row
            return true;
        }

        // Create contact from data
        //$contact = new Contact;
    }

    public function export() {
        return AeExtMregisterCompaniesUser::find()->asArray()->all();
    }
}
