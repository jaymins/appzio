<?php

/*

    This is called by api/apps/recipeasync

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');

class SaveVarsModel {

    public $menus;
    public $tabs;
    public $vars;
    public $gid;

    public $submitvariables;

    public static function saveVars($actionid,$playid)
    {

        $count = 0;
        $pic = false;

        while ($pic == false AND $count < 8) {

            $allvars = Aeplay::getPlayVars($playid);

            if ($allvars AND isset($allvars['upload_temp']) AND $allvars['upload_temp']) {
                $pic = $allvars['upload_temp'];
                // $comment = $allvars['comment_temp'];
            } elseif (isset($allvars) AND isset($allvars['comment_temp']) AND $allvars['comment_temp']) {
                $comment = $allvars['comment_temp'];
            }

            sleep(2);
            $count++;
        }

        if(isset($pic) and isset($comment) AND $pic){
            $play = Aeplay::model()->findByPk($playid);
            $action = AeplayAction::model()->findByPk($actionid);
            SaveVarsModel::saveEntry($pic,$comment,$play->user_id,$action->action_id);
            AeplayVariable::deleteWithName($playid,'upload_temp',$play->game_id);
            AeplayVariable::deleteWithName($playid,'comment_temp',$play->game_id);
        } elseif(isset($comment) AND $comment) {
            $play = Aeplay::model()->findByPk($playid);
            $action = AeplayAction::model()->findByPk($actionid);
            AeplayVariable::deleteWithName($playid,'upload_temp',$play->game_id);
            AeplayVariable::deleteWithName($playid,'comment_temp',$play->game_id);
            SaveVarsModel::saveEntry('photo-placeholder.jpg',$comment,$play->user_id,$action->action_id);
        }

        return true;
    }

    private static function saveEntry($pic,$comment,$user,$actionid){

        $action = Aeaction::model()->findByPk($actionid);
        $config = json_decode($action->config);

        if(isset($config->user_images) AND $config->user_images){
            $arr = (array)json_decode($config->user_images);
            $new['name'] = $user;
            $new['date'] = date("r");
            $new['pic'] = $pic;
            $new['comment'] = $comment;
            $new['user'] = $user;
            $arr[] = $new;
        } else {
            $arr = array();
            $new['name'] = $user;
            $new['date'] = date("r");
            $new['pic'] = $pic;
            $new['comment'] = $comment;
            $new['user'] = $user;
            $arr[] = $new;
        }

        $arr = json_encode($arr);
        $obj = Aeaction::model()->findByPk($actionid);
        $config->user_images = $arr;
        $obj->config = json_encode($config);
        $obj->update();

        return true;

    }

}