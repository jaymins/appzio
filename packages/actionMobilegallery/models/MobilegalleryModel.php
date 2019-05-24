<?php



class MobilegalleryModel extends ArticleModel {

    /* this should be called by aetask */

    public static function saveVars($actionid,$playid,$gid,$user,$galleryid)
    {

        $varvalue = false;
        $count = 0;
        $pic = false;

        $vars = AeplayVariable::getArrayOfPlayvariables($playid);

        if (isset($allvars) AND isset($allvars['submit_pic_temp']) AND $allvars['submit_pic_temp']) {
            $pic = $allvars['submit_pic_temp'];
        } else {
            return false;
        }

        $playaction = AeplayAction::model()->findByPk($actionid);
        $action = Aeaction::model()->findByPk($playaction->action_id);
        $config = json_decode($action->config);

        $sourcebranch = $config->source_branch;
        $type = Aeactiontypes::model()->findByAttributes(array('shortname' => 'mobilegallery'));

        $count = Aeaction::model()->countByAttributes(array('branch_id' => $sourcebranch));
        $count++;

        $uservars = AeplayVariable::getArrayOfPlayvariables($playid);
        $trigger = Aetrigger::model()->findByAttributes(array('shortname'=>'branchactive'));

        if($pic) {
            $source = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/documents/games/' .$gid .'/user_original_images/';
            $target = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'/documents/games/' .$gid .'/original_images/';
            copy($source .basename($pic), $target .basename($pic));

            $subject = isset($uservars['submit_subject_temp']) ? $uservars['submit_subject_temp']: '';
            $comment = isset($uservars['submit_comment_temp']) ? $uservars['submit_comment_temp']: '';
            $params['width'] = 640;
            $params['height'] = 640;

            $img = new ImagesController('submitcontroller');
            $img->gid = $gid;
            $imagefile = $img->getAsset(basename($pic));

            AeplayVariable::updateWithName($playid,'featured_image',$pic,$gid);

            $action = new Aeaction();
            $action->branch_id = $sourcebranch;
            $action->points = 0;
            $action->type_id = $type->id;
            $action->active = 1;
            $action->name = $subject;
            $action->order = $count;
            $action->trigger_id = $trigger->id;

            $conf = new StdClass();
            $conf->subject = $subject;
            $conf->msg = $comment;
            $conf->comment = $comment;
            $conf->mode = 'entry';
            $conf->image_portrait = basename($pic);
            $conf->image_menu = basename($pic);
            $conf->backarrow = 1;
            $conf->hide_menubar = 0;
            $conf->hide_subject = 0;
            $conf->dynamic = 1;
            $conf->source_branch = $sourcebranch;
            $conf->date = date("r");
            $conf->likes = 1;
            $conf->chat_content = '';
            $conf->gallery_id = $galleryid;

            $conf->user = $user;
            $action->config = json_encode($conf);
            $action->insert();

            AeplayVariable::deleteWithName($playid,'submit_comment_temp',$gid);
            AeplayVariable::deleteWithName($playid,'submit_subject_temp',$gid);
            AeplayVariable::updateWithName($playid,'gallery_update',$action->id,$gid);
            Appcaching::removeActionCache($playid,$actionid);

            return true;
        }

        return false;

    }

    public static function onActionComplete($playaction,$action,$userid,$gid){
        AeplayVariable::deleteWithName($playaction->play_id,'gallery_update',$gid);
    }


    public static function createImagesCache($actionid,$data){
        Yii::app()->cache->set( $actionid .'-' .'galleryitems',$data);
        return true;
    }

    /* this will pull the gallery cache, see the correct cache and set it
    for action so that it can be referred only with actionid */
    public static function getEntry($referring_action,$menuid,$actionid){
        $pointer = $referring_action;
        $cache = Yii::app()->cache->get( $pointer .'-' .'galleryitems');

        if($cache AND isset($cache[$menuid])){
            $data = $cache[$menuid];
            $cached['data'] = $data;
            $cached['menuid'] = $menuid;
            Yii::app()->cache->set( $actionid .'-' .'tempcache-content',$cached);
            return $cache[$menuid];
        }

        return false;
    }

    public static function getGalleryEntry($actionid){
        $cache = Yii::app()->cache->get( $actionid .'-' .'tempcache-content');
        if($cache){
            return $cache;
        } else {
            return false;
        }
    }

    public static function flushCaceh($actionid){

        Yii::app()->cache->delete( $actionid .'-' .'galleryitems');
        return true;
    }



}