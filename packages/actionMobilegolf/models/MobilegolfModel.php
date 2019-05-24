<?php

class MobilegolfModel extends ArticleModel {


    /* this will record a purchase for certain club */
    public static function addPurchase($gid,$playid,$placeid){
        $vars = AeplayVariable::getArrayOfPlayvariables($playid);

        if(isset($vars['golf_course_purchases']) AND $vars['golf_course_purchases']){
            $courses = json_decode($vars['golf_course_purchases'],true);
            $courses[$placeid] = true;
            AeplayVariable::updateWithName($playid,'golf_course_purchases',json_encode($courses),$gid);
        }
    }

    public static function handlePurchase($gid,$playid,$vars,$menuid){
        $id = false;
        $price = false;

        if(stristr($menuid,'inapp-mapforonecourse-')){
            $var = 'purchase_mapforonecourse';
            $id = str_replace('inapp-mapforonecourse-','',$menuid);
            $price = 3.99;
        } elseif($menuid == 'inapp-discountgreenfee'){
            $var = 'purchase_discountgreenfee';
        } elseif(stristr($menuid,'inapp-discountandmap-')){
            $var = 'discount_discountandmap';
            $id = str_replace('inapp-mapforonecourse-','',$menuid);
        } elseif($menuid == 'inapp-restore'){
            $var = 'discount_discountandmap';
            if(isset($vars[$var]) AND $vars[$var]){
                return true;
            }

            $var = 'purchase_discountgreenfee';
            if(isset($vars[$var]) AND $vars[$var]){
                return true;
            }

            return false;
        } else {
            return false;
        }

        /* save information about the purchase */
        $obj = new Aepurchase();
        $obj->game_id = $gid;
        $obj->play_id = $playid;
        $obj->price = $price;
        $obj->product_title = 'Map for one course';
        $obj->status = 1;
        $obj->platform = isset($vars['system_source']) ? $vars['system_source'] : 'unknown';
        $obj->insert();

        self::updatePurchaseVariables($gid,$playid,$id,$vars,$var);
        return false;

    }

    public static function updatePurchaseVariables($gid,$playid,$id=false,$vars,$var){
        if(isset($vars[$var]) AND $vars[$var]){
            if(isset($id)){
                self::addPurchase($gid,$playid,$id);
                AeplayVariable::updateWithName($playid,'purchase_mapforonecourse',0,$gid);
                AeplayVariable::updateWithName($playid,'discountandmap',0,$gid);
            }

            return true;
        }
    }

    public static function purchaseButton($type,$id){

        $onclick = new stdClass();
        $onclick->open_sync = 1;

        switch($type) {
            case '{#free#}':
                $onclick->id = 'free-claim-code-'.$id;
                $onclick->action = 'submit-form-content';
                break;

            case '€3.99':
                $onclick->id = 'inapp-mapforonecourse-'.$id;
                $onclick->action = 'inapp-purchase';
                $onclick->product_id_ios = 'mapforonecourse';
                $onclick->product_id_android = 'mapforonecourse';
                $onclick->producttype_android = 'inapp';
                $onclick->producttype_ios = 'inapp';

                break;

            case '€5.99':
                $onclick->id = 'inapp-discountgreenfee';
                $onclick->action = 'inapp-purchase';
                $onclick->product_id_ios = 'discountgreenfee';
                $onclick->product_id_android = 'discountgreenfee';
                $onclick->producttype_android = 'inapp';
                $onclick->producttype_ios = 'inapp';
                break;

            case '€7.99':
                $onclick->id = 'inapp-discountandmap-'.$id;
                $onclick->action = 'inapp-purchase';
                $onclick->product_id_ios = 'discountandmap';
                $onclick->product_id_android = 'discountandmap';
                $onclick->producttype_android = 'inapp';
                $onclick->producttype_ios = 'inapp';
                break;

            case 'restore':
                $onclick->id = 'inapp-restore';
                $onclick->action = 'inapp-restore';
                break;
        }

        return $onclick;

    }


}