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

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileimageview.models.*');
Yii::import('application.modules.aelogic.packages.actionRecipe.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobiledebugController extends ArticleController {

    public $factoryobj;
    public $api_key;
    public $data;
    public $dataobj;


    /* this is a custom function which is called in case action is invisible */

    public function tab1(){
        $this->data = new stdClass();

        ini_set("log_errors", 1);
        ini_set("error_log", "/tmp/php-error.log");
        error_log( "Hello, errors!" );

        $this->header('1');

        $cache = Appcaching::getGlobalCache($this->playid .'-debug');

        if($this->menuid == 'generate-users'){
            $this->generateUsers();
        }

        if($this->menuid == 'delete-users'){
            $this->data->scroll[] = $this->getText('All temp users deleted');
            $this->deleteTempUsers();
        }


        if ( $cache ) {
            $col[] = $this->getTextbutton('Send debug information',array('id' => 'send-mail','width' => '20%','font-size' => '11'));

            if ( $this->menuid == 'send-mail' ) {
                $this->sendAdminNotification( $cache );
            }
            $col[] = $this->getVerticalSpacer('1%');
            $width = '19%';
        } else {
            $width = '24%';
        }


        if($this->menuid == 'send-push'){
            Yii::import('application.modules.aelogic.controllers.*');

            $messaging = new Messaging();
            $messaging->playid = $this->playid;
            $messaging->title = strip_tags('test msg');
            $messaging->message = strip_tags('This is a push message test');
            $messaging->badge = 0;
            $messaging->sendPush();
            $debug = $messaging->debug;

/*          $id = '<b06b0afd b50f6739 bc890842 5578a7cc dd7fea3e f783eea0 ce02dbc7 757c1639>';
            $messaging = new Messaging();
            $messaging->playid = $this->playid;
            $messaging->message = 'This was manually entered id';
            $messaging->sendPush($id);

            $messaging = new Messaging();
            $messaging->playid = $this->playid;
            $messaging->message = 'This was with id from db';
            $messaging->sendPush();*/

            foreach($debug AS $item){
                $this->data->scroll[] = $this->getText($item);
            }

            $col[] = $this->getTextbutton('Msg sent',array('id' => 'send-push','width' => $width,'font-size' => '11'));
        } else {
            $onclick[] = $this->getOnclick('push-permission');
            $onclick[] = $this->getOnclick('id',false,'send-push');
            $col[] = $this->getTextbutton('Send push msg',array('id' => 'send-push','width' => $width,'font-size' => '11','onclick' => $onclick));
        }

        $col[] = $this->getVerticalSpacer('1%');
        $col[] = $this->getTextbutton('add 50 temp users',array('id' => 'generate-users','width' => $width,'font-size' => '11'));
        $col[] = $this->getVerticalSpacer('1%');
        $col[] = $this->getTextbutton('delete temp users',array('id' => 'delete-users','width' => $width,'font-size' => '11'));
        $col[] = $this->getVerticalSpacer('1%');
        $col[] = $this->getTextbutton('update',array('id' => 'update','width' => $width,'font-size' => '11'));

        $this->data->footer[] = $this->getRow($col,array('text-align' => 'center'));

        unset($col);
        $this->data->scroll[] = $this->getSettingsTitle("ID's");
        $this->data->scroll[] = $this->getText('playid: ' .$this->playid);
        $this->data->scroll[] = $this->getText('gameid: ' .$this->gid);
        $this->data->scroll[] = $this->getText('userid: ' .$this->userid);
        $this->data->scroll[] = $this->getSettingsTitle('Debug items');

        if($cache){
            foreach($cache AS $item){
                $this->data->scroll[] = $this->getText($item);
            }

        } else {
            $this->data->scroll[] = $this->getText('No Debug Data Available',array('font-size' => '10','width' => '30%','padding' => '5 5 5 5'));
        }

        if(!empty($this->session_storage)){
            $this->data->scroll[] = $this->getSettingsTitle('Session');

            foreach($this->session_storage AS $key=>$var) {
                if (is_string($key) AND (is_string($var) OR is_numeric($var))) {
                    $col[] = $this->getText($key, array('font-size' => '10', 'width' => '30%', 'padding' => '5 5 5 5'));
                    $col[] = $this->getText($var, array('font-size' => '10', 'width' => '70%', 'padding' => '5 5 5 5'));
                    $this->data->scroll[] = $this->getRow($col);
                    unset($col);
                } elseif (is_string($key) AND (is_array($var) OR is_object($var))) {
                    $col[] = $this->getText($key, array('font-size' => '10', 'width' => '30%', 'padding' => '5 5 5 5'));
                    $col[] = $this->getText(json_encode($var), array('font-size' => '10', 'width' => '70%', 'padding' => '5 5 5 5'));
                    $this->data->scroll[] = $this->getRow($col);
                    unset($col);
                } else {
                    $col[] = $this->getText($key, array('font-size' => '10', 'width' => '30%', 'padding' => '5 5 5 5'));
                    $col[] = $this->getText('unknown format', array('font-size' => '10', 'width' => '70%', 'padding' => '5 5 5 5'));
                    $this->data->scroll[] = $this->getRow($col);
                    unset($col);
                }
            }
        }



        $this->data->scroll[] = $this->getSettingsTitle('Key value storage');
        $results = @AeplayKeyvaluestorage::model()->findAllByAttributes(array('play_id' => $this->playid));

        if(!empty($results)){
            foreach($results AS $key=>$var){
                $keyvalue = (array)$var;
                if(isset($keyvalue['key']) AND isset($keyvalue['value'])){
                    $col[] = $this->getText($keyvalue['key'],array('font-size' => '10','width' => '30%','padding' => '5 5 5 5'));
                    $col[] = $this->getText($keyvalue['value'],array('font-size' => '10','width' => '70%','padding' => '5 5 5 5'));
                    $this->data->scroll[] = $this->getRow($col);
                    unset($col);
                }


            }
        }

        $this->data->scroll[] = $this->getSettingsTitle('Request Headers');
        $headers = apache_request_headers();

        foreach($headers AS $key=>$var){
            $col[] = $this->getText($key,array('font-size' => '10','width' => '30%','padding' => '5 5 5 5'));
            $col[] = $this->getText($var,array('font-size' => '10','width' => '70%','padding' => '5 5 5 5'));
            $this->data->scroll[] = $this->getRow($col);
            unset($col);
        }

        $this->data->scroll[] = $this->getSettingsTitle('remote ip');

        $col[] = $this->getText('ip',array('font-size' => '10','width' => '30%','padding' => '5 5 5 5'));
        $col[] = $this->getText($_SERVER['REMOTE_ADDR'],array('font-size' => '10','width' => '70%','padding' => '5 5 5 5'));
        $this->data->scroll[] = $this->getRow($col);

        return $this->data;
    }

    public function header($tab){


        $this->data->header[] = $this->getSpacer('40');
        $this->data->header[] = $this->getTabs(array('tab1' => 'main','tab2' => 'variables','tab3' => 'helpers'),false,false,$tab);
    }

    public function tab2(){
        $this->data = new stdClass();
        $this->header('2');

        $this->data->scroll[] = $this->getSettingsTitle('Variables');
        //$this->data->scroll[] = $this->getSettingsTitle($this->menuid);

        $color = "#E9E9E9";

        if(strstr($this->menuid,'save-')){
            $var = str_replace('save-','',$this->menuid);
            $this->saveVariable($var,$this->getSubmittedVariableByName('temp_varcontent_'.$var));
            $this->loadVariableContent(true);
        }

        if(strstr($this->menuid,'delete-')){
            $var = str_replace('delete-','',$this->menuid);
            $this->deleteVariable($var);
            $this->loadVariableContent(true);
        }

        foreach($this->varcontent AS $key=>$var){
            $col[] = $this->getText($key,array('font-size' => '10','width' => '20%','padding' => '5 5 5 5'));

            if($this->menuid == 'edit-'.$key){
                $col[] = $this->getFieldtext($var,array('variable' => 'temp_varcontent_'.$key,'font-size' => '13'));
                $col[] = $this->getImage('save-icon-new.png',array('width' => '20','text-align' => 'right','margin' => '4 5 4 5','onclick' => $this->getOnclick('id',false,'save-'.$key)));
            } else {
                $col[] = $this->getText($var,array('font-size' => '10','width' => '65%','padding' => '5 5 5 5'));
                $col[] = $this->getImage('edit-icon.png',array('width' => '20','text-align' => 'right','margin' => '4 5 4 5','onclick' => $this->getOnclick('id',false,'edit-'.$key)));
            }

            $col[] = $this->getImage('admin-delete-icon.png',array('width' => '20','text-align' => 'right','margin' => '4 5 4 5','onclick' => $this->getOnclick('id',false,'delete-'.$key)));
            $this->data->scroll[] = $this->getRow($col,array('background-color' => $color,'vertical-align' => 'middle'));

            if($color == '#E9E9E9'){
                $color = "#ffffff";
            } else {
                $color = "#E9E9E9";
            }
            unset($col);
        }

        /*
        if(!$this->current_tab == 2){
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }
        Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');

        $this->dataobj = new MobilefeedbacktoolModel();
        $this->dataobj->gid = $this->gid;
        $this->dataobj->author_id = $this->playid;
        $this->dataobj->varcontent = $this->varcontent;
        $this->dataobj->playid = $this->playid;

        $this->initKeyValueStorage();
        $userlist = $this->dataobj->initUserlist($this->appkeyvaluestorage->getOne('userlist'));

        //print_r($userlist);die();

        //$this->renderList('userlisting',$userlist,'0');
        $this->data->scroll[] = $this->getText('hello');*/

        return $this->data;


    }


    public function tab3(){
        $this->data = new stdClass();
        $this->header('3');
        $this->data->scroll[] = $this->getSettingsTitle('helper controls');
        $this->getSomeControls();
        return $this->data;



    }

    public function renderList($key,$value,$padding){

        if($padding > 100){
            $this->data->scroll[] = $this->getText('too many');
            return false;
        }

        if(is_array($value)){
            if($padding < 40){
                $this->data->scroll[] = $this->getSettingsTitle((string)$key);
            } else {
                $this->data->scroll[] = $this->getText((string)$key);
            }

            foreach($value as $subkey=>$subvalue){
                $this->renderList($subkey,$subvalue,$padding+5);
            }
        } else {
            $col[] = $this->getText($key,array('font-size' => '10','width' => '30%','padding' => '5 5 5 5', 'margin' => '0 0 0 '.$padding));
            $col[] = $this->getText($value,array('font-size' => '10','width' => '70%','padding' => '5 5 5 5','margin' => '0 0 0 '.$padding));
           // $this->data->scroll[] = $this->getRow($col);
        }

    }

    public function getSomeControls(){

        $this->data->scroll[] = $this->getImage('danger-zone.jpg',array('imgwidth' => '750','margin' => '10 4 18 4'));
        if($this->menuid == 'location-vars'){
            $this->sessionSet('location-asked',false);
            $this->sessionSet('region-monitoring-started',false);
            $this->deleteVariable('location_errors');
            $this->deleteVariable('nearby_beacons');
        }

        if($this->menuid == 'clear-userlist'){
            Yii::import('application.modules.aelogic.packages.actionMobilefeedbacktool.models.*');
            $this->dataobj = new MobilefeedbacktoolModel();
            $this->dataobj->factoryInit($this);
            $this->dataobj->gid = $this->gid;
            $this->dataobj->author_id = $this->playid;
            $this->dataobj->playid = $this->playid;
            $this->initKeyValueStorage();
            $this->userlist = $this->dataobj->initUserlist(true);
        }

        if($this->menuid == 'remove-chats'){
            Aechat::model()->deleteAllByAttributes(array('owner_play_id' => $this->playid));
        }

        if($this->menuid == 'write-session'){
            $this->sessionSet('debugtime',time());
        }

        if($this->menuid == 'clean-session'){
            foreach($this->session_storage as $key=>$part){
                $this->session_storage[$key] = '';
            }
        }

        //echo($this->menuid);die();
        if($this->menuid == 'remove-keyvalue'){
            //echo('yeah');die();
            AeplayKeyvaluestorage::model()->deleteAllByAttributes(array('play_id' => $this->playid));
        }

        if($this->menuid == 'fakeusers'){
            $this->generateFakeUsers();
        }

        if($this->menuid == 'fakeusersdelete'){
            $this->deteleFakeUsers();
        }


        if($this->menuid == 'full-raw-cache'){
            $this->data->scroll[] = $this->getTextbutton('cancel',array('id' => 'cancel','width' => '100%','font-size' => '11'));
            $cachename = $this->playid.$this->userid.'playcache';
            $cachecontent = Appcaching::getGlobalCache($cachename);
            $this->data->scroll[] = $this->getText(json_encode($cachecontent));
        }

        if($this->menuid == 'translations-off'){
            $this->saveVariable('no_localization', 1);
            $this->loadVariables();
        }

        if($this->menuid == 'translations-on'){
            $this->deleteVariable('no_localization');
            $this->loadVariables();
        }


        if($this->menuid = 'clear-swiper'){
            $cachename = $this->playid .'-swiper';
            Appcaching::removeGlobalCache($cachename);
        }

        $col[] = $this->getTextbutton('remove location tracking vars',array('id' => 'location-vars','width' => '23%','font-size' => '11'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('list branches',array('id' => 'list-branches','width' => '23%','font-size' => '11','onclick' => $this->getOnclick('list-branches')));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('del userlist cache',array('id' => 'clear-userlist','width' => '23%','font-size' => '11'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('del swiper cache',array('id' => 'clear-swiper','width' => '23%','font-size' => '11'));


        $this->data->scroll[] = $this->getRow($col,array('text-align' => 'center'));
        $this->data->scroll[] = $this->getSpacer('10');
        unset($col);


        $col[] = $this->getTextbutton('remove all users chats',array('id' => 'remove-chats','width' => '48%','font-size' => '11'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('remove all key value storage values',array('id' => 'remove-keyvalue','width' => '48%','font-size' => '11'));
        $this->data->scroll[] = $this->getRow($col,array('text-align' => 'center'));

        unset($col);
        $this->data->scroll[] = $this->getSpacer('10');

        $col[] = $this->getTextbutton('write to session',array('id' => 'write-session','width' => '32%','font-size' => '11'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('clean session',array('id' => 'clean-session','width' => '32%','font-size' => '11'));
        $col[] = $this->getVerticalSpacer('2%');
        $col[] = $this->getTextbutton('output raw session',array('id' => 'full-raw-cache','width' => '32%','font-size' => '11'));
        $this->data->scroll[] = $this->getRow($col,array('text-align' => 'center'));

        $this->data->scroll[] = $this->getSpacer('10');


        $this->data->scroll[] = $this->getSettingsTitle('Translation Management');
        $this->data->scroll[] = $this->getSpacer('10');

        if($this->getSavedVariable('no_localization')){
            $this->data->scroll[] = $this->getTextbutton('Turn Translations On',array('id' => 'translations-on','width' => '100%','font-size' => '11','background-color' => '#D00062'));
        } else {
            $this->data->scroll[] = $this->getTextbutton('Turn Translations Off',array('id' => 'translations-off','width' => '100%','font-size' => '11'));
        }

        $this->data->scroll[] = $this->getSettingsTitle('Generate fake users');
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getTextbutton('Generate fake users',array('id' => 'fakeusers','width' => '100%','font-size' => '11','background-color' => '#D00062'));


        $this->data->scroll[] = $this->getSettingsTitle('Delete fake users');
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getTextbutton('Delete fake users',array('id' => 'fakeusersdelete','width' => '100%','font-size' => '11','background-color' => '#D00062'));




    }

    public function sendAdminNotification( $body ) {
        $mail = new YiiMailMessage;

        ob_start();
        print_r($body);
        $textualRepresentation = ob_get_contents();
        ob_end_clean();

        $send_to = 'daniel.minchev@appzio.com';

        $mail->setBody($textualRepresentation, 'text/html');
        $mail->addTo( $send_to );
        $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = 'New Debug Email';

        Yii::app()->mail->send($mail);
    }

    public function generateUsers($num = 50){
        while($num > 0){
            if($this->generateAUser()){
                $num--;
            }
        }
    }

    public function deleteTempUsers(){
        if($this->gid AND $this->appinfo->api_key){
            $sql = 'DELETE usergroups_user FROM usergroups_user 
                      WHERE temp_user = 1 AND creator_api_key = :apikey';

            Yii::app()->db
                ->createCommand($sql)
                ->bindValues(array(
                    ':apikey' => $this->appinfo->api_key
                ))
                ->query();
        }
    }


    public function generateAUser(){

        $data = file_get_contents('https://randomuser.me/api/');
        $data = json_decode($data,true);
        if(!is_array($data) OR empty($data)){
            return false;
        }

        $data = $data['results'][0];
        $kittypic = @file_get_contents('http://thecatapi.com/api/images/get?format=src&type=png');
        if(!$kittypic){
            $kittypic = @file_get_contents('http://thecatapi.com/api/images/get?format=src&type=png');
        }

        if(!$kittypic){
            return false;
        }

        $kittypic = $this->copyImage($kittypic,'png');
        $pic = file_get_contents($data['picture']['large']);
        $pic = $this->copyImage($pic,'jpg');

        $countryname = file_get_contents('http://restcountries.eu/rest/v1/alpha?codes='.$data['nat']);

        $countryname = json_decode($countryname,true);
        $countryname = $countryname[0]['name'];

        $location = ThirdpartyServices::addressToCoordinates($this->gid,$countryname,$data['location']['city'],$data['location']['street']);

        if(!$location){
            $location = ThirdpartyServices::addressToCoordinates($this->gid,$countryname,$data['location']['city']);
        }

        $vars = $this->varcontent;

        $vars['oauth_code'] = '';
        $vars['oauth_userid'] = '';
        $vars['email'] = $data['email'];
        $vars['real_name'] = $data['name']['first'] . ' ' .$data['name']['last'];
        $vars['screen_name'] = 'nick_'.$data['name']['first'];
        $vars['gender'] = $data['gender'] == 'female' ? 'woman' : 'man';
        $vars['lat'] = isset($location['lat']) ? $location['lat'] : $vars['lat'];
        $vars['lon'] = isset($location['lon']) ? $location['lon'] : $vars['lon'];
        $vars['profilepic'] = $pic;
        $vars['profilepic2'] = $kittypic;
        $vars['country'] = $countryname;
        $vars['zip'] = $data['location']['postcode'];
        $vars['city'] = $data['location']['city'];
        $vars['phone'] = $data['phone'];

        /* create the user */
        $play = $this->createUser($data);

        if ( empty($play) ) {
            return false;
        }

        /* save users variables, modeling after the current user */
        AeplayVariable::saveNamedVariablesArray($vars,$play,$this->gid);

        /* save entry to matching table */
        $match = new MobilematchingModel();
        $match->game_id = $this->gid;
        $match->play_id = $play;
        $match->lat = $vars['lat'];
        $match->lon = $vars['lon'];
        $match->gender = $vars['gender'];
        $match->match_always = 1;
        $match->insert();

        return true;
    }

    public function createUser($data){
        $userid = @Controller::quickRegistration($data['email'],$data['phone'],'/aeplay/home/gamehome?gid=' .$this->gid,$this->gid,'',
            $this->appinfo->api_key,false,false,0,false,false,false,true);

        if(!$userid){
            return false;
        }

        $game = new Aegame;
        $game->gid = $this->gid;
        $game->userid = $userid;
        $play = $game->playGame(false,false);
        Datarecipes::createActionsFromRecipe($this->gid,$userid,$play,false,$this->api_version,$this->query);
        return $play;
    }


    public function copyImage($data,$extension){
        $imagepath = Controller::getDocumentsFolder($this->gid);
        if(!is_dir($imagepath.'/thirdparties')){
            mkdir($imagepath.'/thirdparties',0777,true);
        }

        $md5 = md5($data);
        $filename = 'tmp_' .$md5 .'.'.$extension;
        file_put_contents($imagepath.'/thirdparties/'.$filename,$data);

        return $filename;
    }

    public function deteleFakeUsers(){
        $sql = "SELECT * FROM ae_game_play_variable WHERE `value` LIKE '%dummydomain.com'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($rows as $row){
            Aeplay::model()->deleteByPk($row['play_id']);
        }
    }

    public function generateFakeUsers(){

        /* this will prevent the script from running twice */
        $sql = "SELECT SQL_NO_CACHE * FROM ae_game_play_variable WHERE `value` LIKE '%dummydomain.com'";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        if($rows){
            //return false;
        }

        $path = Yii::getPathOfAlias('application.modules.aelogic.packages.actionMobiledebug.data');
        $men = file_get_contents($path.'/men.txt');
        $women = file_get_contents($path.'/women.txt');

        $men = explode(chr(10), $men);
        $women = explode(chr(10), $women);

        $imagepath = Controller::getDocumentsFolder($this->gid);
        $imagepath = $imagepath . DS . 'fakedata';

        $raw_menpics = scandir($imagepath. DS . 'men');
	    $raw_womenpics = scandir($imagepath. DS . 'women');

	    $menpics = array();
        $womenpics = array();

        foreach($raw_menpics as $menpic){
            if(substr($menpic, 0,1) == '.') {
            	continue;
            }

            $file_man = $imagepath . DS . 'men' . DS . $menpic;

            if ( @is_dir($file_man) ) {
	            $many = scandir($file_man);

	            // Valid
	            if ( $many ) {
					$menpics[] = $menpic;
	            }
            } else {
	            $menpics[] = $menpic;
            }

        }

	    foreach($raw_womenpics as $womanpic){
		    if(substr($womanpic, 0,1) == '.') {
			    continue;
		    }

		    $file_women = $imagepath . DS . 'women' . DS . $womanpic;

		    if ( @is_dir($file_women) ) {
			    $many = scandir($file_women);

			    // Valid
			    if ( $many ) {
				    $womenpics[] = $womanpic;
			    }
		    } else {
			    $womenpics[] = $womanpic;
		    }

	    }

	    foreach($men as $man){

	    	$pic_men = array();

		    $man = explode(';', $man);
            $pic = array_shift($menpics);

            if(is_dir($imagepath . DS .'men' . DS .$pic)){
                $many = @scandir($imagepath . DS .'men' . DS .$pic);

                foreach($many as $item){
                    if(substr($item, 0,1) != '.'){
	                    $pic_men[] = 'men' . DS . $pic . DS . $item;
                    }
                }

            } else {
	            $pic_men[] = 'men' . DS . $pic;
            }

            $this->createFakeUser($pic_men,$man,'man');
        }

	    foreach($women as $woman){

		    $pic_women = array();

		    $woman = explode(';', $woman);
		    $pic = array_shift($womenpics);

		    if(is_dir($imagepath . DS .'women' . DS .$pic)){
			    $many = @scandir($imagepath . DS .'women' . DS .$pic);

			    foreach($many as $item){
				    if(substr($item, 0,1) != '.'){
					    $pic_women[] = 'women' . DS . $pic . DS . $item;
				    }
			    }

		    } else {
			    $pic_women[] = 'women' . DS . $pic;
		    }

		    $this->createFakeUser($pic_women,$woman,'woman');
	    }

    }

    public function copyImageFakeUser($pic){
        $imagepath = Controller::getDocumentsFolder($this->gid);

        if(!is_dir($imagepath.'/thirdparties')){
            mkdir($imagepath.'/thirdparties',0777,true);
        }

        $data = file_get_contents($imagepath.'/fakedata/'.$pic);

        $extension = substr($pic,-3);
        $md5 = md5($data);
        $filename = 'tmp_' .$md5 .'.'.$extension;
        file_put_contents($imagepath.'/thirdparties/'.$filename,$data);
        return $filename;
    }


    public function createFakeUser($pics,$userdata,$sex){

        $num = 1;

        /* prevents duplicates */
        //$test = AeplayVariable::model()->findAllByAttributes(array('value' => $userdata[0]));

        $sql = "SELECT SQL_NO_CACHE * FROM ae_game_play_variable WHERE `value` = '".$userdata[0] ."'";
        $rows = @Yii::app()->db->createCommand($sql)->queryAll();

        if($rows){
            return false;
        }

	    foreach ( $pics as $pic ) {
		    if(!isset($profilepic)){
			    $vars['profilepic'] = $this->copyImageFakeUser($pic);
		    } else {
			    $varname = 'profilepic'.$num;
			    $num++;
			    $vars[$varname] = $this->copyImageFakeUser($pic);;
		    }
        }

	    // $data['profilepic'] = $this->copyImageFakeUser($pic);

        $name = explode(' ',$userdata[0]);

        $vars['oauth_code'] = '';
        $vars['oauth_userid'] = '';
        $vars['email'] = Helper::generateShortcode(20) .'@' .'dummydomain.com';
        $vars['real_name'] = $userdata[0];
        $vars['firstname'] = $name[0];
        $vars['real_name'] = isset($name[1]) ? $name[1] : 'hidden';
        $vars['screen_name'] = $userdata[0];
        $vars['age'] = $userdata[1];
        $vars['gender'] = $sex;
        $vars['lat'] = '42.'.rand(1000,9999);
        $vars['lon'] = '23.'.rand(1000,9999);
        $vars['relationship_status'] = $userdata[2];
        $vars['seeking'] = $userdata[3];
        $vars['religion'] = $userdata[4];
        $vars['diet'] = $userdata[5];
        $vars['tobacco'] = $userdata[6];
        $vars['alcohol'] = $userdata[7];
        $vars['zodiac_sign'] = $userdata[8];
        $vars['profile_comment'] = $userdata[9];
        $vars['profile_location_invisible'] = 1;
        $vars['reg_phase'] = 'complete';
        $vars['fake_user'] = 1;

        $data['email'] = $vars['email'];
        $data['phone'] = false;

        /* create the user */
        $play = $this->createUser($data);

        if ( empty($play) ) {
            return false;
        }

        /* save users variables, modeling after the current user */
        AeplayVariable::saveNamedVariablesArray($vars,$play,$this->gid);

        /* save entry to matching table */
        $match = new MobilematchingModel();
        $match->game_id = $this->gid;
        $match->play_id = $play;
        $match->lat = $vars['lat'];
        $match->lon = $vars['lon'];
        $match->gender = $sex;
        $match->match_always = 1;
        $match->insert();

        return true;
    }

}