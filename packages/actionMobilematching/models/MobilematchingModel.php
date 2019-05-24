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

Yii::import('application.modules.aelogic.controllers.*');
Yii::import('application.modules.aechat.models.*');

class MobilematchingModel extends ArticleModel {

    public $id;
    public $game_id;
    public $gid;

    public $playid_thisuser;
    public $playid_otheruser;

    /** @var MobilematchingModel */
    public $obj_thisuser;

    /** @var MobilematchingModel */
    public $obj_otheruser;

    /** @var MobilematchingmetaModel */
    public $obj_mobilematchingmeta;

    /** @var AeplayKeyvaluestorage */
    public $obj_datastorage;

    /** @var Aechat */
    public $obj_chat;

    public $chatid;

    public $firstname_thisuser;
    public $firstname_otheruser;

    public $notifications;

    public $score;
    public $gender;

    public $actionid;
    public $debug;

    public $playid;

    public $flag;

    public $uservars;
    public $extra_join_query;
    public $extra_selects;

    public $send_accept_push = true;

    public $education;
    public $hindu_caste;
    public $role;
    public $is_boosted;
    public $boosted_timestamp;

    public $server_time_offset = '+1 hour';

    public $pivot_variables = array(
        'age',
        'relationship_status',
        'seeking',
        'religion',
        'diet',
        'tobacco',
        'alcohol',
        'zodiac_sign',
        'reg_status',
        'reg_phase',
        'profilepic',
        'email',
        'profile_location_invisible',
        'real_name'
    );

    public $metakeys = array(
        'change-location',
        'spark-profile',
        'extra-superhot',
        'hide-distance',
        'active-users-first',
        'unlimited-swipes',
        'hide-age',
        'next-mutual-likes',
        'send-media'
    );


    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobilematching';
    }

    public function relations()
    {
        return array(
            'aeaction' => array(self::BELONGS_TO, 'Aeaction', 'playtask_id'),
            'aegame' => array(self::BELONGS_TO, 'Aegame', 'game_id'),
            'user' => array(self::BELONGS_TO, 'UserGroupsUseradmin', 'user_id'),
        );
    }

    /* called by async task */
    public static function updateCities($options){

    	$required = array( 'lat', 'lon', 'gid' );

	    foreach ( $required as $item ) {
		    if ( !isset($options[$item]) ) {
		    	return false;
		    }
    	}

        $cities = ThirdpartyServices::getNearbyCities($options['lat'],$options['lon'],$options['gid']);
        AeplayVariable::updateWithName($options['playid'],'nearby_cities',json_encode($cities),$options['gid']);

        return true;
    }

    public function updateTempUsers(){
        return true;
        $sql = "SELECT * FROM ae_game_play_variable WHERE `value` LIKE '%dummydomain.com'";

        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        foreach($rows as $row){
            $vars = AeplayVariable::getArrayOfPlayvariables($row['play_id']);
            if(isset($vars['reg_phase'])){
                return true;
            }

            $arr['reg_phase'] = 'complete';
            AeplayVariable::saveVariablesArray($arr, $row['play_id'], $this->gid);
        }


    }

    public function createPivotView($pivot_vars=false){

        if(!$pivot_vars){
            $pivot_vars = $this->pivot_variables;
        }

        $viewname = 'matching_'.$this->gid;
        if($this->pivotCheck($pivot_vars,$viewname)){
            return true;
        }

        try{
            @\Yii::app()->db->createCommand("DROP VIEW IF EXISTS $viewname")->query();
        } catch(\CDbException $e){
        }

        $field = '';
        $join = '';
        $metajoin = '';

        $vars = Aevariable::getGameVariables($this->gid);

        foreach($vars as $var){
            $name = $var['name'];
            $id = $var['id'];
            $varindex[$name] = $id;
        }

        $datatype = '';

        foreach($pivot_vars as $var){
            if(isset($varindex[$var])){
                if(stristr($var, '-')){
                    $var = str_replace('-', '_', $var);
                }
                $varid = $varindex[$var];
                $field .= $var.'tbl.value as '.$var .','.chr(10);
                $tablename = $var.'tbl';
                $join .= "LEFT JOIN ae_game_play_variable AS $tablename ON ae_ext_mobilematching.play_id = $tablename.play_id AND $tablename.variable_id = '$varid' ".chr(10);
            }
        }

        $sql = "CREATE VIEW $viewname AS
                SELECT 
	            $field 
	            ae_ext_mobilematching.*
	                FROM ae_ext_mobilematching
	                $join
	                $metajoin
	            WHERE reg_phasetbl.value = 'complete'
                GROUP BY ae_ext_mobilematching.play_id
                ORDER BY ae_ext_mobilematching.play_id DESC";

        Yii::app()->db->createCommand($sql)->query();

    }

    public function pivotCheck($pivot_vars,$viewname){
        $sql = "SHOW COLUMNS FROM $viewname";

        try{
            $test = Yii::app()->db->createCommand($sql)->queryAll();
        } catch(CDbException $e){
            return false;
        }

        if($test){
            $existing_columns = array();

            foreach($test as $column){
                $column = $column['Field'];
                $existing_columns[$column] = true;
            }

            if($test){
                foreach ($pivot_vars as $column){
                    if(!isset($existing_columns[$column])){
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function setPointer($foreignplay){
        $cachename = $this->actionid .'-' .'pointer';
        Appcaching::setGlobalCache($cachename,$foreignplay);
    }

    /* NOTE: This function would have absolutely no context or any of the variables initialised ! */
    public static function beaconExit($payload,$actionobj){
        if(!isset($actionobj->action_id)){ return false; }

        $command2 = new StdClass;
        $command2->action = 'open-action';
        $command2->action_config = $actionobj->action_id;

        return array($command2);
    }

    public function getPointer(){
        $cachename = $this->actionid .'-' .'pointer';
        return Appcaching::getGlobalCache($cachename);
    }

    public function initMatching($foreign_id=false,$debug=true){
        if($foreign_id){
            $this->playid_otheruser = $foreign_id;
        }

        if(!is_object($this->obj_thisuser) AND $this->playid_thisuser){
            $this->obj_thisuser = MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_thisuser));
        }

        if(!is_object($this->obj_otheruser) AND $this->playid_otheruser){
            $this->obj_otheruser = MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_otheruser));
        }

        if(!$this->obj_thisuser){
            $this->turnUserToItem(false, __FILE__);
        }

        $this->obj_datastorage = new AeplayKeyvaluestorage();
        $this->obj_datastorage->play_id = $this->playid_thisuser;

        $this->obj_mobilematchingmeta = new MobilematchingmetaModel();
        $this->obj_mobilematchingmeta->play_id = $this->playid_thisuser;

        if(!$this->gid OR !$this->playid_thisuser OR !$this->actionid){
            throw new CException(Yii::t('yii','Object not initialised properly'));
        }

        $this->tableMigration();
    }

    public function initChat(){
        $this->obj_chat = new Aechat();
        $this->obj_chat->play_id = $this->playid_thisuser;
        $this->obj_chat->gid = $this->gid;
        $this->obj_chat->context = 'mobilematching';
        $this->obj_chat->context_key = $this->actionid;
        $this->obj_chat->game_id = $this->gid;
        $this->obj_chat->initChat();
        $this->chatid = $this->obj_chat->getChatId();
    }

    private function tableMigration(){

        $columns = MobilematchingModel::getMetaData()->columns;
        if(isset($columns['user_id'])){

            $sql = "TRUNCATE ae_ext_mobilematching";
            Yii::app()->db->createCommand($sql)->query();

            $sql = "ALTER TABLE `ae_ext_mobilematching` DROP FOREIGN KEY `user`";
            Yii::app()->db->createCommand($sql)->query();

            $sql = "ALTER TABLE `ae_ext_mobilematching` DROP `user_id`, DROP `matches`, DROP `twoway_matches`, DROP `chats`, DROP `unmatch`, DROP `notifications`";
            Yii::app()->db->createCommand($sql)->query();

        }

    }

    /* chat content can be saved to either users chat field */
    public function getChatLocation(){

    }


    public function getNumberOfMatches($other_user_play_id = false, $key = 'two-way-matches'){

	    if($other_user_play_id){
		    $get_by = ( $key == 'two-way-matches' ? 'key' : 'value' );
		    $this->obj_datastorage->play_id = $other_user_play_id;
		    $count = count($this->obj_datastorage->get($key, $get_by));
		    $this->obj_datastorage->play_id = $this->playid_thisuser;
            return $count;
        }

        return count($this->obj_datastorage->get($key));
    }

    public function getNotificationCount(){

        if(isset($this->obj_thisuser->notifications)){
            $notifications = unserialize($this->obj_thisuser->notifications);
            foreach($notifications as $notify){
                return 1;
            }
        }

	    return false;
    }


    public function getChatContent(){
         $this->initChat();
         return json_encode($this->obj_chat->getChatContent());
    }

    public function resetNotifications(){
        $this->obj_datastorage->deleteAllByAttributes(array('play_id' => $this->playid_thisuser,'value' => $this->playid_otheruser,'key' => 'notifications-match'));
        $this->obj_datastorage->deleteAllByAttributes(array('play_id' => $this->playid_thisuser,'value' => $this->playid_otheruser,'key' => 'notifications-msg'));
    }

    public function getNotifications(){
        $this->initChat();
        $arr = $this->obj_datastorage->get('notifications-match');
        $output = array();

        if(!empty($arr)){
            foreach($arr as $notification){
                $output[] = array(
                    'type' => 'match',
                    'user' => $notification,
                );
            }
        }

        $arr2 = $this->obj_datastorage->get('notifications-msg');

        if(!empty($arr2)){
            foreach($arr2 as $notification){
                $output[] = array(
                    'type' => 'msg',
                    'user' => $notification,
                );
            }
        }

        return $output;
    }

    public static function saveChatContent($recordid){

    }

    /* saves information about the match & matches both ways */
    public function saveMatch(){

        $this->obj_datastorage->set('matches',$this->playid_otheruser);
        $this->obj_datastorage->play_id = $this->playid_otheruser;

        if($this->obj_datastorage->valueExists('matches',$this->playid_thisuser)){
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            $this->saveTwoWayMatch();
            return true;
        } else {
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            return false;
        }
    }

    public function forceMatch() {
	    $this->obj_datastorage->set('matches',$this->playid_otheruser);
	    $this->obj_datastorage->play_id = $this->playid_thisuser;
	    $this->saveTwoWayMatch();
	    return true;
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
        $matches = json_decode($this->factory->getSavedVariable('current_matches'),true);
        $out = array();

        if($matches){
            foreach($matches as $match){
                if($this->playid_otheruser != $match['play_id']){
                    $out[] = $match;
                }
            }

            $out = json_encode($out);
            $this->factory->saveVariable('current_matches',$out);
        }

        $varcontent = AeplayVariable::fetchWithName($this->playid_otheruser,'current_matches',$this->gid);
        $varcontent = json_decode($varcontent);
        $out = array();

        if(is_object($varcontent)){
            foreach($varcontent as $match){
                if($this->playid_thisuser != $match['play_id']){
                    $out[] = $match;
                }
            }

            AeplayVariable::updateWithName($this->playid_otheruser,'current_matches',$out,$this->gid);
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

        if(!$exist){
            $this->obj_datastorage->set('two-way-matches',$this->playid_otheruser);
            $this->obj_datastorage->play_id = $this->playid_otheruser;
            $this->obj_datastorage->set('two-way-matches',$this->playid_thisuser);

            /* by default the play id should always be of this play, hence we save it right back */
            $this->obj_datastorage->play_id = $this->playid_thisuser;
            $this->saveNotificationForMatch();
        }

    }

    public function addNotificationToBanner($type='match'){
        $this->obj_datastorage->play_id = $this->playid_otheruser;
        $this->obj_datastorage->set('notifications-'.$type,$this->playid_thisuser);
    }

    /* saves a notification banner & sends a push */
    public  function saveNotificationForMatch(){

        $this->addNotificationToBanner('match');

        if ( $this->send_accept_push ) {
            if($this->firstname_thisuser){
                $title = $this->firstname_thisuser .'{#has_accepted_your_friend_invite#}'.'!';
                $name = $this->firstname_thisuser .'{#has_accepted_your_friend_invite#}'.'!';
            } else {
                $name = '';
            }

            $msg = $title .' '. $name;
            Aenotification::addUserNotification( $this->obj_otheruser->play_id, $title, $msg, '+1', $this->gid );
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
        return $this->obj_datastorage->get( 'two-way-matches' );
    }

    public function getMyLikes(){
        return $this->obj_datastorage->get( 'matches' );
    }

    public function getMyDislikes(){
        return $this->obj_datastorage->get( 'un-matches' );
    }

    public function getGroupChats(){

        $sql = "SELECT * FROM ae_chat_users 
                LEFT JOIN ae_chat ON ae_chat_users.chat_id = ae_chat.id
                WHERE `chat_user_play_id` = :playId AND `type` = 'group'";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':playId' => $this->playid_thisuser
            ))
            ->queryAll();


        $output = array();

        foreach ($rows as $row){
            $output[] = $row['context_key'];
        }

        return $output;

    }

    public function getMyOutbox( $validity = false ){
        // return $this->obj_datastorage->get('matches');

        $sql = "SELECT * FROM ae_game_play_keyvaluestorage WHERE `play_id` = :play_id AND `key` = 'matches'";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':play_id' => $this->playid_thisuser
            ))
            ->queryAll();

        $output = array();

        foreach ($rows as $row){

            if ( $validity AND !$this->isUserStillValid( $row, $validity ) ) {
                continue;
            }

            if ( $this->checkIfUnmatched( $row['value'], $row['play_id'] ) ) {
                continue;
            }

            if ( $this->checkIfBothMatched( $row['value'], $row['play_id'] ) ) {
                continue;
            }

            $output[] = $row['value'];
        }

        return $output;
    }

    public function getMyInbox( $validity = false, $field = 'matches' ){

        $sql = "SELECT * FROM ae_game_play_keyvaluestorage WHERE `value` = :play_id AND `key` = :field";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':play_id' => $this->playid_thisuser,
                ':field' => $field,
            ))
            ->queryAll();

        $output = array();

        foreach ($rows as $row){
            if ( $validity AND !$this->isUserStillValid( $row, $validity ) ) {
                continue;
            }

            if ( $this->checkIfUnmatched( $row['play_id'], $row['value'] ) ) {
                continue;
            }

            if ( $this->checkIfBothMatched( $row['play_id'], $row['value'] ) ) {
                continue;
            }

            $output[] = $row['play_id'];
        }

        return $output;
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

        $storage = new AeplayKeyvaluestorage();
        $storage->deleteByPk( $row['id'] );

    }

    public function checkIfBothMatched( $play_id, $value ) {
        $model = new AeplayKeyvaluestorage();

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
        $model = new AeplayKeyvaluestorage();

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

    public static function locateUserWithIp(){

        if($_SERVER['REMOTE_ADDR'] == '127.0.0.1' OR  $_SERVER['REMOTE_ADDR'] == '::1'){
            $address = '93.152.145.119';
        } else {
            $address = $_SERVER['REMOTE_ADDR'];
        }

        $data = ThirdpartyServices::locateWithIp($address);

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

        AeplayVariable::updateWithName($playid,'lat',$location->latitude,$gid);
        AeplayVariable::updateWithName($playid,'lon',$location->longitude,$gid);
        AeplayVariable::updateWithName($playid,'city',$location->city,$gid);
        AeplayVariable::updateWithName($playid,'zip',$location->zip_code,$gid);
        AeplayVariable::updateWithName($playid,'country',$location->country_name,$gid);

        $obj = MobilematchingModel::model()->findByAttributes(array('play_id' => $playid));

        if(!is_object($obj)){
            return false;
        }
        $obj->lon = $location->longitude;
        $obj->lat = $location->latitude;
        $obj->update();

    }


    public function turnUserToItem($reverse=false,$file){

        $obj = MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_thisuser));
        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);

        if($reverse AND isset($vars['city_selected']) AND isset($vars['country_selected']) AND $vars['city_selected'] AND $vars['country_selected']){
            Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            MobilelocationModel::addressTranslation($this->gid,$this->playid_thisuser,$vars['country_selected'],$vars['city_selected']);
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } elseif($reverse AND isset($vars['city']) AND isset($vars['country']) AND $vars['city'] AND $vars['country']){
            Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            MobilelocationModel::addressTranslation($this->gid,$this->playid_thisuser,$vars['country'],$vars['city']);
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        }
        
        if(!is_object($obj) AND isset($vars['lat']) AND isset($vars['lon'])){
            $obj = new MobilematchingModel();
            $obj->game_id = $this->gid;
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

        $this->obj_thisuser = MobilematchingModel::model()->findByAttributes(array('play_id' => $this->playid_thisuser));

        //self::updateUsersLocation($playid,$gid);

    }

    public function getStatsStatus(){
        $data = ThirdpartyServices::getFlurryData($this->gid);

        if($data){
            return true;
        } else {
            return false;
        }
    }

    public function loadStats(){
        $cache = Appcaching::getGlobalCache($this->gid.'statsloading');

        if(!$cache){
            ThirdpartyServices::loadFlurryData($this->gid);
        }
    }


    public function getStats($what,$count){
        return ThirdpartyServices::getFlurryNode($this->gid,$what,$count);
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
                                    ':gameId' => $this->gid,
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
                        ':gameId' => $this->gid,
                    ))
                    ->queryAll();

                if(isset($rows[0]['totalcount'])){
                    return $rows[0]['totalcount'];
                } else {
                    return 0;
                }

                break;

        }
    }

    private function getWhereQuery($type='exclude', $exclude_skipped = false,$table='ae_ext_mobilematching'){
        $criteria = '';

        $exclude1 = $this->obj_datastorage->get('matches');

        if ( $exclude_skipped ) {
            $exclude2 = array();
        } else {
            $exclude2 = $this->obj_datastorage->get('un-matches');
        }

        $exclude3 = $this->obj_datastorage->get('un-matches-auto');


        // Very temporary solution
        switch ( $type ) {
            case 'olive':
                $criteria = self::getWhereQuery('exclude');
                //$part2 = " AND `value` = '" .$this->uservars['interests'] ."'";
                //$criteria = $part1 .chr(10) .$part2;
                break;

            case 'exclude':
                $exclude = array_merge($exclude1,$exclude2,$exclude3);

                if(isset($exclude) AND is_array($exclude) AND !empty($exclude)){
                    $excludeids = '';

                    foreach($exclude AS $key => $value){
                        if($value){
                            $excludeids .= "'" .$value ."',";
                        }
                    }

                    if(!empty($excludeids)){
                        $excludeids = (string)$excludeids;
                        $excludeids = substr($excludeids,0,-1);
                        $criteria = "AND $table.play_id NOT IN ($excludeids)";
                    }
                }                
                break;
            
            case 'include':

                $args = array(
                    'play_id' => $this->playid_thisuser,
                    'key' => 'requested_match',
                );

                $storage = new AeplayKeyvaluestorage();
                $matches = $storage->findAllByAttributes( $args );

                if ( empty($matches) ) {
                    return array();
                }

                $criteria = '';
                $ids = '';

                foreach ($matches as $match) {
                    $ids .= "'" . $match->value . "',";
                }

                if (!empty($ids)) {
                    $ids = (string)$ids;
                    $ids = substr($ids, 0, -1);
                    $criteria = "AND play_id IN ($ids)";
                }

                break;

            case 'requestors':
                $criteria = "AND match_always = 1";
                break;

            case 'acceptors':
                $criteria = "AND match_always = 0";
                break;
        }

        return $criteria;
    }


    /*
     *  1. Show people with same city and with exact match of interests.
        2. show people that are different city (by distance) and same interests
        3. show people with partial match in interests and same city
        4. show people with paritial match and different city (by distance)
        5. show people with 0 match in interest sorted by distance
    */
    public function oliveQuery($distance = 15000,$varname,$varid,$strict=false){

        $output = array();

        $this->extra_join_query = "LEFT JOIN ae_game_play_variable ON ae_ext_mobilematching.play_id = ae_game_play_variable.play_id AND ae_game_play_variable.variable_id = $varid";
        $all = $this->getUsersNearby($distance,'olive');
        $vars = isset($this->uservars[$varname]) ? json_decode($this->uservars[$varname],true) : array();

        /* rate similarity */
        $all = self::addSimilarity($all,$vars);

        /* 1. in output */
        foreach($all as $key=>$value){
            if($value['distance'] == 0 AND $value['similarity'] == 1){
                $output[] = $value;
                unset($all[$key]);
            }
        }

        /* 2. in output */
        foreach($all as $key=>$value){
            if($value['similarity'] == 1){
                $output[] = $value;
                unset($all[$key]);
            }
        }

        /* 3. in output */
        foreach($all as $key=>$value){
            if($value['similarity'] > 0 AND $value['distance'] == 0){
                $output[] = $value;
                unset($all[$key]);
            }
        }

        /* 4. in output */
        foreach($all as $key=>$value){
            if($value['similarity'] > 0){
                $output[] = $value;
                unset($all[$key]);
            }
        }

        if($strict){
            return $output;
        }

        /* 5. all the rest */
        $output = $output+$all;

        return $output;
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

    public static function mergeOlive($array1,$array2){

        if(empty($array1) OR empty($array2)){
            return $array1;
        }

        $ids = array();

        foreach($array1 as $item){
            $ids[] = $item['id'];
        }

        foreach($array2 as $item){
            if(!in_array($item['id'],$ids)){
                $array1[] = $item;
            }
        }

        return $array1;
    }

    /*
     * use this for testing if needed:
    set @orig_lat='-26.20410280';
    set @orig_lon='28.04730510';
    set @bounding_distance=360;
    SELECT * ,( 3959 * 1.609344 * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) * sin( radians(`lat`)))) AS `distance` FROM `ae_ext_mobilematching` WHERE ( `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance) AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance) ) AND play_id <> :playID AND game_id = :gameId ORDER BY `distance` ASC limit 50;*/
    public function getUsersNearby($distance = 150, $type = 'exclude', $sex_depended = false, $match_opposing_roles = false, $sorting=false, $units = 'km' ){

        if(empty($this->uservars)){
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $where_clause = $this->getWhereQuery($type);

        if (empty($where_clause) AND $type == 'include') {
            return array();
        }

        $query_by_sex = $this->getQueryBySex($vars,$sex_depended);
        $query_by_role = $this->getQueryByRole($vars,$match_opposing_roles);
        $sorting = $this->getSorting($sorting);

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

/*        echo("set @orig_lat=$lat").'<br>';
        echo("set @orig_long=$lon").'<br>';
        echo("set @bounding_distance=360").'<br>';*/

        $sql = "SELECT
            *,
            ( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
                * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
                * sin( radians(`lat`)))
            ) AS `distance`
            $this->extra_selects
            FROM `ae_ext_mobilematching`
            $this->extra_join_query
            
            WHERE
            (
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            )

            AND ae_ext_mobilematching.play_id <> :playId
            AND ae_ext_mobilematching.game_id = :gameId
            $query_by_sex
            $where_clause
            $query_by_role
            
            GROUP BY ae_ext_mobilematching.play_id
            HAVING (distance <= $distance OR `lat` = '0.00000000')

            ORDER BY $sorting ASC 
            limit 200
        ";

        $this->debug = $sql;

/*        echo($this->playid_thisuser);

        print_r($sql);die();*/

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
                ':playId' => $this->playid_thisuser,
                ':gameId' => $this->gid,
                ':unit' => ( $units == 'km' ? 1.609344 : 1 ),
            ))
            ->queryAll();

        return $rows;
    }


    public function getFakeUsers($distance = 150, $type = 'exclude', $sex_depended = false, $match_opposing_roles = false, $sorting=false, $units = 'km',$table ){

        if(empty($this->uservars)){
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $where_clause = $this->getWhereQuery($type,false,$table);

        if (empty($where_clause) AND $type == 'include') {
            return array();
        }

        $filters = $this->getFilterQuery($vars);

        $sql = "SELECT
            *,
            $table.play_id as distance
            FROM $table
            $this->extra_join_query
            
            WHERE
            $table.play_id <> :playId
            AND $table.game_id = :gameId
            AND $table.lat = '0.00000000'
            AND $table.email LIKE '%dummydomain.com'
            $filters
            $where_clause
            GROUP BY $table.real_name
            ORDER BY $table.play_id ASC 
            limit 200
        ";

        $this->debug = $sql;
        
        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
                ':playId' => $this->playid_thisuser,
                ':gameId' => $this->gid
            ))
            ->queryAll();

        return $rows;
    }

    public function getUsersNearbyDesee($distance = 150, $type = 'exclude', $sex_depended = false, $match_opposing_roles = false, $sorting=false, $units = 'km' ){
        $viewname = 'matching_'.$this->gid;

        if(empty($this->uservars)){
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $where_clause = $this->getWhereQuery($type,false,$viewname);

        if (empty($where_clause) AND $type == 'include') {
            return array();
        }

        $sorting = $this->getSorting($sorting);

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $filters = $this->getFilterQuery($vars);

/*        echo("set @orig_lat=$lat").'<br>';
        echo("set @orig_long=$lon").'<br>';
        echo("set @bounding_distance=360").'<br>';*/

        $sql = "SELECT
            *,
            ( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
                * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
                * sin( radians(`lat`)))
            ) AS `distance`
            $this->extra_selects,
            play_id as play_id
            FROM $viewname
            $this->extra_join_query
            
            WHERE
            (
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            )

            AND $viewname.play_id <> :playId
            AND $viewname.game_id = :gameId
            AND $viewname.lat <> '0.00000000'
            $filters
            $where_clause
            
            GROUP BY $viewname.play_id
            HAVING distance <= $distance
            ORDER BY $sorting ASC 
            limit 100
        ";

        $this->debug = $sql;

        /*        echo($this->playid_thisuser);

        print_r($sql);die();
*/
        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
                ':playId' => $this->playid_thisuser,
                ':gameId' => $this->gid,
                ':unit' => ( $units == 'km' ? 1.609344 : 1 ),
            ))
            ->queryAll();

        return $rows;
    }

    public function getFilterQuery($vars){

        $sql = '';

        if(isset($vars['men']) AND $vars['men'] == 1 AND isset($vars['women']) AND $vars['women'] == 1){

        } elseif(isset($vars['men']) AND $vars['men'] == 1){
            $sql .= " AND gender = 'man'";
        } elseif(isset($vars['women']) AND $vars['women'] == 1){
            $sql .= " AND gender = 'woman'";
        }

        $preferences = array(
            'relationship_status',
            'seeking',
            'religion',
            'diet',
            'tobacco',
            'alcohol',
            'zodiac_sign'
        );

        foreach ($preferences as $preference) {
            $pref = 'preference_'.$preference;
            $status = isset($vars[$pref]) ? $vars[$pref] : null;

            if (empty($status)) {
                continue;
            }

            if ($status == '[]') {
                continue;
            }

            $test = json_decode($status,true);

            if(is_array($test)){
                $p = '';

                foreach($test as $stat){
                    $p .= " $preference = '$stat' OR ";
                }

                if($p){
                    $p = substr($p,0,-3);
                }

                $sql .= " AND ($p)";

            } else {
                $sql .= " AND $preference = $status ";
            }

        }

        if(isset($vars['filter_age_start']) AND isset($vars['filter_age_end'])){
            $start = $vars['filter_age_start'];
            $end = $vars['filter_age_end'];
            $sql .= " AND age > $start AND age < $end ";
        }

        return $sql;
    }

    public function getUsersByIDs( $user_ids, $units = 'km',$viewname ){

        if ( empty($user_ids) ) {
            return false;
        }

        if(empty($this->uservars)){
            $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $user_ids_array = implode(',', $user_ids);

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT
            *,( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
               * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
               * sin( radians(`lat`)))) AS `distance`
          
            FROM $viewname
            
            WHERE
            (
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            )
  
            AND $viewname.play_id IN ($user_ids_array)
            AND $viewname.play_id <> :playId
            AND $viewname.game_id = :gameId
            
            GROUP BY $viewname.play_id

            ORDER BY FIELD($viewname.play_id, $user_ids_array)
            limit 1000
        ";

        $this->debug = $sql;

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid_thisuser,
            ':gameId' => $this->gid,
            ':unit' => ( $units == 'km' ? 1.609344 : 1 ),
        ))
            ->queryAll();

        if(!$rows){
            return array();
        }

        return $rows;
    }

    public function getSorting($sorting){

        if ( $sorting == 'boosted' ) {
            return '`is_boosted` DESC, `distance`';
        } else if ( $sorting == 'active-and-boosted' ) {
            return '`last_update` DESC, `is_boosted` DESC, `distance`';
        }

        return '`distance`';
    }

    public function getQueryBySex($vars,$sex_depended){
        $query_by_sex = false;

        if ( $sex_depended AND isset($vars['gender'])) {
            if ( $vars['gender'] == 'man' OR $vars['gender'] == 'male' ) {
                $sex = 'woman';
                $secondsex = 'female';
            } else {
                $sex = 'man';
                $secondsex = 'male';
            }

            $query_by_sex = "AND (gender = '$sex' OR gender = '$secondsex') ";
        }

        return $query_by_sex;
    }

    public function getQueryByRole($vars,$match_opposing_roles){
        if($match_opposing_roles AND isset($vars['role'])){
            if($vars['role'] == 'influencer'){
                $srch = 'brand';
            } else {
                $srch = 'influencer';
            }

            /* match always is inluced because approving users changes this value */
            $query_by_role = "AND role = '$srch' AND match_always = 1";
        } else{
            $query_by_role = false;
        }

        return $query_by_role;
    }


    public function getUsersWhoHaveMatchedMe($validity){
        $inbox = $this->getMyInbox($validity, 'suggested');

        if ( empty($inbox) ) {
            return false;
        }

        $values = implode(',',$inbox);

        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);

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
                    AND game_id = $this->gid
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

    public function getUsersNearbySila($distance=150, $exclude_skipped = false){

        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $excludequery = $this->getWhereQuery( 'exclude', $exclude_skipped );

        if(!isset($vars['score']) OR !isset($vars['gender'])){
            return false;
        }

        $score = $vars['score'];

        if($score > 9){
            $scoregroup = 1;
        } else {
            $scoregroup = 2;
        }

        if($vars['gender'] == 'man'){
            $sex = 'woman';
        } else {
            $sex = 'man';
        }

        if(isset($vars['education'])){
            $education = $vars['education'];
        } else {
            $education = 1;
        }

        if($education == 'Choose' OR !is_numeric($education)){
            $education = 0;
        }

        $sql = "SELECT
                    *
                    ,((ACOS(SIN(@orig_lat * PI() / 180) * SIN(`lat` * PI() / 180) + COS(@orig_lat * PI() / 180) * COS(`lat` * PI() / 180) * COS((@orig_long - `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`,
                    ABS(score - $score) AS score_match,

                    if(`score` > '9',1,2) as scoregroup,
                    ABS(if(`score` > '9',1,2) - $scoregroup) as scoregroup_match,
                    ABS(education - $education) AS education_match

                    FROM `ae_ext_mobilematching`
                    # LEFT JOIN ae_game_play_variable ON ae_ext_mobilematching.play_id = ae_game_play_variable.id
                    WHERE
                    (
                      `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
                      AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
                    )

                    AND play_id <> $this->playid_thisuser
                    AND game_id = $this->gid
                    AND gender = '$sex'
                    # AND distance > 30
                    $excludequery

                    ORDER BY education_match, scoregroup_match,score_match,`distance` ASC
                    limit 100
        ";


        //echo($sql);die();

        $rows = Yii::app()->db
            ->createCommand($sql)
/*            ->bindValues(array(
                ':gameId' => $this->gid,
                ':userId' => $this->user_id,
                ':score' => $score,
                ':scoregroup' => $scoregroup,
                ':sex' => $sex
                ))*/
            ->queryAll();

        return $rows;

    }

    /*
    * Clean up the Key-value storage table
    * This method would essentially remove all unnecessary fields, created during the matching process
    */
    public function removeTwoWayMatches($blockChat = true) {
        $storage = new AeplayKeyvaluestorage();

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

        $context = $this->factory->getTwoWayChatId($this->playid_otheruser,$this->playid_thisuser);
        $chat = Aechat::model()->findByAttributes(array('context_key' => $context));

        if (!is_object($chat)) {
            return true;
        }

        $chat->blocked = 1;
        $chat->update();

    }

    public function getHiddenUsers($hiddenFriendsList)
    {
        if (empty($hiddenFriendsList)) {
            return array();
        }

        $ids = join(', ', $hiddenFriendsList);
        $gameId = $this->gid;

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

    public function getTotalSwipes() {
        $totals = $this->obj_datastorage->getTotalSwipesSQL();
        return $totals;
    }

    public function getLastSwipe() {
        $data = $this->obj_datastorage->getLastSwipeSQL();
        return $data;
    }

    /*
     * Delete the Match action between the current user and the remote ( swiped ) user
     */
    public function deleteMatchEntry() {
        $storage = new AeplayKeyvaluestorage();

        $storage->deleteAllByAttributes(array(
            'play_id' => $this->playid_thisuser,
            'value' => $this->playid_otheruser, // Remote user play
        ));

        return true;
    }

    public function deleteAutomaticUnmatches()
    {
        AeplayKeyvaluestorage::model()->deleteAllByAttributes(array('key' => 'un-matches-auto-'));
    }

}