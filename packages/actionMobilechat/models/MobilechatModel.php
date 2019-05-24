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

class MobilechatModel extends ArticleModel {


    /* this is a special function, which will check whether
    the action should be updated or if the cache is still valid.
    this will make any refresh calls faster, as we don't have to do the
    expensive initialisation */

    public static function checksumChecker($args){

        $md5 = self::checkQuery($args['chat_id']);
        
        if($md5 == $args['checksum']){
            return true;
        }

        return false;

    }

    public static function SetChecksumChecker($playid,$userid){
        $name = $playid . '-' . $userid .'-chattemp2';
        $cache = Appcaching::getGlobalCache($name);
        
        if(isset($cache['chat_id'])){
            $out['includepath'] = 'application.modules.aelogic.packages.actionMobilechat.models.MobilechatModel';
            $out['class'] = 'MobilechatModel';
            $out['method'] = 'checksumChecker';
            $out['chat_id'] = $cache['chat_id'];
            $out['checksum'] = self::checkQuery($cache['chat_id']);
            return $out;
        }
    }

    public static function checkQuery($id){
        $sql = "SELECT message.*, attachment.chat_attachment_path
                FROM ae_chat_messages as message
                LEFT OUTER JOIN ae_chat_attachments as attachment
                ON message.id = attachment.chat_message_id
                WHERE message.chat_id = :chat_id
                GROUP BY message.id
                ORDER BY `id` ASC";


        $messages = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( array(':chat_id' => $id) )
            ->queryAll();

        return md5(serialize($messages));

    }



}