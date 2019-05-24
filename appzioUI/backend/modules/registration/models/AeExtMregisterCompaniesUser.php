<?php

namespace backend\modules\registration\models;

use Yii;
use \backend\modules\registration\models\base\AeExtMregisterCompaniesUser as BaseAeExtMregisterCompaniesUser;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ae_ext_mregister_companies_users".
 */
class AeExtMregisterCompaniesUser extends BaseAeExtMregisterCompaniesUser
{


    public function upload(){

        if(!isset($this->importFile->tempName)){
            $this->addError('importFile', 'Faulty file');
            return false;
        }
      
        $content = file_get_contents($this->importFile->tempName);
        $lines = explode(chr(10), $content);

        if(count($lines) < 2){
            $this->addError('importFile', 'Seems like there is no data. Check that you are using the right kind of linefeeds (unix)');
            return false;
        }

        $headers = array_shift($lines);
        $headers = explode(';', $headers);

        if(count($headers) < 2) {
            $this->addError('importFile', 'Seems like there is no data. Check that you use semicolon (;) as your separator.');
            return false;
        }

        foreach ($headers as $header){
            if(!$this->hasAttribute($header)){
                $this->addError('importFile','Unknown column: '.$header);
            }
        }

        if($this->errors){
            return false;
        }

        $success = 0;
        $failure = 0;

        foreach ($lines as $line){
            $line = explode(';', $line);

            $obj = new AeExtMregisterCompaniesUser();
            $obj->app_id = isset($_SESSION['app_id']) ? $_SESSION['app_id'] : false;
            foreach ($headers as $key=>$header){
                if(isset($line[$key])){
                    $obj->$header = $line[$key];
                }

                if($header == 'email'){
                    $email = $line[$key];
                }
            }

            if(isset($email)){
                $test = AeExtMregisterCompaniesUser::findOne(['email' => $email]);
                if($test){
                    $this->addError('importFile','Employee '.$email .' already exists');
                    $failure++;
                    unset($email);
                    unset($test);
                    continue;
                } else {
                    @$obj->insert();
                }
            }

            if($obj->getErrors()){
                foreach ($obj->getErrors() as $error){
                    $this->addError('importFile','Error on line: '.serialize($error));
                }
            }

            if($obj->getPrimaryKey()){
                $success++;
            } else {
                $failure++;
            }
        }

        return ['success' => $success,'failure' => $failure];
    }

    public function export() {
        return self::find()->asArray()->all();
    }

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
