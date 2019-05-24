<?php
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');
class TattooMobilematchingModel extends MobilematchingModel {

    public function getMyMatches(){
        $sql = "SELECT context_key FROM ae_chat_messages
                LEFT JOIN ae_chat ON ae_chat.id = ae_chat_messages.chat_id
                  INNER JOIN (
                        SELECT id, MAX(chat_message_timestamp) AS maxsign FROM ae_chat_messages GROUP BY id
                      ) ms ON ae_chat_messages.id = ms.id AND chat_message_timestamp = maxsign
                WHERE context_key LIKE :playId
                ORDER BY ae_chat_messages.chat_message_timestamp DESC
                ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playId' => '%' . $this->playid_thisuser . '%'
            ))
            ->queryAll();


        $output = array();

        foreach ($rows as $row){

            $parts = explode('-chat-',$row['context_key']);
            if (count($parts) > 1) {
                foreach ($parts as $part) {
                    if (!empty($part) && $part != $this->playid_thisuser) {
                        $output[$part] =  $part;
                    }
                }
            }
        }

        return $output;
    }


}