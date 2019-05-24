<?php


namespace Bootstrap\Models;
use ThirdpartyServices;

trait DataHelpers {


    /**
     * http://ae.com/api/19332514d43c75f60cedd159fcce2ff0/actions/refreshaction?actionid=417327&tabid=1&fb_login=false
     */
    public function getCurrentActionCallUrl(){
        $base = \Yii::app()->params['siteURLssl'];
        
        if(substr($base, -1) != '/'){
            $base .= '/';
        }

        $token = $this->access_token;
        $tab = $this->getActiveTab();
        $actionid = $this->actionid;
        return $base.'api/'.$token.'/actions/refreshaction?actionid='.$actionid.'?tabid='.$tab;
    }


    public function getMobileConfigItem($key){

        $settings = json_decode($this->mobilesettings->config_main, true);

        if(isset($settings[$key]) AND $settings[$key]){
            return $settings[$key];
        }

        return false;
    }


    public function getActiveTab(){
        if(isset($this->query['tabid'])){
            return $this->query['tabid'];
        }

        return 1;
    }

    /* @var $this BootstrapModel */

    /**
     * Get country codes from JSON file in name - country code pairs
     *
     * @return array
     */
    public function getCountryCodes(){
        $path = \Yii::getPathOfAlias('application.modules.aelogic.Bootstrap.Data');
        $file = $path .'/countrycodes.json';
        $cities = file_get_contents($file);
        $cities = json_decode($cities,true);
        $output = array();

        foreach ($cities['countries'] as $country){
            $name = $country['name'];
            $output[$name] = $country['code'];
        }

        return $output;
    }


    /**
     * Get timezones from JSON file in name - country code pairs
     *
     * @return array
     */
    public function getTimezones(){
        $path = \Yii::getPathOfAlias('application.modules.aelogic.Bootstrap.Data');
        $file = $path .'/timezones.json';
        $cities = file_get_contents($file);
        $cities = json_decode($cities,true);
        $output = array();

        foreach ($cities as $country){
            $name = $country['title'];
            $output[$name] = $name .' - '.$country['name'] .' ('.$country['gmt'].')';
        }

        return $output;
    }


    public function convertBirthDateToAge($year,$month='01',$day='01'){
        $date = new \DateTime("$day.$month.$year");
        $now = new \DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }

    public function getMenuData($id){
        return \AeMenuItems::model()->findByAttributes(['menu_id' => $id]);
    }

    /**
     * Returns country code based on users location
     *
     * @return mixed
     */
    public function getCountryCode(){
        $codes = $this->getCountryCodes();
        
        if(!$this->getSavedVariable('country')){
            $this->setUserAddress();
        }

        $country = $this->getSavedVariable('country');
        array_flip($codes);

        if(isset($codes[$country])){
            return $codes[$country];
        }
    }

    /**
     * Set current user address
     *
     * @return bool
     */
    public function setUserAddress($force=true){
        if(!$this->getSavedVariable('lat')){
            return false;
        }

        if($this->getSavedVariable('country') AND $force==false){
            return false;
        }

        $vars = $this->coordinatesToAddress($this->getSavedVariable('lat'), $this->getSavedVariable('lon'));

        if(isset($vars)){
            $this->saveNamedVariables($vars);
        }

        $this->loadVariables();
        $this->loadVariableContent();
    }

    public function timeElapsedString($datetime, $full = false) {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' {#ago#}' : '{#just_now#}';
    }


    public function coordinatesToAddress($lat,$lon){
        $location = ThirdpartyServices::geoAddressTranslation($lat, $lon, $this->appid);

        if(!$location){
//            $this->setError('Location could not be fetched, make sure you have Google API key defined');
        }

        $vars = array();

        if(isset($location['city'])){ $vars['city'] = $location['city']; }
        if(isset($location['country'])){ $vars['country'] = $location['country']; }
        if(isset($location['county'])){ $vars['county'] = $location['county']; }
        if(isset($location['zip'])){ $vars['zip'] = $location['zip']; }
        if(isset($location['street'])){ $vars['street'] = $location['street']; }

        return $vars;
    }

    public function addressToCoordinates($country,$city=false){
        return $location = ThirdpartyServices::addressToCoordinates($this->appid,$country,$city);
    }

    public function findClosestVenue($lat,$lon,$keywords='restaurants,bars,cafes'){
        return ThirdpartyServices::findClosestVenue($lat, $lon, $this->appid,300,$keywords);
    }

    public function findClosestVenues($lat,$lon,$keywords='bar'){
        return ThirdpartyServices::findClosestVenues($lat, $lon, $this->appid,300,$keywords);
    }

    /**
     * Find play based on two different variables
     *
     * @param $var1
     * @param $var2
     * @param $var1_value
     * @param $var2_value
     */
    public function findPlayFromVariables($var1,$var2,$var1_value,$var2_value){

        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value FROM ae_game_play_variable AS tbl1
                LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id
                LEFT JOIN ae_game_variable AS vartable2 ON tbl2.variable_id = vartable2.id

                WHERE tbl1.`value` = :var1_value
                AND tbl2.`value` = :var2_value
                AND vartable1.name = :var1_name
                AND vartable2.name = :var2_name
                AND vartable1.game_id = :gid
                AND vartable2.game_id = :gid

                ORDER BY tbl1.play_id DESC
                ";


        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':var1_value' => $var1_value,
                ':var2_value' => $var2_value,
                ':var1_name' => $var1,
                ':var2_name' => $var2,
                ':gid' => $this->appid
            ))
            ->queryAll();

        if(isset($rows[0]['play_id'])){
            return $rows[0]['play_id'];
        }

        return false;
    }


    /**
     * Note: this will return only the latest user with this value & it will exclude
     * the current user by default
     *
     * @return mixed
    */
    public function findPlayFromVariable($varname,$varvalue,$include_current_user=false){

        if($include_current_user){
            $add = '';
        } else {
            $add = 'AND tbl1.play_id <>' .$this->playid;
        }

        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value FROM ae_game_play_variable AS tbl1
                LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id

                WHERE tbl2.`value` = :varvalue
                AND vartable1.game_id = :gid
                AND vartable1.name = :varname
                $add
              
                ORDER BY tbl1.play_id DESC
                ";


        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':varname' => $varname,
                ':varvalue' => $varvalue,
                ':gid' => $this->appid
            ))
            ->queryAll();

        if(isset($rows[0]['play_id'])){
            return $rows[0]['play_id'];
        }

        return false;
    }

    public function getActionThemeByPermaname($permaname){
        $cachename = 'theme'.$permaname.$this->appid;
        $cache = \Appcaching::getGlobalCache($cachename);

        if($cache){
            return $cache;
        }

        \Appcaching::getAppCache('theme'.$permaname, $this->appid);

        $id = $this->getActionidByPermaname($permaname);
        if($id){
            $config = \Aeaction::getActionConfig($id);
            if(isset($config->article_action_theme) AND $config->article_action_theme){
                \Appcaching::setGlobalCache($cachename, $config->article_action_theme,1200);
                return $config->article_action_theme;
            }
        }
    }

    /**
     * Will create a button for adding a calendar event
     *
     * @param array $parameters
     * <code>
     * $array = array(
     * 'starttime' => time()+700
     * 'endtime' => time()+1400,
     * 'organizer' => 'My Name',
     * 'organizer_email' => 'myemail@domain.com',
     * 'subject' => 'Checkup',
     *
     * // optional
     * 'description' => 'Event description',
     * 'location' => 'Event location',
     * 'repeat_daily_until' => unixtime,
     * 'repeat_weekly_until' =>  unixtime,
     * 'repeat_monthly_until' =>  unixtime,
     * 'repeat_yearly_until' =>  unixtime,
     * 'url' =>  'https://appzio.com',
     * 'invitees' => array('invitee1@appzio.com','invitee2@appzio.com'),
     *
     * // if this is set to true, we won't email the invitations to invited people
     * 'dont_send_invites' => true
     *
     * );

     * @return \string
     */
    public function getCalendarFile($parameters){
        /** @var BootstrapView $this */

        $calpath = 'documents/games/' . $this->model->appid .'/calendars/';
        $path = $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .$calpath;

        if(!is_dir($path)){
            mkdir($path,0777);
        }

        $template = $this->getCalendarTemplate($parameters);
        $filename = time() .\Helper::generateShortcode(5) .'.ics';
        file_put_contents($path.$filename,$template);

        if(isset($parameters['invitees']) AND is_array($parameters['invitees']) AND $parameters['invitees']){
            if(!isset($parameters['dont_send_invites'])){
                foreach($parameters['invitees'] as $invitee){
                    // this should add the email to be sent using the generated file as an attachment
                }
            }
        }

        return $path.$filename;

    }

    /**
     * Return the user's timezone based on his current location
     *
     * @return array
     */
    public function getTimezone($cur_lat, $cur_long, $country_code = '', $timetolive) {

        $cache = \Appcaching::getGlobalCache('timezone-asked'.$this->playid);

        if ( $cache ) {
            return array();
        }

        $timezone_ids = ($country_code) ? \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $country_code)
            : \DateTimeZone::listIdentifiers();

        if ($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = array();
            $tz_distance = 0;

            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach($timezone_ids as $timezone_id) {
                    $timezone = new \DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat = $location['latitude'];
                    $tz_long = $location['longitude'];

                    $theta = $cur_long - $tz_long;
                    $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                        + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));

                    if (!$time_zone || $tz_distance > $distance) {

                        $dateTime = new \DateTime("now", $timezone);
                        $offset = $timezone->getOffset( $dateTime );

                        $time_zone = array(
                            'timezone_id' => $timezone_id,
                            'offset_in_seconds' => $offset,
                        );

                        $tz_distance = $distance;
                    }
                }

            }

            \Appcaching::setGlobalCache('timezone-asked'.$this->playid,true, $timetolive);

            return $time_zone;
        }

        return array();
    }

    public function getNickname($content,$age=true){
        $name = isset($content['nickname']) ? $content['nickname'] : false;

        if($name){
            return $name;
        }

        if(!$name){
            $name = isset($content['name']) ? $content['name'] : false;
        }

        if(!$name){
            $name = isset($content['firstname']) ? $content['firstname'] : false;
        }

        if(!$name){
            $name = isset($content['real_name']) ? $content['real_name'] : false;
        }

        if(stristr($name, ' ')){
            $name = explode(' ', $name);
            $name = $name[0];
        }

        if(!$name){
            $name = '{#anonymous#}';
        }

        if(!$age){
            return $name;
        }

        if(isset($content['age']) AND $content['age']){
            $name .= ', '.$content['age'];
        } elseif(isset($content['birth_year']) AND is_numeric($content['birth_year'])){
            $year = (int) $content['birth_year'];
            $age = date('Y') - $year;
            $name .= ', ' .$age;
        }

        return $name;

    }

}