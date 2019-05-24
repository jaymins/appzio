<?php

namespace packages\actionMswipematch\Models;
use Bootstrap\Models\BootstrapModel;

use CException;
use Yii;


trait Matches {

    public function getPointer(){
        $cachename = $this->actionid .'-' .'pointer';
        return \Appcaching::getGlobalCache($cachename);
    }

    public function setPointer($foreignplay){
        $cachename = $this->actionid .'-' .'pointer';
        \Appcaching::setGlobalCache($cachename,$foreignplay);
    }


    public function getNumberOfMatches($other_user_play_id=false){

        if($other_user_play_id){
            $this->obj_datastorage->play_id = $other_user_play_id;
            $count = count($this->obj_datastorage->get('two-way-matches'));
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            return $count;
        }

        return count($this->obj_datastorage->get('two-way-matches'));
    }



    /* saves information about the match & matches both ways */
    public function saveMatch(){

        $this->obj_datastorage->set('matches',$this->playid_otheruser);
        $this->obj_datastorage->play_id = $this->playid_otheruser;

        $nickname = $this->getSavedVariable('username');

/*        if($this->obj_datastorage->valueExists('un-matches',$this->playid_thisuser)){
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            $this->obj_datastorage->delete
        }*/

        if($this->obj_datastorage->valueExists('matches',$this->playid_thisuser)){
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            $this->saveTwoWayMatch();
            return true;
        } else {
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            return false;
        }
    }

    public function saveSuperhot( $superhot_var ) {

        $this->obj_datastorage->set( 'superlikes', $this->playid_otheruser );
        $this->obj_mobilematchingmeta->decrementValue( $superhot_var );

        return true;
    }

    public function reportUser(){
        $this->skipMatch();

        if(isset($this->obj_otheruser->flag)){
            $this->obj_otheruser->flag = $this->obj_otheruser->flag+1;
        } else {
            return false;
        }

        $this->obj_otheruser->update();
        $this->removeTwoWayMatches();

        return true;
    }

    public function blockUser(){
        $this->skipMatch();
        $this->removeTwoWayMatches();
        $this->obj_datastorage->set('blocked',$this->playid_otheruser);
        return true;
    }

    public function skipMatch( $delete_matches = false, $machine_added = false ){

        if($machine_added){
            $this->obj_datastorage->set('un-matches-auto-',$this->playid_otheruser);
        } else {
            $this->obj_datastorage->set('un-matches',$this->playid_otheruser);
        }

        if ( $delete_matches ) {
            $this->removeTwoWayMatches();
        }

        $this->removeFromCurrentMatches();
        return true;
    }

    /* used by apps that give "daily selection" (sila atm) */
    public function removeFromCurrentMatches(){
        $matches = json_decode($this->getSavedVariable('current_matches'),true);
        $out = array();

        if($matches){
            foreach($matches as $match){
                if($this->playid_otheruser != $match['play_id']){
                    $out[] = $match;
                }
            }

            $out = json_encode($out);
            $this->saveVariable('current_matches',$out);
        }

        $varcontent = \AeplayVariable::fetchWithName($this->playid_otheruser,'current_matches',$this->appid);
        $varcontent = json_decode($varcontent);
        $out = array();

        if(is_object($varcontent)){
            foreach($varcontent as $match){
                if($this->playid_thisuser != $match['play_id']){
                    $out[] = $match;
                }
            }

            \AeplayVariable::updateWithName($this->playid_otheruser,'current_matches',$out,$this->appid);
        }
    }

    public function makeSuggested() {
        $this->obj_datastorage->set('suggested', $this->playid_otheruser);
        return true;
    }

    public function getTwoWayMatchStatus(){
        return $this->obj_datastorage->valueExists('two-way-matches',$this->playid_otheruser);
    }

    public function saveTwoWayMatch(){

        $exist = $this->obj_datastorage->valueExists('two-way-matches',$this->playid_otheruser);

        /* this will delete possible unmatches (if user is first unmatched and then matched */
        @\AeplayDatastorage::model()->deleteAllByAttributes(array('play_id' => $this->playid,'key' => 'un-matches', 'value' => $this->playid_otheruser));

        if(!$exist){
            $this->obj_datastorage->set('two-way-matches',$this->playid_otheruser);
            $this->obj_datastorage->play_id = $this->playid_otheruser;
            $this->obj_datastorage->set('two-way-matches',$this->playid_thisuser);

            /* by default the play id should always be of this play, hence we save it right back */
            $this->obj_datastorage->play_id = $this->playid_thisuser;

            if($this->send_notification){
                $this->saveNotificationForMatch();
            }
        }

    }


    public function resetMatches( $reset_two_way_matches = true ){

        $this->obj_datastorage->del('un-matches');
        $this->obj_datastorage->del('un-matches-auto');
        $this->obj_datastorage->del('matches');

        if ( $reset_two_way_matches ) {
            $this->obj_datastorage->del('two-way-matches');
            $this->obj_datastorage->del('notifications-match');
        }

        /* todo: support for hard reset where also the information from the other end is deleted */
    }

    public function resetUnmatches(){
        $this->obj_datastorage->del('un-matches');
        $this->obj_datastorage->del('un-matches-auto');
    }

    public function resetAutoUnmatches(){
        $this->obj_datastorage->del('un-matches-auto');
    }

    public function getMyMatches(){
        return $this->obj_datastorage->get('two-way-matches');
    }


    public function isUserStillValid( $row, $validity ) {

        if ( !isset($row['timestamp']) OR stristr($row['timestamp'], '0000') ) {
            return true;
        }

        $stamp = strtotime($row['timestamp']);
        $time = strtotime( $this->server_time_offset ); // This we need to adjust depending on the server's settings
        $diff = $time - $stamp;

        // If is valid
        if ( $diff < $validity ) {
            return true;
        }

        $storage = new \AeplayKeyvaluestorage();
        $storage->deleteByPk( $row['id'] );

    }

    public function checkIfBothMatched( $play_id, $value ) {
        $model = new \AeplayKeyvaluestorage();

        $matched = $model->findByAttributes(array(
            'play_id' => $play_id,
            'key' => 'two-way-matches',
            'value' => $value,
        ));

        if ( $matched ) {
            return true;
        }

        return false;
    }

    public function checkIfUnmatched( $play_id, $value ) {
        $model = new \AeplayKeyvaluestorage();

        $is_unmatched = $model->findByAttributes(array(
            'play_id' => $value,
            'key' => 'un-matches',
            'value' => $play_id,
        ));

        if ( $is_unmatched ) {
            return true;
        }

        return false;
    }

    public function getCount($what){

        switch($what){
            case 'matches':
                $sql = "SELECT count(ae_game_play_keyvaluestorage.id) AS totalcount FROM `ae_game_play_keyvaluestorage`
                        LEFT JOIN ae_game_play ON ae_game_play_keyvaluestorage.play_id = ae_game_play.id
                        WHERE `key` = 'two-way-matches'
                        AND ae_game_play.game_id =:gameId";

                $rows = Yii::app()->db
                    ->createCommand($sql)
                    ->bindValues(array(
                        ':gameId' => $this->appid,
                    ))
                    ->queryAll();

                if(isset($rows[0]['totalcount'])){
                    return round($rows[0]['totalcount'] / 2,0);
                } else {
                    return 0;
                }

                break;

            case 'messages':
                $sql = "SELECT count(ae_chat_messages.id) AS totalcount FROM `ae_chat_messages`
                        LEFT JOIN ae_game_play ON ae_chat_messages.author_play_id = ae_game_play.id
                        WHERE ae_game_play.game_id =:gameId";

                $rows = Yii::app()->db
                    ->createCommand($sql)
                    ->bindValues(array(
                        ':gameId' => $this->appid,
                    ))
                    ->queryAll();

                if(isset($rows[0]['totalcount'])){
                    return $rows[0]['totalcount'];
                } else {
                    return 0;
                }

                break;

        }

        return true;
    }

    public static function addSimilarity($all,$value,$column_name = 'value'){
        foreach($all as $key=>$user){
            $val = json_decode($user[$column_name],true);
            $all[$key]['similarity'] = self::arrayCompare($value,$val);
        }

        return $all;
    }

    public static function arrayCompare($array1,$array2){

        $count = count($array1);
        $similarity = 0;

        if(empty($array1) OR empty($array2)){
            return 0;
        }

        foreach($array1 as $key=>$value){
            if(isset($array2[$key])){
                if($array2[$key] == $value){
                    if($value == 1){
                        $similarity++;
                    }
                }
            }
        }

        $similarity = $similarity/$count;
        return $similarity;
    }

    public function getUsersWhoHaveMatchedMe($validity){
        $inbox = $this->getMyInbox($validity, 'suggested');

        if ( empty($inbox) ) {
            return false;
        }

        $values = implode(',',$inbox);

        $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $include_ids = false;
        if ( !empty($values) ) {
            $include_ids = "AND ae_ext_mobilematching.play_id IN ($values)";
        }

        $sex = 'man';

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT
                    *
                    ,((ACOS(SIN(@orig_lat * PI() / 180) * SIN(`lat` * PI() / 180) + COS(@orig_lat * PI() / 180) * COS(`lat` * PI() / 180) * COS((@orig_long - `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`
                    FROM `ae_ext_mobilematching`
                    # LEFT JOIN ae_game_play_variable ON ae_ext_mobilematching.play_id = ae_game_play_variable.id
                    WHERE
                    (
                      `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
                      AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
                    )

                    AND play_id <> $this->playid_thisuser
                    AND game_id = $this->appid
                    AND gender = '$sex'
                    $include_ids
                    ORDER BY `distance` ASC
                    limit 100
        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        return $rows;

    }

    /*
    * Clean up the Key-value storage table
    * This method would essentially remove all unnecessary fields, created during the matching process
    */
    public function removeTwoWayMatches($blockChat = true) {
        $storage = new \AeplayKeyvaluestorage();

        // Remove two-way-matches
        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'two-way-matches'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'matches'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_otheruser, // Remote user play
            'value' => $this->playid_thisuser,
            'key' => 'two-way-matches'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_otheruser, // Remote user play
            'value' => $this->playid_thisuser,
            'key' => 'matches'
        ));


        /* clean any notifications */
        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_otheruser, // Remote user play
            'value' => $this->playid_thisuser,
            'key' => 'notifications-msg'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'notifications-msg'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_otheruser, // Remote user play
            'value' => $this->playid_thisuser,
            'key' => 'notifications-match'
        ));

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'notifications-match'
        ));


        /* un-match both ways */
        $storage->populateRecord(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'un-matches'
        ));

        $storage->populateRecord(array(
            'play_id' => $this->playid_otheruser,
            'value' => $this->playid_thisuser, // Remote user play
            'key' => 'un-matches'
        ));

        $storage->populateRecord(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
            'key' => 'un-matches-auto'
        ));

        $storage->populateRecord(array(
            'play_id' => $this->playid_otheruser,
            'value' => $this->playid_thisuser, // Remote user play
            'key' => 'un-matches-auto'
        ));

        if (!$blockChat) {
            return true;
        }

        $context = $this->getTwoWayChatId($this->playid_otheruser,$this->playid_thisuser);
        $chat = \Aechat::model()->findByAttributes(array('context_key' => $context));

        if (!is_object($chat)) {
            return true;
        }

        $chat->blocked = 1;
        $chat->update();

        return true;
    }

    public function getHiddenUsers($hiddenFriendsList)
    {
        if (empty($hiddenFriendsList)) {
            return array();
        }

        $ids = join(', ', $hiddenFriendsList);
        $gameId = $this->appid;

        $sql = "
            SELECT ae_game_play.id FROM ae_game_play
            JOIN ae_game_play_variable ON ae_game_play_variable.play_id = ae_game_play.id
            JOIN ae_game_variable ON ae_game_variable.id = ae_game_play_variable.variable_id
            WHERE ae_game_play.game_id = $gameId
            AND ae_game_variable.name = 'fb_id'
            AND ae_game_play_variable.value IN ($ids)
            GROUP BY ae_game_play.id
        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        return $rows;
    }

    /*
     * Delete the Match action between the current user and the remote ( swiped ) user
     */
    public function deleteMatchEntry() {
        $storage = new \AeplayKeyvaluestorage();

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
        ));

        return true;
    }

}