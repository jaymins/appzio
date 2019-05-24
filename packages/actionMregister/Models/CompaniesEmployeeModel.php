<?php


namespace packages\actionMregister\Models;

use CActiveRecord;


/**/
class CompaniesEmployeeModel extends CActiveRecord
{
    public $id;
    public $app_id;
    public $company_id;
    public $play_id;
    public $registered;
    public $firstname;
    public $lastname;
    public $department;
    public $email;
    public $phone;
    public $registered_date;


    public function tableName()
    {
        return 'ae_ext_mregister_companies_users';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'company' => array(self::BELONGS_TO, 'packages\actionMregister\Models\CompaniesModel', 'company_id')
        );
    }

    public static function saveRegistration($email,$playid,$app_id){
        $obj = CompaniesEmployeeModel::model()->with('company')->findByAttributes([
            'email' => $email
        ]);

        if(!$obj){
            $obj = new CompaniesEmployeeModel();
            $obj->email = $email;
            $obj->company_id = self::getCompanyIdByDomain($email,$app_id);
            $obj->play_id = $playid;
            $obj->registered_date = date('Y-m-d H:i:s');
            $obj->app_id = $app_id;
            $obj->registered = 1;
            $obj->insert();
        } else {
            $obj->play_id = $playid;
            $obj->registered_date = date('Y-m-d H:i:s');
            $obj->registered = 1;
            $obj->update();
        }
    }

    public static function getCompanyIdByDomain($email,$app_id){
        $parts = explode('@', $email);

        if(!isset($parts[1])){
            return null;
        }

        $company = CompaniesModel::model()->findByAttributes([
            'domain' => $parts[1],
            'app_id' => $app_id
        ]);

        if(!$company){
            return null;
        }

        return $company->id;
    }

    public static function checkValidity($email,$app_id){

        if(self::admitByCompany($email,$app_id)){
            return true;
        }

        $obj = CompaniesEmployeeModel::model()->with('company')->findByAttributes([
            'email' => $email,'app_id' => $app_id
        ]);

        if(!isset($obj->company->id)){
            return false;
        }

        if(!self::checkUserLimit($obj->company->id,$obj->company->user_limit)){
            return false;
        }

        return self::checkExpiry($obj->company);

    }

    private static function admitByCompany($email,$app_id){
        $parts = explode('@', $email);

        if(!isset($parts[1])){
            return false;
        }

        $company = CompaniesModel::model()->findByAttributes([
            'subscription_active' => 1,
            'admit_by_domain' => 1,
            'app_id' => $app_id,
            'domain' => $parts[1]
            ]);

        if(!$company){
            return false;
        }

        return self::checkExpiry($company);
    }

    private static function checkExpiry($company)
    {

        if(!isset($company->subscription_expires)){
            return false;
        }

        $date = strtotime($company->subscription_expires);

        if($date > time()){
            return true;
        }

        return false;
    }


    private static function checkUserLimit($id,$limit)
    {

        $count = CompaniesEmployeeModel::model()->countByAttributes([
            'registered' => 1,
            'company_id' => $id
        ]);

        if($count < $limit){
            return true;
        }

        return false;

    }



}