<?php

namespace packages\actionMswipematch\Models;
use Bootstrap\Models\BootstrapModel;

use CException;
use Yii;

Trait MatchingUser {

    public static function locateUserWithIp(){

        if($_SERVER['REMOTE_ADDR'] == '127.0.0.1' OR  $_SERVER['REMOTE_ADDR'] == '::1'){
            $address = '93.152.145.119';
        } else {
            $address = $_SERVER['REMOTE_ADDR'];
        }

        $data = \ThirdpartyServices::locateWithIp($address);

        if(is_object($data)){
            return $data;
        } else {
            return false;
        }
    }

    public static function updateUsersLocation($playid,$gid){

        $location = self::locateUserWithIp();

        if(!isset($location->latitude) OR !isset($location->longitude) OR !isset($location->city) OR !isset($location->country_name)){
            return false;
        }

        \AeplayVariable::updateWithName($playid,'lat',$location->latitude,$gid);
        \AeplayVariable::updateWithName($playid,'lon',$location->longitude,$gid);
        \AeplayVariable::updateWithName($playid,'city',$location->city,$gid);
        \AeplayVariable::updateWithName($playid,'zip',$location->zip_code,$gid);
        \AeplayVariable::updateWithName($playid,'country',$location->country_name,$gid);

        $obj = \MobilematchingModel::model()->findByAttributes(array('play_id' => $playid));

        if(!is_object($obj)){
            return false;
        }
        $obj->lon = $location->longitude;
        $obj->lat = $location->latitude;
        $obj->update();

        return true;
    }


    public function turnUserToItem($reverse=false,$file){

        $obj = \MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_thisuser));
        $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);

        if($reverse AND isset($vars['city_selected']) AND isset($vars['country_selected']) AND $vars['city_selected'] AND $vars['country_selected']){
            \Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            \MobilelocationModel::addressTranslation($this->appid,$this->playid_thisuser,$vars['country_selected'],$vars['city_selected']);
            $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } elseif($reverse AND isset($vars['city']) AND isset($vars['country']) AND $vars['city'] AND $vars['country']){
            \Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            \MobilelocationModel::addressTranslation($this->appid,$this->playid_thisuser,$vars['country'],$vars['city']);
            $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        }

        if(!is_object($obj) AND isset($vars['lat']) AND isset($vars['lon'])){
            $obj = new \MobilematchingModel();
            $obj->game_id = $this->appid;
            $obj->play_id = $this->playid_thisuser;
            $obj->lat = $vars['lat'];
            $obj->lon = $vars['lon'];

            if(isset($vars['score'])) { $obj->score = $vars['score']; }
            if(isset($vars['gender'])) { $obj->gender = $vars['gender']; }
            if(isset($vars['education'])) { $obj->education = $vars['education']; }
            if(isset($vars['hindu_caste'])) { $obj->hindu_caste = $vars['hindu_caste']; }
            if(isset($vars['role'])) {
                $obj->role = $vars['role'];
                if($vars['role'] == 'brand'){
                    $obj->match_always = 1;
                }
            }

            $obj->insert();
        }

        $this->obj_thisuser = \MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_thisuser));

        //self::updateUsersLocation($playid,$gid);

        return true;
    }


}