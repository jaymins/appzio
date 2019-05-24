<?php

namespace packages\actionMswipematch\Models;
use Bootstrap\Models\BootstrapModel;


use CException;
use Yii;


Trait Listings {


/*    public function joinForBookmarks(){
        $viewname = 'matching_'.$this->appid;
        return "LEFT JOIN ae_game_play_keyvaluestorage AS bookmarks ON $viewname.`play_id` = bookmarks.`value` AND `key` = 'bookmark' AND bookmarks.play_id = ".$this->playid;
    }

    public function selectForBookmarks(){
        return "bookmarks.id as `bookmark`";
    }*/

/*                JOIN ae_game_play_keyvaluestorage AS bookmarks ON $viewname.`play_id` = bookmarks.`value` AND bookmarks.`key` = 'bookmark' AND bookmarks.play_id = :playId
*/

    public function listMyMatches(){
        $viewname = 'matching_'.$this->appid;


        if(in_array('nickname', $this->pivot_variables)){
            $sorting[] = 'nickname';
        }

        if(in_array('name', $this->pivot_variables)){
            $sorting[] = 'name';
        }

        if(in_array('firstname', $this->pivot_variables) AND !isset($sorting)){
            $sorting[] = 'firstname';
        }

        if(in_array('real_name', $this->pivot_variables) AND !isset($sorting)){
            $sorting[] = 'real_name';
        }

        if(isset($sorter)){
            $sorter = 'ORDER BY '.implode(',', $sorting);
        } else {
            $sorter = '';
        }

        $sql = "SELECT * FROM 
                ae_game_play_keyvaluestorage AS storage
                LEFT JOIN $viewname ON storage.`value` = $viewname.play_id
                WHERE storage.play_id = :playId AND $viewname.play_id $sorter IS NOT NULL
                ";

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
        ))->queryAll();

        if(!$rows){
            return array();
        }

        /* get nickname and sort by it */
        foreach($rows as $key=>$row){
            $rows[$key]['nickname'] = $this->getNickname($row);
        }

        $rows = $this->array_msort($rows, array('nickname'=>SORT_ASC));

        foreach($rows as $row){
            $pointer = $row['key'];
            $output[$pointer][] = $row;
        }

        $sql = "SELECT * FROM 
                ae_game_play_keyvaluestorage AS storage
                LEFT JOIN $viewname ON storage.play_id = $viewname.play_id
                WHERE storage.`value` = :playId AND storage.`key` = 'matches'
                GROUP BY $viewname.play_id
                ";

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
        ))->queryAll();

        foreach($rows as $row){
            $output['like_me'][] = $row;
        }

        $sql = "SELECT * FROM 
                ae_game_play_keyvaluestorage AS storage
                LEFT JOIN $viewname ON storage.play_id = $viewname.play_id
                WHERE storage.`value` = :playId AND storage.`key` = 'un-matches'
                GROUP BY $viewname.play_id
                ";

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
        ))->queryAll();

        foreach($rows as $row){
            $output['un-matches'][] = $row;
        }

        /* add a field bookmarked */
        if(isset($output['bookmark'])){
            foreach($output['bookmark'] as $bookmark){
                $bookmarks[] = $bookmark['play_id'];
            }
        } else {
            $bookmarks = array();
        }

        foreach($output as $groupid=>$group){
            foreach($group as $key=>$value){
                if(in_array($value['play_id'], $bookmarks)){
                    $output[$groupid][$key]['is_bookmarked'] = true;
                } else {
                    $output[$groupid][$key]['is_bookmarked'] = false;
                }
            }
        }

        /* add a field is_liked */
        if(isset($output['matches'])) {
            foreach ($output['matches'] as $match) {
                $matches[] = $match['play_id'];
            }
        } else{
            $matches = array();
        }

        foreach($output as $groupid=>$group){
            foreach($group as $key=>$value){
                if(in_array($value['play_id'], $matches)){
                    $output[$groupid][$key]['is_liked'] = true;
                } else {
                    $output[$groupid][$key]['is_liked'] = false;
                }
            }
        }

        /* add chat info (needs to be turned on separately as not all themes require it) */
        if($this->include_chat === true){
            if(isset($output['two-way-matches'])) {
                foreach ($output['two-way-matches'] as $key=>$match) {
                    $chatid = $this->getTwoWayChatId($match['play_id']);

                    if($chatid){
                        $msg = $this->getChatLastMsg($chatid);
                        $output['two-way-matches'][$key]['chat'] = $msg;

                        if(isset($msg['chat_message_is_read']) AND $msg['chat_message_is_read'] == 0){
                            $output['two-way-matches'][$key]['unread'] = true;
                        } else {
                            $output['two-way-matches'][$key]['unread'] = false;
                        }
                    }
                }
            }
        }

        return $output;

    }

    public function getChatUnread($chatid){
        $sql = "SELECT count(ae_chat_messages.id) as counter,context_key,ae_chat.id,ae_chat_messages.id,ae_chat_messages.chat_id FROM ae_chat
                      LEFT JOIN ae_chat_messages ON ae_chat.id = ae_chat_messages.chat_id
                      WHERE context_key = :id AND chat_message_is_read = 0 AND author_play_id <> :playId LIMIT 1";

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
            ':id' => $chatid
        ))->queryAll();

        if(isset($rows[0]['counter']) AND $rows[0]['counter'] > 0){
            return true;
        }

        return false;
    }

    public function getChatLastMsg($chatid){
        $sql = "SELECT context_key,ae_chat.id,ae_chat_messages.id,ae_chat_messages.chat_id,chat_message_text AS msg,chat_message_timestamp AS msgtime,chat_message_is_read FROM ae_chat
                      LEFT JOIN ae_chat_messages ON ae_chat.id = ae_chat_messages.chat_id
                      WHERE context_key = :id AND author_play_id <> :playId ORDER BY ae_chat_messages.id DESC LIMIT 1 ";

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
            ':id' => $chatid
        ))->queryAll();

        if(isset($rows[0])){
            return $rows[0];
        }

        return false;
    }


    public function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }




}