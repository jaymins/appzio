<?php

class classifiedsMobilematchingSubController extends MobilematchingController {

    public $mymatchtitleimage = 'my-matches-text.png';
    public $filtering = true;
    public $resettime = 86400; // in seconds, 86400 for 24h

    public function notFound(){

        $this->configureBackground('actionimage1');

        $params['crop'] = 'round';
        $params['width'] = '120';
        $params['margin'] = '100 0 0 0';
        $params['priority'] = '9';

        $img = $this->getSavedVariable('profilepic');

        if($img){
            $tit[] = $this->getImage($img,$params);
            $row[] = $this->getColumn($tit,array('width' => '100%','text-align' => 'center'));
        }

        $row[] = $this->getSpacer('50');

        $row[] = $this->getText('{#sorry_no_one_new#}',array('style' => 'register-text-step-2'));

        /*
        $row[] = $this->getSpacer(20);
        $row[] = $this->getTextbutton('{#invite_friends#}',array(
            'id' => 'share-button',
            'action' => 'share',
            'style' => 'general_button_style_red',
        ));
        */

        $row[] = $this->getTextbutton('{#scan_again#}',array('style' => 'general_button_style_red','id' => '12345'));

        $this->setFooter();

/*        $img = $this->getImageFileName('match-bg.jpg');*/

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
/*            'background-image' => $img,
            'background-size'=>'cover'*/
        ));

    }

    /* actually handles the intro action */
    public function activateLocationTracking(){
        if($this->getConfigParam('intro_action')){

            $timetowait = $this->getConfigParam('intro_delay') ? $this->getConfigParam('intro_delay') : $this->resettime;
            $intro_repeats = $this->getConfigParam('intro_repeats') ? $this->getConfigParam('intro_repeats') : 3;
            $current_repeats = $this->getSavedVariable('intro_repeats') ? $this->getSavedVariable('intro_repeats') : 0;

            $var = $this->getSavedVariable('intro_action') ? $this->getSavedVariable('intro_action') : 0;

            if(is_numeric($var) AND $var+$timetowait < time()){

                if($current_repeats < $intro_repeats){
                    $onclick = new stdClass();
                    $onclick->action = 'open-action';
                    $onclick->action_config = $this->getConfigParam('intro_action');
                    $onclick->open_popup = 1;
                    $this->data->onload[] = $onclick;
                    $this->saveVariable('intro_repeats',$current_repeats+1);
                }
            }
        }

    }

    public function getSilaImage($image,$params){
        $bg = $this->getImageFileName('sila-ring-photo.png');
        $image = $this->getImage($image,$params);
        $ret = $this->getColumn(array($image),array('background-image' => $bg,'background-size' => 'contain','width' => '80','height' => '80', 'margin' => '0 10 0 10'));
        return $ret;
    }

    public function getSilaMatches(){
        $gender = $this->getSavedVariable('gender');
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;

        if ( $gender == 'female' OR $gender == 'woman') {
            return $this->matchingLogicForWomen($search_dist);
        }

        /* note: caching etc. applies only to men, women have a dynamic list */
        $lastupdate = $this->getSavedVariable('last_match_update');
        $current_matches = $this->getSavedVariable('current_matches');

        if ($lastupdate AND ($lastupdate + $this->resettime) > time() AND $current_matches) {
            $obj = @json_decode( $current_matches, true );

            if(is_array($obj) AND !empty($obj)){
                $this->filtering = false;
                return $obj;
            }

        } else if ( $current_matches ) {

            $obj = @json_decode( $current_matches, true );

            if(is_array($obj)){

                foreach ($obj as $user){
                    $playid = $user['play_id'];
                    $this->mobilematchingobj->playid_otheruser = $playid;

                    // making sure its not a two-way match
                    $status = $this->mobilematchingobj->getTwoWayMatchStatus();
                    if(!$status){
                        $this->mobilematchingobj->skipMatch();
                    }
                }
            }
        }


        /* special addition where we first show all that have matched with this lady.
            If there are not enough of them, we also query for "normal matches" */

        $users = $this->mobilematchingobj->getUsersNearbySila($search_dist);


        // Get already un-matched users also
        if ( empty($users) ) {
            //$users = $this->mobilematchingobj->getUsersNearbySila($search_dist, $exclude_skipped = true);
        }

        $this->saveVariable('last_match_update',time());
        $this->saveVariable('current_matches',json_encode($users));
        return $users;
    }

    private function matchingLogicForWomen($search_dist){
        $this->mobilematchingobj->playid = $this->playid;
        $users = $this->mobilematchingobj->getUsersWhoHaveMatchedMe( $this->resettime );

        if(count($users) < 5){
            $more = $this->mobilematchingobj->getUsersNearbySila($search_dist);

            if(!empty($users)){
                foreach($users as $user){
                    $id = $user['play_id'];
                    $exhausted[$id] = true;
                }

                foreach($more as $user){
                    $id = $user['play_id'];
                    if(!isset($exhausted[$id])){
                        $users[] = $user;
                        if(count($users) == 5){
                            break;
                        }
                    }
                }
            } else {
                $users = $more;
            }
        }

        return $users;
    }

    private function cleanupMatchArray($ids){
        $users = json_decode($this->getSavedVariable('current_matches'),true);
        $out = array();

        if(!empty($users)){
            foreach($users as $user){
                if(in_array($user['play_id'],$ids)){
                    $out[] = $user;
                }
            }

            $this->saveVariable('current_matches',json_encode($out));
        }
    }

    public function showMatches($skipfirst=false){

        $this->migrateNotifications();

        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $users = $this->getSilaMatches();

        $this->putUsersToDebug( $users );

        $detail_view = $this->requireConfigParam('detail_view');
        $counter = 0;
        $ids = array();

        if(empty($users)){
            $this->notFound();
        } else {

            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';

            $row[] = $this->getText('',array('width' => '15'));
            $row[] = $this->getImage('sila-refresh.png',array('onclick' => $onclick,'height' => '20','margin' => '0 10 0 0'));
            $row[] = $this->getText('{#todays_matches#}',array('style' => 'main-title-sila-text'));

            $this->data->scroll[] = $this->getRow($row,array('style' => 'main-title-sila'));
            unset($row);

            $vars = $this->getAMatch($users);

            $itemcount = 0;

            /* didn't find any users */
            if($vars == false){
                return false;
            }

            foreach($vars as $i => $one){
                $count = 2;
                $piccount = 1;

                // we have distance and some other info on the query that we need
                if ( !isset($one['play_id']) ) {
                    continue;
                }

                if($itemcount == 4){
                    /* if we got here, it means we are here for the first time and we clean up a little */
                    $this->cleanupMatchArray($ids);
                    continue;
                }

                $id = $one['play_id'];
                $ids[] = $id;

                while ($count < 10) {
                    $n = 'profilepic' . $count;
                    if (isset($one[$n]) AND strlen($one[$n]) > 2) {
                        $piccount++;
                    }
                    $count++;
                }

                $distance = round($one['distance'], 0);
                $profilepic = isset($one['profilepic']) ? $one['profilepic'] : false;

                if(isset($one['private_photos']) AND $one['private_photos'] == 1){
                    $profilepic = 'sila-private-photos.png';
                    $one['profilepic'] = 'sila-private-photos.png';
                }

                if($this->filtering == true){
                    $filtering = $this->silaFiltering($one);
                } else {
                    $filtering = true;
                }

                $path = $_SERVER['DOCUMENT_ROOT'] .$profilepic;

                if(file_exists($path) AND filesize($path) > 40){
                    $customfile = true;
                } else {
                    $customfile = false;
                }

                if($profilepic AND $distance < $search_dist AND $filtering AND ($customfile OR $profilepic == 'sila-private-photos.png')) {

                    if(!$skipfirst){

                        $rows[] = $this->getText('',array('height' => '1', 'background-color' => '#909090'));
                        $rows[] = $this->getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i);
                        $rows[] = $this->getSpacer('5');

                        // Add a "suggested" label to the shown user
                        if ( $one['gender'] == 'woman' ) {
                            $this->mobilematchingobj->playid_otheruser = $id;
                            $this->mobilematchingobj->makeSuggested();
                        }

                        $swipestack[] = $this->getColumn($rows);
                        $itemcount++;

                        unset($rows);

                    } else {
                        $skipfirst = false;
                    }

                } else {

                    /* its important that we exclude non-matches as otherwise we will sooner or later
                    run out of matches as only the skipped get excluded by query, rest is handled here in php */
                    if(!$filtering){
                        $this->addToDebug('Ignored '.$id);
                        $this->mobilematchingobj->playid_otheruser = $id;
                        $this->mobilematchingobj->skipMatch();
                    } else {
                        $this->addToDebug('Skipped for now');
                    }

                }

            }

            if(empty($swipestack)){
                $this->notFound();
                return false;
            }

            $this->data->scroll[] = $this->getColumn($swipestack);
            $this->data->scroll[] = $this->getText('',array('height' => '1', 'background-color' => '#909090'));
            $this->setFooter();

        }

    }

    public function silaFiltering($one){

        if(!$this->filterByAge($one)){
            return false;
        }

        if(!$this->filterByReligion($one)){
            return false;
        }

        if($this->getSavedVariable('dont_match_nearby')){
            if($one['distance'] < 45){
                return false;
            }
        }

        if(isset($one['hide_user']) AND $one['hide_user'] == '1'){
            return false;
        }

        return true;

    }

    public function itsAMatch($id=false){
        //$this->data->scroll[] = $this->getImage('redbg.png');

        $this->configureBackground('actionimage2');

        $cachepointer = $this->playid .'-matchid';
        Appcaching::setGlobalCache($cachepointer,$id);

        if(!$id){
            $this->initMobileMatching();
            $id = $this->mobilematchingobj->getPointer();
        }

        $vars = AeplayVariable::getArrayOfPlayvariables($id);
        $chatid = $this->requireConfigParam('chat');

        $params['margin'] = '30 0 0 0';
        $row[] = $this->getImage('its-a-match2.png',$params);

        $textstyle['margin'] = '0 0 0 0';
        $textstyle['color'] = '#000000';
        $textstyle['text-align'] = 'center';

        $row[] = $this->getText('{#you_have_a_match_with#} ' .$this->getFirstName($vars),$textstyle);

        $params['crop'] = 'round';
        $params['margin'] = '10 10 0 10';
        $params['width'] = '107';
        $params['text-align'] = 'center';
        $params['border-width'] = '3';
        $params['border-color'] = '#ffffff';
        $params['border-radius'] = '50%';
        $params['priority'] = '9';

        $profilepic = isset($vars['profilepic']) ? $vars['profilepic'] : 'photo-placeholder.jpg';

        $pics[] = $this->getImage($profilepic,$params);
        $pics[] = $this->getImage($this->getVariable('profilepic'),$params);

        $row[] = $this->getRow($pics,array('margin' => '80 30 0 30','text-align' => 'center'));

        $row[] = $this->getSpacer('50');
        $row[] = $this->getImagebutton('btn-snd-msg.png',$this->getTwoWayChatId($id),false,array(
            'sync_open' => '1', 'action' => 'open-action', 'config' => $chatid));
        $textstyle['margin'] = '15 0 10 0';
        $textstyle['text-align'] = 'center';

        $row[] = $this->getSpacer(15);
        $row[] = $this->getImagebutton('btn-keep-playing.png','keep-playing');

        $this->data->scroll[] = $this->getColumn($row,array(
            //'background-color' => '#e33124',
            //'height' => '100%'
        ));

        $this->setFooter();
    }

    public function swipe($swipestack){
        $nope = 'nope.png';
        $like = 'like.png';

        if ( $this->getConfigParam( 'actionimage3' ) ) {
            $nope = $this->getConfigParam( 'actionimage3' );
        }

        if ( $this->getConfigParam( 'actionimage4' ) ) {
            $like = $this->getConfigParam( 'actionimage4' );
        }

        return $this->getSwipestack($swipestack,array(
            'margin' => '20 20 0 20',
            'overlay_left' => $this->getImage($nope,array('text-align' => 'right','width'=> '400','height'=> '400')),
            'overlay_right' => $this->getImage($like,array('text-align' => 'left','width'=> '400','height'=> '400'))
        ));
    }

    public function getBtns($id,$detail_view,$i){
        $column[] = $this->getImagebutton('btn1-refresh2.png', 'refresh', false, array('width' => '25%', 'margin' => '0 0 0 0'));
        $column[] = $this->getImagebutton('btn2-no2.png', 'no_' . $id, false, array('width' => '25%', 'margin' => '0 0 0 0','action' => 'swipe-left'));
        $column[] = $this->getImagebutton('btn3-yes2.png', 'yes_' . $id, false, array('width' => '25%','action' => 'swipe-right'));
        $column[] = $this->getImagebutton('btn4-info2.png', $id, false, array('width' => '25%', 'sync_open' => '1', 'action' => 'open-action', 'config' => $detail_view));
        return $this->getRow($column, array('align' => 'center', 'noanimate' => true));
    }

    public function getIncomingRequests() {
        $matches = $this->mobilematchingobj->getMyInbox( $this->resettime );
        $this->data->scroll[] = $this->getSettingsTitle('{#inbox#} ({#valid_for_24h#})');
        $this->matchList($matches);
    }

    public function getOutgoingRequests() {
        $matches = $this->mobilematchingobj->getMyOutbox( $this->resettime );
        $this->data->scroll[] = $this->getSettingsTitle('{#outbox#} ({#valid_for_24h#})');
        $this->matchList($matches);
    }

    public function getCard($profilepic,$detail_view,$distance,$piccount,$id,$one,$i){

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->sync_open = 1;
        $onclick->back_button = 1;
        $onclick->id = $id;
        $onclick->action_config = $detail_view;

        $options['imgwidth'] = 500;
        $options['imgheight'] = 500;
        $options['width'] = 70;
        $options['height'] = 90;
        $options['float'] = 'center';
        $options['margin'] = '5 8 0 5';
        $options['border-radius'] = '35';
        $options['imgsop'] = 'yes';
        $options['priority'] = '9';

        $options['mask'] = 'shapemask.png';
        $options['frame'] = 'frame.png';

        $page[] = $this->getImage($profilepic, $options);

        if(isset($one['birth_day']) AND isset($one['birth_month']) AND $one['birth_year']){
            $age = Controller::getAge($one['birth_day'],$one['birth_month'],$one['birth_year']);
        } else {
            $age = '??';
        }

        $texparam['color'] = '#000000';
        //$texparam['background-color'] = '#80000000';

        $texparam['font-size'] = 18;
        $texparam['margin'] = '2 0 0 0';
        $texparam['padding'] = '0 0 0 0';
        $texparam['text-align'] = 'left';

        $texts[] = $this->getSpacer('5');

        $name = isset($one['screen_name']) ? $one['screen_name'] : $this->getFirstName($one);

        $texts[] = $this->getText($name . ', ' . $age, $texparam);
        $texparam['font-size'] = 12;
        $texparam['color'] = '#565656';

        $city = isset($one['city']) ? $one['city'] : '{#city_hidden#}';
        $country = isset($one['country']) ? $one['country'] : '{#country_hidden#}';
        $texts[] = $this->getText($city .', ' .$country, $texparam);

        unset($texparam['background-color']);
        unset($texparam['padding']);
        $texparam['margin'] = '8 5 0 5';
        $texparam['font-size'] = 12;
        $texparam['text-align'] = 'left';

        $toolbar[] = $this->getImage('sila-marker.png',array('width' => '20', 'margin' => '4 0 0 0','float' => 'bottom'));
        $toolbar[] = $this->getText($distance . ' km', $texparam);

        $toolbar[] = $this->getImage('sila-camera2.png', array('width' => '20', 'margin' => '0 5 0 5','vertical-align' => 'bottom'));
        $toolbar[] = $this->getText($piccount, $texparam);
        $texts[] = $this->getRow($toolbar, array('margin' => '0 0 0 0','text-align' => 'left', 'vertical-align' => 'middle'));

        $page[] = $this->getColumn($texts,array('vertical-align' => 'top'));
        $page[] = $this->getImage('sila-chat.png',array('width' => '40', 'margin' => '20 15 0 10','float' => 'bottom'));

        return $this->getRow($page, array(
            'onclick' => $onclick,
            'margin' => '5 0 5 0'
            //'background-color' => '#0D000000'
        ));

    }

    public function migrateNotifications() {
        if ( $this->getSavedVariable( 'notify' ) == '' ) {
            $this->saveVariable( 'notify', 1 );
        }
    }

    public function putUsersToDebug( $users ) {

        if ( !file_exists('mobile_sila_logs') ) {
            mkdir('mobile_sila_logs', 0777, true);
        }

        $path = 'mobile_sila_logs/log-'. $this->playid .'.txt';

        $contents = array();
        if ( file_exists( $path ) ) {
            $contents = @file_get_contents( $path );
        }

        if ( $contents ) {
            $contents = json_decode( $contents, true );
        }

        $timestamp = strtotime( 'today midnight' );

        // Don't save data, already saved
        if ( isset($contents[$timestamp]) ) {
            return false;
        }

        $users_data = array();

        foreach ($users as $user) {
            $users_data[] = $user['play_id'];
        }

        $contents[$timestamp] = $users_data;
        $file_data = json_encode( $contents );

        file_put_contents($path, $file_data);
        // file_put_contents($path, $file_data, FILE_APPEND);
    }

}