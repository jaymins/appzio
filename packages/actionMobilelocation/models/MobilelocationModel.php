<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

class MobilelocationModel extends ArticleModel {


    static function geoTranslate($vars,$gid,$playid){

        if(!isset($vars['lat']) OR !isset($vars['lon'])){
            return false;
        }

        $info = ThirdpartyServices::geoAddressTranslation($vars['lat'],$vars['lon'],$gid);

        if(isset($info['country'])){
            AeplayVariable::saveVariablesArray($info,$playid,$gid);
            self::updateMobileMatching($playid,$vars);
        } else {
            return false;
        }
    }

    static function addressTranslation($gid,$playid,$country,$city,$address=false){

        $info = ThirdpartyServices::addressToCoordinates($gid,$country,$city,$address);

        if(isset($info['lat']) AND isset($info['lon'])){
            AeplayVariable::saveVariablesArray($info,$playid,$gid);
            self::updateMobileMatching($playid,$info);
        } else {
            return false;
        }
    }

    static function updateMobileMatching($playid,$vars){
        Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');
        $obj = MobilematchingModel::model()->findByAttributes(array('play_id' => $playid));
        if(is_object($obj)){
            $obj->lat = $vars['lat'];
            $obj->lon = $vars['lon'];
            $obj->update();
        }

    }


}