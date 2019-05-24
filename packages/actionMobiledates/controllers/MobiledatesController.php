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
Yii::import('application.modules.aelogic.article.controllers.*');

Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');
Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

Yii::import('application.modules.aetask.models.*');

class MobiledatesController extends ArticleController {

    public $configobj;
    public $theme;

    public $gamevariables;

    public $action;
    public $branch_id;
    public $action_id;

    public $app_id;
    public $actions;

    public $phase;
    public $date_type;
    public $action_mode;

    public $mobilematchingobj;
    public $mobilechatobj;

    public $data;

    public $rate_var = 'rating_results';
    public $date_id_var = 'active_date_id';
    public $tip_var = 'activity_tip_amount';
    public $tip_var_with_fee = 'activity_tip_amount_with_fee';

    public $requestsobj;

    public $wallet;
    public $wallet_track;

    public $allow_refresh = false;

    public $current_time;
    public $default_time;
    public $time_left_for_action;
    public $default_time_for_action;
    public $debug_msgs = array();


    public function adminHook() {

        if ( !isset($_GET['update-locations-coods']) ) {
            return false;
        }

        $this->getCurrentAction();

        $config = $this->action->config;

        if ( empty($config) ) {
            return false;
        }

        $config = json_decode( $config, true );

        if ( !isset($config['dates_sponsored_locations']) OR empty($config['dates_sponsored_locations']) ) {
            return false;
        }

        $locations = $config['dates_sponsored_locations'];
        $locations = explode(PHP_EOL, $locations);

        $geocoded_lcs = array();

        foreach ($locations as $location) {
            $date_coord = ThirdpartyServices::addressToCoordinates($this->app_id, $location);

            if ( empty($date_coord) ) {
                continue;
            }

            $geocoded_lcs[] = array(
                'place' => $location,
                'lat' => $date_coord['lat'],
                'lon' => $date_coord['lon'],
            );
        }

        if ( empty($geocoded_lcs) ) {
            return false;
        }

        $config['geocoded_locations'] = $geocoded_lcs;

        $this->action->config = json_encode($config);
        $this->action->update();
    }

    public function askPermissions() {
        
        $refresh_interval = 10;

        $initially_asked = $this->getVariable( 'initial_ask_time' );

        if (
            !$initially_asked OR
            ( $initially_asked + $refresh_interval < time() )
        ) {

            if ( $this->userid != $this->getSavedVariable('push_permission_checker') ) {
                $this->saveVariable('initial_ask_time', time());
                $this->saveVariable('push_permission_checker', $this->userid);
            }

            $pusher = $this->getOnclick('push-permission');
            $this->data->onload[] = $pusher;
        }

    }

    public function getCurrentAction() {
        $action = Aeaction::model()->findByPk( $this->action_id );

        if ( empty($action) ) {
            return false;
        }

        $this->action = $action;
    }

    public function getData(){

        // Initialize the Request Object
        $this->requestsobj = new MobiledatesModel();

        // Initialize the Wallet
        $this->wallet = new WalletModel();
        $this->wallet->playid = $this->playid;
        $this->wallet->createWallet( 100 );
        $this->wallet_track = new WalletTrackModel();

        // Every newly registered user would get a 5 stars rating
        // assigned by the system itself
        $this->addSystemRating();

        // Fix users gender
        $this->fixGender();

        // Update when the action was last opened
        $this->setLastOpenedStamp();

        // Default user preferences
        $this->setDefaultUserPreferences();
        $this->handleSubmissions();

        $this->initMobileMatching();

        // Global menuid dependencies
        if ( $this->menuid == 'make-a-plan' ) {
            $this->saveVariable('date_preferences', 'requestor');
            $this->saveVariable('to_requestor_stamp', time());
        }

        if ( $this->menuid == 'cancel-accepters-plans' ) {
            $this->cancelAcceptersPlans();
        }

        if ( $this->menuid == 'make-accepter' ) {
            $this->saveVariable('date_preferences', 'acceptor');
        }

        if ( preg_match('~change-acceptor~', $this->menuid) ) {
            $this->updatePreferences();
        }

        $this->phase = $this->getSavedVariable('date_phase') ? $this->getSavedVariable('date_phase') : 'step-1';
        $this->date_type = $this->getSavedVariable( 'date_preferences' );

        // This should be only performed manually
        if ( isset($_GET['do-migration']) ) {
            $this->performMigration();
        }

        // Determine whether the screen needs to be refresed
        $this->allowScreenRefresh();

        $this->action_mode = $this->getConfigParam('mode');

        if ( $this->getConfigParam('article_action_theme') ) {
            $theme = $this->getConfigParam('article_action_theme');

            Yii::import('application.modules.aelogic.packages.actionMobiledates.themes.'. $theme .'.controllers.*');

            $method = strtolower($theme);

            $this->data = new StdClass();
            if ( method_exists($this, $method) ) {
                $this->$method();
            }

            $this->addDebugToFile();

            return $this->data;
        }
        
    }

    public function addDebugToFile() {

        if ( empty($this->debug_msgs) ) {
            return false;
        }

        if ( !file_exists('mobile_plan_logs') ) {
            mkdir('mobile_plan_logs', 0777, true);
        }

        $path = 'mobile_plan_logs/planslog-'. $this->playid .'.txt';

        ob_start();
        echo '<pre>';
        print_r($this->debug_msgs);
        echo '</pre>';
        $textualRepresentation = ob_get_contents();
        ob_end_clean();

        file_put_contents($path, $textualRepresentation, FILE_APPEND);
    }

    public function allowScreenRefresh() {

        if ( $this->phase != 'step-1' OR $this->date_type != 'acceptor' ) {
            return false;
        }

        if ( !isset($this->varcontent['lat']) ) {
            return false;
        }

        $lat = $this->varcontent['lat'];
        $lon = $this->varcontent['lon'];

        $active_dates = $this->requestsobj->getActiveDates( $lat, $lon );

        if ( $active_dates ) {
            $this->allow_refresh = true;
        }

        /*
        $known_dates = $this->getVariable( 'previewed_dates' );

        if ( $known_dates ) {
            $known_dates = json_decode( $known_dates, true );
        }

        $active_dates_array = array();
        foreach ($active_dates as $date) {
            $active_dates_array[] = $date['play_id'];
        }
        
        if ( empty($known_dates) OR !array_intersect($active_dates_array, $known_dates) ) {
            $this->allow_refresh = true;
            $this->saveVariable( 'previewed_dates', json_encode( $active_dates_array ) );
        }
        */

    }

    public function addSystemRating() {

        // Ratings already exist
        if ( isset($this->varcontent[$this->rate_var]) OR !empty($this->varcontent[$this->rate_var]) ) {
            return false;
        }

        $user_ratings = array();

        $user_ratings[]['system-rating'] = 5;
        $user_ratings = json_encode( $user_ratings );

        $this->saveVariable( $this->rate_var, $user_ratings );
    }

    public function getStarsRatings( $remote_user_play_id ) {

        // Get the results from the current ratings
        $remote_user_vars = $this->getPlayVariables( $remote_user_play_id );

        $count = 5;
        $stars_to_show = 0;

        $columns = array();
        $stars_to_show = $this->getRequestRatings();

        for ($i = 0; $i < $count; $i++) {
            $rate_id = 'rate-' . ($i+1) . '|' . $remote_user_play_id;

            $star_image = ( $stars_to_show > $i ? 'star-full.png' : 'star.png' );
            $icons = $this->getImagebutton($star_image, $rate_id, array('style' => 'date-star-cell-image'));

            $columns[] = $this->getColumn(array(
                $icons,
            ), array( 'style' => 'date-star-cell' ));
        }

        $result = $this->getRow($columns, array( 'style' => 'date-stars-row' ));

        return $result;
    }

    public function getUserVoteResults( $remote_user_play_id, $remote_user_vars = false ) {

        if ( !$remote_user_vars ) {
            // Get the results from the current ratings
            $remote_user_vars = $this->getPlayVariables( $remote_user_play_id );
        }

        if ( !isset($remote_user_vars[$this->rate_var]) OR empty($remote_user_vars[$this->rate_var]) ) {
            return 'N/A';
        } else {
            $tc = 0;
            $results_total = json_decode( $remote_user_vars[$this->rate_var], true );

            foreach ($results_total as $vote_value) {
                foreach ($vote_value as $play_id => $rate) {
                    $tc += $rate;
                }
            }

            $people_voted = count( $results_total );
            $vote_results = $tc / $people_voted;
        }

        // return round($vote_results, 1);
        return number_format( (float)$vote_results, 1, '.', '' );
    }

    public function getImageSlider( $user_data ){

        $images = array();

        foreach ($user_data as $key => $value) {
            if ( preg_match('/profilepic/', $key) AND !empty($value) ) {
                $images[] = $value;
            }
        }

        if ( empty($images) ) {
            $images[] = 'image-placeholder.png';
        }

        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false,'imgwidth' => 800,'imgheight'=> 800, 'imgcrop' => 'yes'));
        
        $image_styles['imgwidth'] = '800';
        $image_styles['imgheight'] = '800';
        $image_styles['imgcrop'] = 'yes';
        // $image_styles['width'] = $this->screen_width - 62;
        // $image_styles['height'] = $this->screen_width;
        // $image_styles['width'] = $this->screen_width;
        $image_styles['not_to_assetlist']  = true;
        $image_styles['crop']  = 'round';
        $image_styles['margin']  = '1 1 1 1';
        $image_styles['priority']  = '9';
        $image_styles['image_fallback'] = $fallback_image_path;

        $navi_styles['margin'] = '-45 0 0 0';
        $navi_styles['align'] = 'center';

        $totalcount = count($images);
        $items = array();

        foreach ($images as $i => $image) {
            $scroll = array();
            $scroll[] = $this->getImage( 'overlay-large.png', array( 'crop' => 'round', 'floating' => '1' ) );
            $scroll[] = $this->getImage($image, $image_styles);
            $scroll[] = $this->getSwipeNavi($totalcount, ($i+1), $navi_styles);
            $items[] = $this->getColumn($scroll);
        }

        return $this->getSwipearea( $items );
    }

    public function handleSubmissions() {
            
        if ( !isset($this->menuid) OR empty($this->menuid) ) {
            return false;
        }

        $this->handleCurrentState();
        $this->handleRatings();
    }

    public function handleCurrentState() {
        
        if ( !preg_match('~step-~', $this->menuid) ) {
            return;
        }

        $special_cases = array(
            'step-1', 'step-2', 'step-3', 'step-4', 'step-5', 'step-6', 'step-accept', 'step-end-date',
        );

        foreach ($special_cases as $mid) {
            if ( preg_match("/$mid/", $this->menuid) ) {
                $tmp_menu_id = $mid;
                $this->saveVariable( 'date_phase', $tmp_menu_id );
                break;
            }
        }

    }

    public function handleRatings() {

        if ( !preg_match('~rate-~', $this->menuid) ) {
            return false;
        }

        $ratings_total = 0;
        $totally_voted = 0;

        $pieces = explode('|', $this->menuid);

        // Missing User ID
        if ( !isset($pieces[1]) ) {
            return false;
        }

        $current_rating = str_replace('rate-', '', $pieces[0]);
        $remote_user_play_id = $pieces[1];

        $remote_user_vars = $this->getPlayVariables( $remote_user_play_id );

        $user_ratings = array();

        if ( isset($remote_user_vars[$this->rate_var]) AND !empty($remote_user_vars[$this->rate_var]) ) {
            $user_ratings = json_decode( $remote_user_vars[$this->rate_var], true );
        }

        $user_voted_in_plan = $this->getRequestRatings();
        $user_votes = array();

        // If already voted in this session, just update the vote
        // We relay to get the most recent entry from the DB
        
        foreach ($user_ratings as $i => $rt_arr) {
            if ( array_key_exists($this->playid, $rt_arr) ) {
                $user_votes[] = $i;
            }
        }
            
        if ( $user_voted_in_plan ) {
            $index = array_pop($user_votes);
            // Update the last vote only
            $user_ratings[$index][$this->playid] = $current_rating;
        } else {
            $user_ratings[][$this->playid] = $current_rating;
        }

        $user_ratings = json_encode( $user_ratings );

        $this->saveRemoteVariable( $this->rate_var, $user_ratings, $remote_user_play_id );

        // Store the results to the DB Requests table
        $this->updateRatingDBRequest( $current_rating );
    }

    /*
    * Updates the user's current Match DB entry
    * "match_always" represents people, looking for other companion matches
    */
    public function createMatchRequest() {
        $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $this->playid ) );

        if ( empty($user) ) {
            return false;
        }

        $user->match_always = 1;
        $user->update();
        Aetask::registerTask($this->playid, 'mobiledates:create_plan', false, 'async', 660);

        return true;
    }

    /*
    * Return all Users, which matched the request of the current user
    */
    public function getMatchedUsers() {

        if ( empty($this->mobilematchingobj) ) {
            $this->initMobileMatching();
        }

        $users = $this->mobilematchingobj->getUsersNearby( 200, 'include', false );
        return $users;
    }

    /*
    * Return all Acceptors
    */
    public function getAcceptors() {
        $users = $this->mobilematchingobj->getUsersNearby( 200, 'acceptors', false );
        return $users;
    }

    /*
    * Return all Users, which are currently "requesting a match"
    */
    public function getUsersToMatch() {
        $users = $this->mobilematchingobj->getUsersNearby( 200, 'requestors', false );
        return $users;
    }

    public function getDistance( $distance ) {
        $distance = round($distance, 1) . ' KM away';
        return $distance;
    }

    public function calculateDistance( $remote_lat, $remote_lng ) {
        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid);

        $user_lat = $vars['lat'];
        $user_lng = $vars['lon'];

        return Helper::getDistance( $user_lat, $user_lng, $remote_lat, $remote_lng, 'K' );
    }

    /*
    * Gets the remote user play data
    * This method would now rely on the keyvalue storage table
    * in order to get correct play id
    */
    public function getRemoteUserPlayData( $type = false ) {

        $remote_play_id = '';

        if ( !empty($this->menuid) AND $type != 'state-end-date' ) {
            $pieces = explode('|', $this->menuid);
            if ( isset($pieces[1]) ) {
                $remote_play_id = $pieces[1];
                $data = $this->getPlayVariables( $remote_play_id );
                $this->saveVariable( 'last_opended_profile_id', $remote_play_id );

                if ( $data ) {
                    $data['user_play_id'] = $remote_play_id;
                    return $data;
                }
            }
        }

        $storage = new AeplayKeyvaluestorage();

        $active_date = $storage->findByAttributes(array(
            'play_id' => $this->playid,
            'key' => 'twoway_matches',
        ));

        $requsted_match_accepter = $storage->findByAttributes(array(
            'key' => 'requested_match',
            'value' => $this->playid,
        ));

        $requsted_match_requester = $storage->findByAttributes(array(
            'key' => 'requested_match',
            'play_id' => $this->playid,
        ));

        if ( $active_date ) {
            $remote_play_id = $active_date->value;
        } else if ( $requsted_match_accepter ) {
            $remote_play_id = $requsted_match_accepter->play_id;
        } else if ( $requsted_match_requester ) {
            $remote_play_id = $requsted_match_requester->value;
        }

        // if ( $type == 'profile' AND empty($remote_play_id) ) {
        if ( $type == 'profile' ) {
            $remote_play_id = $this->getVariable( 'last_opended_profile_id' );
        }

        if ( empty($remote_play_id) ) {
            return array();
        }

        $data = $this->getPlayVariables( $remote_play_id );

        $data['user_play_id'] = $remote_play_id;

        return $data;
    }

    public function removeMatch() {
        
        if ( !isset($this->menuid) ) {
            return false;
        }

        $pieces = explode('|', $this->menuid);

        if ( !isset($pieces[1]) OR !isset($pieces[2]) ) {
            return false;
        }

        $task_id = $pieces[1]; // 'all' or a Play ID
        $status = $pieces[2];

        if ( $status !== 'decline' ) {
            return false;
        }

        if ( $task_id == 'all' ) {

            $this->notifyNotConfirmedAccepters();
            
            // Delete all users, who "requested a match" for this person's play ( planner )
            $this->playkeyvaluestorage->del( 'requested_match' );

        } else {

            $storage = new AeplayKeyvaluestorage();

            // Remove the previous applications of this user
            $storage->deleteAllByAttributes(array(
                'play_id' => $this->playid,
                'value' => $task_id,
                'key' => 'requested_match'
            ));

            // Push - user removed
            $this->sendPlanPush( $task_id, 'You have not been confirmed', 'Why not make your plans instead?' );
        }

    }

    /*
    * When the user accepts a certain match, we mark the Date as ended
    */
    public function acceptMatch() {
        
        if ( !isset($this->menuid) ) {
            return;
        }

        $pieces = explode('|', $this->menuid);

        if ( !isset($pieces[1]) OR !isset($pieces[2]) ) {
            return;
        }

        $remote_user_play_id = $pieces[1];
        $status = $pieces[2];

        if ( $status != 'accept' ) {
            return;
        }

        // Logic for the current user
        $this->resetMatchAlways();

        $this->notifyNotConfirmedAccepters( $remote_user_play_id );

        // Remote all previous "match requests" for this user
        $this->playkeyvaluestorage->del( 'requested_match' );

        $this->playkeyvaluestorage->set( 'twoway_matches', $remote_user_play_id, $this->playid );

        // ------

        // Logic for the remotely matched user
        $this->playkeyvaluestorage->set( 'twoway_matches', $this->playid, $remote_user_play_id );
        $name = $this->getFirstName( $this->varcontent['name'] );

        // Update the date information for the accepter as well
        $active_request_id = $this->getVariable( $this->date_id_var );
        if ( $active_request_id ) {
            $this->saveRemoteVariable( $this->date_id_var, $active_request_id, $remote_user_play_id );
        }

        // Add acceptor as "confirmed"
        $this->addConfirmedAccepterDBRequest( $remote_user_play_id );

        // Push - Requester accepted Acceptor
        $this->sendPlanPush( $remote_user_play_id, $name . ' has confirmed!', 'Check in to arrange your plans', 1 );
    }

    public function acceptMatchAcceptor() {
        
        if ( !isset($this->menuid) ) {
            return;
        }

        $pieces = explode('|', $this->menuid);

        if ( !isset($pieces[1]) OR !isset($pieces[2]) ) {
            return;
        }

        $remote_user_play_id = $pieces[1];
        $status = $pieces[2];

        if ( $status != 'accept' ) {
            return;
        }

        $this->connectUsers( $remote_user_play_id );
    }

    public function connectUsers( $remote_user_play_id ) {

        // Add user to db
        $active_request_id = $this->addAccepterDBRequest( $remote_user_play_id );

        $this->playkeyvaluestorage->set( 'requested_match', $this->playid, $remote_user_play_id );

        if ( empty($active_request_id) ) {
            return false;
        }

        $requester_user_data = $this->getPlayVariables( $remote_user_play_id );

        $params = json_encode(array(
            'playid' => $this->playid,
            'app_id' => $this->gid,
            'request_id' => $active_request_id,
            'date_created_at' => $requester_user_data['timer'], // The time, when the date request was created
            'time_to_date' => $this->default_time, // How long a date request would last
        ));

        // Async task - this would trigger a notification when the date request ends
        Aetask::registerTask($this->playid, 'mobiledates:said_yes', $params, 'async', 90000);
    }

    /*
    * Works almost like checkAcceptorsMatch, however, it would only check if the currently previewed
    * Accepter has already confirmed somebody else's plan.
    */
    public function checkActivePlans( $play_id ) {
        $storage = new AeplayKeyvaluestorage();

        $args = array(
            'play_id' => $play_id,
            'key' => 'twoway_matches',
        );

        $active_user = $storage->findByAttributes( $args );

        if ( empty($active_user) ) {
            return false;
        }

        return $active_user->id;
    }

    /*
    * Check if the Acceptor has an active Date
    * Removes the "requested_match" for other users
    * Returns the Play ID of the Active date's user
    */
    public function checkAcceptorsMatch() {

        // Not sure about this one
        if ( preg_match('~step-1|end-date~', $this->menuid) ) {
            return false;
        }

        $storage = new AeplayKeyvaluestorage();

        $args = array(
            'play_id' => $this->playid,
            'key' => 'twoway_matches',
        );

        $active_user = $storage->findByAttributes( $args );

        if ( empty($active_user) ) {
            return false;
        }

        // Remove the previous applications of this user
        $storage->deleteAllByAttributes(array(
            'value' => $this->playid,
            'key' => 'requested_match'
        ));

        $requester_vars = $this->getPlayVariables( $active_user->value );

        if(isset($requester_vars['date_phase'])){
            $requester_phase = $requester_vars['date_phase'];
        }

        $active_request_id = $this->getActiveRequest();
        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( !empty($request) AND isset($request->requester_action) AND $request->requester_action ) {
            $status = 'activity-ended-completely';
        } else if ( isset($requester_phase) AND $requester_phase == 'step-end-date' ) {
            $status = 'activity-ended';
        } else {
            $status = 'date-active';
        }

        return array(
            'id' => $active_user->value,
            'status' => $status,
        );
    }

    /*
    * Check the state of a date for Requestor
    */
    public function checkRequestorsMatch() {

        if ( preg_match('~step-1|end-date~', $this->menuid) ) {
            return false;
        }

        $storage = new AeplayKeyvaluestorage();

        $args = array(
            'play_id' => $this->playid,
            'key' => 'twoway_matches',
        );

        $active_user = $storage->findByAttributes( $args );

        if ( empty($active_user) ) {
            return false;
        }

        $accepter_vars = $this->getPlayVariables( $active_user->value );
        $accepter_phase = $accepter_vars['date_phase'];

        $active_request_id = $this->getActiveRequest();
        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( !empty($request) AND $request->accepter_action ) {
            $status = 'activity-ended-completely';
        } else if ( $accepter_phase == 'step-3' ) {
            $status = 'activity-ended';
        } else {
            $status = 'date-active';
        }

        return array(
            'id' => $active_user->value,
            'status' => $status,
        );
    }

    public function verifyRequest() {
        $user_data = $this->getRemoteUserPlayData( 'profile' );

        if ( !isset($user_data['timer']) OR empty($user_data['timer']) ) {
            return false;
        }

        $timer_started = $user_data['timer'];
        $matched = $this->playkeyvaluestorage->valueExists( 'requested_match', $this->playid, $user_data['user_play_id'] );
        $seconds_left = $this->default_time - ( $this->current_time - $timer_started );

        if ( $seconds_left > 1 OR $matched ) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyMatch() {
        $this->loadVariables();

        $this->time_left_for_action = $this->default_time_for_action;

        $timer_started = $this->getVariable( 'timer_action' );

        // Failsafe, we shouldn't have this case
        if ( !$timer_started ) {
            return false;
        }

        $diff = $this->current_time - $timer_started;
        $this->time_left_for_action = $this->default_time_for_action - $diff;

        if ( $this->time_left_for_action < 0 ) {
            
            // Delete all users, who "requested a match" for this person's play
            $this->playkeyvaluestorage->del( 'requested_match' );

            return false;
        }

        return true;
    }

    public function sendAdminNotifications() {
        $mail = new YiiMailMessage;

        $send_to = $this->configobj->feedback_email;

        $notes_var = 'feedback-notes';

        $body = ( isset($this->submitvariables[$notes_var]) ? $this->submitvariables[$notes_var] : $this->configobj->feedback_body );

        $mail->setBody($body, 'text/html');
        $mail->addTo( $send_to );
        $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = $this->configobj->feedback_subject;

        Yii::app()->mail->send($mail);
    }

    public function getMenuParams() {
        
        if ( empty($this->menuid) ) {
            return false;
        }

        $pieces = explode('|', $this->menuid);

        if ( !isset($pieces[1]) OR !isset($pieces[2]) ) {
            return false;
        }

        return array(
            'action' => $pieces[1],
            'case' => $pieces[2],
        );
    }

    public function clearDateResults( $action = false , $case = false ) {

        if ( empty($action) AND empty($case) ) {
            if ( $params = $this->getMenuParams() ) {
                $action = $params['action'];
                $case = $params['case'];
            }
        }

        if ( $action != 'end-date' ) {
            return;
        }

        $this->clearTimers();

        $user_requests = $this->playkeyvaluestorage->get( 'requested_match' );

        $this->playkeyvaluestorage->del( 'twoway_matches' );

        // Delete all users, who "requested a match" for this person's play ( planner )
        $this->playkeyvaluestorage->del( 'requested_match' );
        $this->playkeyvaluestorage->del( 'matches' );
        $this->playkeyvaluestorage->del( 'notifications-msg' );
        $this->playkeyvaluestorage->del( 'two-way-matches' );

        // Remove the previous applications of this user
        $storage = new AeplayKeyvaluestorage();
        
        $storage->deleteAllByAttributes(array(
            'value' => $this->playid,
            'key' => 'requested_match'
        ));

        $this->deleteVariable( $this->tip_var );
        $this->deleteVariable( $this->tip_var_with_fee );

        // Special cases
        if ( !empty($case) ) {

            // Update the "State" of the plan
            // This would set the active plan to either "Pending" or "Completed"
            if ( $case != 'cancel-date' ) {
                $this->updateStatusDBRequest();
            }

            switch ($case) {
                case 'cancel-date':

                    $this->resetMatchAlways();

                    if ( $user_requests ) {
                        foreach ($user_requests as $requester_play_id) {
                            $chat_room_id = $this->getTwoWayChatId( $requester_play_id );
                            $this->deleteChatRoom( $chat_room_id );
                        }
                    }

                    $this->addToDebugLocal( 'Plan canceled manually ' . $this->varcontent['name'] . '-' . $this->playid, 'Time: ' . date( 'Y-m-d H:i:s' ), 940, 'dates-main' );

                    Aetask::deleteTask( $this->playid, 'mobiledates:create_plan', 'async' );

                    // Cancel the Date in the Database
                    $this->cancelDBRequest( false, 'User canceled' );
                    break;

                case 'time-expired':

                    $this->resetMatchAlways();

                    $this->addToDebugLocal( 'Time for action expired ' . $this->varcontent['name'] . '-' . $this->playid, 'Time: ' . date( 'Y-m-d H:i:s' ), 940, 'dates-main' );

                    Aetask::deleteTask( $this->playid, 'mobiledates:create_plan', 'async' );

                    // Cancel the Date in the Database
                    $this->cancelDBRequest( false, 'Time expired' );
                    break;
                
                case 'charge': // Submit - Requester
                    $this->chargeRequester();
                    $this->updateActionDBRequest( 'Submit and Pay' );
                    break;

                case 'end-plan': // Submit - Accepter
                    $this->updateActionDBRequest( 'Submit and End Plan' );
                    break;

                case 'no-show': // End Date - Both
                    $this->updateActionDBRequest( 'No Show' );
                    break;

                case 'cancel-plans': // End Date - Both
                    $this->updateActionDBRequest( 'Cancel Plans' );
                    break;
            }

        }

        return true;
    }

    public function doPayment() {
        $onload = new StdClass();
        $onload->action = 'inapp-purchase';
        $onload->id = 'MatchSwappUnlimited';
        $onload->product_id_ios = 'MatchSwappUnlimited';
        $onload->product_id_android = 'matchswappdisableadvertising';
        $onload->producttype_android = 'inapp';
        $onload->producttype_ios = 'inapp';
        $this->data->onload[] = $onload;
    }

    public function getFirstName( $name ){
        
        if (!strstr($name, ' ')) {
            return $name;
        }

        $firstname = explode(' ', trim($name));
        $firstname = $firstname[0];

        return $firstname;
    }

    public function setDefaultUserPreferences() {

        $variables = array(
            'slider_value_distance' => '40',
            'slider_value_age' => '32',
        );

        foreach ($variables as $var => $value) {
            if ( !$this->getSavedVariable( $var ) ) {
                $this->saveVariable( $var, $value );
            }
        }

        return true;
    }
    
    public function matchPreferences( $current_user_data, $remote_user_data, $name = false ) {

        $error = false;

        // Check preferences
        $required = array(
            'look_for_women', 'look_for_men'
        );

        foreach ($required as $pref) {
            if ( !isset($current_user_data[$pref]) OR !isset($remote_user_data[$pref]) ) {
                $error = true;
            }
        }

        if ( $error ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Preferences filter failed', 1036, 'dates-main' );
            }
            return false; // Still disable the matching in this case
        }

        if(!isset($current_user_data['gender']) OR !isset($current_user_data['look_for_men']) OR !isset($current_user_data['look_for_women'])
                OR !isset($remote_user_data['gender']) OR !isset($remote_user_data['look_for_men']) OR !isset($remote_user_data['look_for_women'])
        ){
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Preferences filter failed', 1043, 'dates-main' );
            }
            return false;
        }

        $gender_cu = $current_user_data['gender'];
        $cu_pref_men = $current_user_data['look_for_men'];
        $cu_pref_women = $current_user_data['look_for_women'];

        $gender_ru = $remote_user_data['gender'];
        $ru_pref_men = $remote_user_data['look_for_men'];
        $ru_pref_women = $remote_user_data['look_for_women'];

        $cases = array(
            'man-man' => array(
                '1|0|1|0',
                '1|1|1|0',
                '1|0|1|1',
                '1|1|1|1',
            ),
            'man-woman' => array(
                '0|1|1|0',
                '1|1|1|0',
                '0|1|1|1',
                '1|1|1|1',
            ),
            'woman-man' => array(
                '1|0|0|1',
                '1|1|0|1',
                '1|0|1|1',
                '1|1|1|1',
            ),
            'woman-woman' => array(
                '0|1|0|1',
                '0|1|1|1',
                '1|1|0|1',
                '1|1|1|1',
            ),
        );

        foreach ($cases as $scenario => $combinations) {

            $genders = explode('-', $scenario);
                
            if ( ($genders[0] == $gender_cu) AND ($genders[1] == $gender_ru) ) {

                foreach ($combinations as $combination) {
                    $matrix = explode('|', $combination);
                    if (
                        $matrix[0] == $cu_pref_men AND
                        $matrix[1] == $cu_pref_women AND
                        $matrix[2] == $ru_pref_men AND
                        $matrix[3] == $ru_pref_women
                    ) {
                        if ( $name ) {
                            $this->addToDebugLocal( $name, 'Preferences Filter passed', 1096, 'dates-main' );
                        }
                        return true;
                    }
                }

            }

        }

        $this->addToDebugLocal( $name, 'Preferences filter failed - sex mismatch', 1109, 'dates-main' );

        return false;
    }

    public function ageFilterPassed( $current_user_data, $remote_user_data, $name = false ) {

        $age_pref_planner = ( isset($current_user_data['slider_value_age']) ? $current_user_data['slider_value_age'] : false );
        $age_pref_accepter = ( isset($remote_user_data['slider_value_age']) ? $remote_user_data['slider_value_age'] : false );
        $cu_age = ( isset($current_user_data['age']) ? $current_user_data['age'] : false );
        $ru_age = ( isset($remote_user_data['age']) ? $remote_user_data['age'] : false );

        if ( !$age_pref_planner OR !$age_pref_accepter OR !$cu_age OR !$ru_age ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Age filter passed - but missing settings for this user', 1145, 'dates-main' );
            }
            return true; // No Age preferences
        }

        $options_ac = array(
            'options' => array(
                'min_range' => 18,
                'max_range' => $age_pref_planner
            )
        );

        $options_pl = array(
            'options' => array(
                'min_range' => 18,
                'max_range' => $age_pref_accepter
            )
        );

        $check1 = ( filter_var( $cu_age, FILTER_VALIDATE_INT, $options_ac ) ? true : false ); 
        $check2 = ( filter_var( $ru_age, FILTER_VALIDATE_INT, $options_pl ) ? true : false ); 

        if ( $check1 AND $check2 ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Age filter passed', 1169, 'dates-main' );
            }
            return true;
        }

        if ( $name ) {
            $this->addToDebugLocal( $name, 'Age filter failed', 1152, 'dates-main' );
        }

        return false;
    }

    public function locationFilterPassed( $current_user_data, $remote_user_data, $name = false ) {

        $location_pref = ( isset($current_user_data['slider_value_distance']) ? $current_user_data['slider_value_distance'] : false );

        if ( !$location_pref ) {
            return true; // No Location preferences
        }

        $user_lat = $current_user_data['lat'];
        $user_lng = $current_user_data['lon'];
        $r_user_lat = $remote_user_data['lat'];
        $r_user_lng = $remote_user_data['lon'];

        $distance = Helper::getDistance( $user_lat, $user_lng, $r_user_lat, $r_user_lng, 'K' );

        if ( $location_pref > $distance ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Location filter passed', 1168, 'dates-main' );
            }
            return true;
        }

        if ( $name ) {
            $this->addToDebugLocal( $name, 'Location filter failed', 1174, 'dates-main' );
        }

        return false;
    }

    public function bonusFilterPassed( $current_user_data, $remote_user_data, $name = false ) {

        // Always true
        return true;
        
        // Always pass this filter, if the user isn't interested in such superficial stuff like money :)   
        if ( !isset($current_user_data['show_cash_only']) OR empty($current_user_data['show_cash_only']) ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Bonus filter passed, but missing show_cash_only', 1216, 'dates-main' );
            }
            return true;
        }

        if ( isset($remote_user_data[$this->tip_var]) AND !empty($remote_user_data[$this->tip_var]) ) {
            if ( $name ) {
                $this->addToDebugLocal( $name, 'Bonus filter passed', 1198, 'dates-main' );
            }
            return true;
        }

        if ( $name ) {
            $this->addToDebugLocal( $name, 'Bonus filter failed', 1204, 'dates-main' );
        }

        return false;
    }

    public function setLastOpenedStamp() {
        $current_time = time();
        $last_opened = $this->getVariable( 'action_last_opened' );

        if ( $current_time > ($last_opened + 600) ) {
            $this->saveVariable( 'action_last_opened', $current_time );
        }
    }

    public function updateLocation($interval) {
        $this->refreshLocation();

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;
    }

    public function refreshLocation() {

        if ( $this->menuid == 'ask-location' ) {
            return false;
        }

        $current_time = time();
        $last_updated = $this->getVariable( 'location_last_updated' );
        $last_updated_matching = $this->getVariable( 'matching_location_last_updated' );
        $last_userid = $this->getVariable( 'last_known_userid' );

        if (
            ($last_userid != $this->userid) OR
            ($current_time > ($last_updated + 600))
        ) {
            $this->saveVariable( 'location_last_updated', $current_time );
            $this->saveVariable( 'last_known_userid', $this->userid );

            $onload = new StdClass();
            $onload->id = 'ask-location';
            $onload->action = 'ask-location';
            $this->data->onload[] = $onload;
        }

        if (
            ($last_userid != $this->userid) OR
            ($current_time > ($last_updated_matching + 610))
        ) {
            $this->saveVariable( 'matching_location_last_updated', $current_time );

            // Update the matching table as well
            $lat = $this->getVariable( 'lat' );
            $lon = $this->getVariable( 'lon' );

            $matchingModel = new MobilematchingModel();
            $matching_entry = $matchingModel->findByAttributes(array('play_id' => $this->playid));
            if ( is_object($matching_entry) ) {
                $matching_entry->lat = $lat;
                $matching_entry->lon = $lon;
                $matching_entry->update();
            }
        }

    }

    public function updateScreen() {
        $this->rewriteActionConfigField('poll_interval', 5);
        $this->rewriteActionField('poll_interval', 5);
        $this->rewriteActionField('poll_update_view', 'all');
    }

    public function stopScreenUpdate() {
        $this->rewriteActionConfigField('poll_interval', 0);
        $this->rewriteActionField('poll_interval', 0);
        $this->rewriteActionField('poll_update_view', 'all');
    }

    /*
    * Allow new requests
    */
    public function clearTimers() {
        $this->deleteVariable( 'timer' );
        $this->deleteVariable( 'timer_action' );
        $this->deleteVariable( 'invitations_sent' );
    }

    public function validateFunds( $sbm_sum ) {

        $min = 250;
        $max = 12000;

        $wallet = $this->wallet->getWallet();
        $current_wallet_sum = $wallet->funds_raw;

        $result = array();

        if ( $sbm_sum < $min ) {
            $result['success'] = false;
            $result['code'] = 1;
        } else if ( $sbm_sum > $max ) {
            $result['success'] = false;
            $result['code'] = 2;
        } else if ( $sbm_sum > $current_wallet_sum ) {
            $result['success'] = false;
            $result['code'] = 3;
        }

        if ( empty($result) ) {
            $result['success'] = true;

            $amount = 20; // percents
            $sum_with_fee = $sbm_sum * ((100-$amount) / 100);

            // Update the Tip amount, so the Acceptor see the submitted sum - 20%
            $this->saveVariable( $this->tip_var_with_fee, $sum_with_fee );
        }

        return $result;
    }

    /*
    * Checks whether the user initiated a Chat during the current Request session
    * Stores the result into the database
    */
    public function checkChatMessages( $play_id ) {

        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $request_stamp = $request->timestamp;

        $chat_room_id = $this->getTwoWayChatId( $play_id );

        $chatUsersModel = new Aechatusers();

        $chat_room = $chatUsersModel->findByAttributes( array( 'context_key' => $chat_room_id ) );

        if ( empty($chat_room) ) {
            return false;
        }
        
        $active_chat_id = $chat_room->chat_id;

        $chatModel = new Aechat();
        $chatModel->current_chat_id = $active_chat_id;

        $messages = $chatModel->getMessages( $request_stamp );

        // Delete the chat room
        $this->deleteChatRoom( $chat_room_id );

        if ( empty($messages) ) {
            return false;
        }

        // Update the DB
        $request->chat_initiated = 1;
        $request->update();
    }

    /*
    * Send a Push within the context of the current Plan
    */
    public function sendPlanPush( $play_id, $text, $description, $badge_count = 0, $active_request_id = null ) {

        if ( empty($active_request_id) ) {
            $active_request_id = $this->getActiveRequest();
        }

        // If DB date was canceled by a previously executed action
        if ( empty($active_request_id) ) {
            $this->addToDebugLocal( 'Missing active request ID for: ' . $play_id, '----', 1315, 'dates-main' );
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            $this->addToDebugLocal( 'Failed to find the request in the DB: ' . $active_request_id, '--request_id--', 1322, 'dates-main' );
            return false;
        }

        $pushes = $request->sent_pushes;

        if ( empty($pushes) ) {
            $pushes = array();
        } else {
            $pushes = json_decode( $pushes, true );
        }

        $db_string = $play_id . $text . $description;
        $db_string = strtolower( str_replace(' ', '-', $db_string) );

        if ( !in_array($db_string, $pushes) ) {

            $notification_db_id = Aenotification::addUserNotification( $play_id, $text, $description, $badge_count, $this->gid );

            $pushes[] = $db_string;
            $request->sent_pushes = json_encode( $pushes );
            $request->update();

            $this->addToDebugLocal( 'Plan push sent to: ' . $play_id . ' dbID: ' . $notification_db_id, $text . ' ' . $description, 1343, 'dates-main' );
        } else {
            $this->addToDebugLocal( 'Notification already sent to: ' . $play_id, $text . ' ' . $description, 1345, 'dates-main' );
        }
    }

    /*
    * This method would automatically verify if the user is still valid
    * Acts like a second stage verification on the Accepter's side
    * If 'false', the status of both users would automatically reset to the default state
    */
    public function checkUserValidity( $user_data = false ) {

        if ( empty($user_data) ) {
            $user_data = $this->getRemoteUserPlayData( 'profile' );
        }

        if ( !isset($user_data[$this->date_id_var]) OR empty($user_data[$this->date_id_var]) ) {
            return false;
        }

        $active_date_id = $user_data[$this->date_id_var];

        $request = $this->requestsobj->findByPk( $active_date_id );

        if ( empty($request) ) {
            return false;
        }

        // Plan is still active
        if ( $request->confirmed_respondent ) {
            return true;
        }

        $stamp = strtotime( $request->timestamp );
        $current_time = time();

        $diff = $current_time - $stamp;
        $time_left = ($this->default_time + $this->default_time_for_action) - $diff;

        if ( $time_left <= 0 ) {
            // This is probably needed .. but it also messes up the request statuses
            // $this->resetUserStatus( $request );
            return false;
        } else {
            return true;
        }

    }

    public function resetUserStatus( $request ) {

        $storage = new AeplayKeyvaluestorage();

        // Remove the previous applications of this user
        $storage->deleteAllByAttributes(array(
            'play_id' => $request->requester_playid,
            'value' => $this->playid,
            'key' => 'requested_match'
        ));

        $request->status = 'Canceled';
        $request->update();
    }

    public function notifyNotConfirmedAccepters( $skip_play_id = false ) {
        $storage = new AeplayKeyvaluestorage();
        
        $all_current_accepters = $storage->findAllByAttributes(array(
            'play_id' => $this->playid,
            'key' => 'requested_match'
        ));

        if ( empty($all_current_accepters) ) {
            return false;
        }

        foreach ($all_current_accepters as $accepter) {
            if ( $skip_play_id != $accepter->value ) {
                $this->sendPlanPush( $accepter->value, 'You have not been confirmed', 'Why not make your plans instead?' );
            }
        }
    }

    public function resetMatchAlways() {
        $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $this->playid ) );

        if ( empty($user) ) {
            return false;
        }

        $user->match_always = 0; // Prevent showing up in the listing
        $user->update();
    }

    public function updatePreferences() {
        $this->saveVariable('date_preferences', 'acceptor');
        $this->saveVariable('date_phase', 'step-1');
    }

    public function fixGender() {
        $gender = $this->getVariable( 'gender' );
        if ( $gender == 'male' ) {
            $this->saveVariable('gender', 'man');
        }
    }

    /*
    * This would automatically "charge" the plan requester
    * This means that:
    * a) the variable of his/her current funds would be decremented
    * b) the data would be stored in the database
    */
    public function chargeRequester() {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );
        
        if ( empty($request) ) {
            return false;
        }

        $accepter_play_id = $request->confirmed_respondent;

        if (
            !isset($this->varcontent[$this->tip_var]) OR
            !isset($this->varcontent[$this->tip_var_with_fee]) OR
            empty($this->varcontent[$this->tip_var]) OR
            empty($this->varcontent[$this->tip_var_with_fee])
        ) {
            return false;
        }

        $tip = $this->varcontent[$this->tip_var];
        $tip_with_fee = $this->varcontent[$this->tip_var_with_fee];

        // Calculate the new sums -> this is where the magic happens
        $wallet_requester = $this->wallet->getWallet();
        $current_requester_sum = $wallet_requester->funds_raw;
        $new_requester_sum = $current_requester_sum - $tip;

        $wallet_accepter = $this->wallet->getWallet( $accepter_play_id );
        $current_accepter_sum = $wallet_accepter->funds_raw;
        $new_accepter_sum = $current_accepter_sum + $tip_with_fee;

        // Add the tracking record
        $this->wallet_track->request_id = $active_request_id;
        $this->wallet_track->sum_requester_before = $current_requester_sum;
        $this->wallet_track->sum_requester_after = $new_requester_sum;
        $this->wallet_track->sum_accepter_before = $current_accepter_sum;
        $this->wallet_track->sum_accepter_after = $new_accepter_sum;
        $this->wallet_track->sum_admin = $tip - $tip_with_fee;
        $this->wallet_track->insert();

        // Update the users wallets
        $this->wallet->updateWallet( $new_requester_sum );
        $this->wallet->updateWallet( $new_accepter_sum, $accepter_play_id );

        return true;
    }

    /*
    * Retrieves the user's active DB request
    * The function would work for both requesters and accepters
    */
    public function getActiveRequest() {

        $request_id = $this->getVariable( $this->date_id_var );

        if ( $request_id ) {
            return $request_id;
        }

        return false;
    }

    /*
    * Create a new DB Request
    */
    public function createDBRequest() {
        $this->requestsobj->id = null;
        $this->requestsobj->requester_playid = $this->playid;
        $this->requestsobj->activity = $this->varcontent['activity_idea'];
        $this->requestsobj->location = $this->varcontent['activity_address'];
        $this->requestsobj->tip = ( isset($this->varcontent[$this->tip_var]) ? $this->varcontent[$this->tip_var] : '' );
        $this->requestsobj->status = 'Active';
        $this->requestsobj->timestamp = date( 'Y-m-d H:i:s' );
        $this->requestsobj->lat = $this->varcontent['lat'];
        $this->requestsobj->lon = $this->varcontent['lon'];
        $this->requestsobj->row_last_updated = time();
        
        if ( $this->requestsobj->insert() ) {
            $insert_id = $this->requestsobj->id;

            if ( empty($insert_id) ) {
                $insert_id = $this->requestsobj->getRequest( $this->playid );
            }

            $this->saveVariable( $this->date_id_var, $insert_id );
        }
    }

    public function cancelDBRequest( $change_pref = false, $status = 'Timed Out' ) {

        $active_request_id = $this->getActiveRequest();

        // If DB date was canceled by a previously executed action
        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) OR !is_object($request) ) {
            return false;
        }

        $request->status = $status;
        $request->row_last_updated = time();
        $request->update();

        if ( isset($request->respondents) AND $request->respondents ) {
            $respondents = json_decode( $request->respondents );

            if(!empty($respondents)){
                foreach ($respondents as $respondent_play_id => $stamp) {
                    $this->saveRemoteVariable( 'date_phase', 'step-1', $respondent_play_id );
                }
            }
        }

        // Remove the Date ID
        $this->deleteVariable( $this->date_id_var );

        // Make sure that users would always default to Accepters
        if ( $change_pref ) {
            $this->updatePreferences();
        }
    }

    /*
    * Adds an accepter to the correct DB
    * Would also return the active request ID
    */
    public function addAccepterDBRequest( $remote_user_id ) {

        $remote_user_data = $this->getPlayVariables( $remote_user_id );
        
        if ( empty($remote_user_data) ) {
            return false;
        }

        $active_request_id = ( isset($remote_user_data[$this->date_id_var]) ? $remote_user_data[$this->date_id_var] : false );

        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $respondents = $request->respondents;

        if ( empty($respondents) ) {
            $respondents = array();
        } else {
            $respondents = json_decode( $respondents, true );
        }

        $respondents[$this->playid] = time();

        $request->respondents = json_encode( $respondents );
        $request->row_last_updated = time();
        $request->update();

        return $active_request_id;
    }

    public function addConfirmedAccepterDBRequest( $remote_user_play_id ) {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $request->confirmed_respondent = $remote_user_play_id;
        $request->row_last_updated = time();
        $request->update();
    }

    public function updateActionDBRequest( $status ) {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $user_type = $this->varcontent['date_preferences'];
        $column = ( $user_type == 'acceptor' ? 'accepter_action' : 'requester_action' );

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $request->{$column} = $status . '|' . time();
        $request->row_last_updated = time();
        $request->update();

        // Make sure that users would always default to Accepters
        $this->updatePreferences();
    }

    public function updateRatingDBRequest( $rating ) {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $user_type = $this->varcontent['date_preferences'];
        $column = ( $user_type == 'acceptor' ? 'accepter_rating' : 'requester_rating' );

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $request->{$column} = $rating;
        $request->row_last_updated = time();
        $request->update();
    }

    public function getRequestRatings() {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }
        
        $user_type = $this->varcontent['date_preferences'];
        $column = ( $user_type == 'acceptor' ? 'accepter_rating' : 'requester_rating' );

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }
        
        return $request->{$column};
    }

    public function updateStatusDBRequest() {
        $active_request_id = $this->getActiveRequest();

        if ( empty($active_request_id) ) {
            return false;
        }

        $request = $this->requestsobj->findByPk( $active_request_id );

        if ( empty($request) ) {
            return false;
        }

        $current_status = $request->status;
        $request->status = ( $current_status == 'Active' ? 'Pending' : 'Completed' );
        $request->row_last_updated = time();
        $request->update();   
    }

    // Re-enable the chat and delete all Chat related flags
    public function clearChatFlags() {
        $this->playkeyvaluestorage->del( 'chat-room-' . $this->playid );
        $this->playkeyvaluestorage->del( 'chat-flag' );
    }

    public function migrateUsers() {

        /*
        $users = $this->requestsobj->getAllUsers( $this->gid );
        if ( $users ) {
            foreach ($users as $user) {
                $variables = $this->getPlayVariables( $user['play_id'] );
                if ( !isset($variables['to_requestor_stamp']) ) {
                    $this->saveRemoteVariable( 'to_requestor_stamp', time(), $user['play_id'] );
                }
            }
        }
        */

        $is_migrated = $this->getVariable( 'user_migrated' );
        if ( !$is_migrated ) {
            $this->saveVariable( 'date_phase', 'step-1' );
            $this->saveVariable( 'date_preferences', 'acceptor' );
            $this->saveVariable( 'user_migrated', 1 );
        }

    }

    public function addToDebugLocal($user, $msg, $line, $file){
        $this->debug_msgs[] = $user . '; ' . $msg . '; ' . $line . '; ' . $file;
    }

    public function compareDeepValue($val1, $val2) {
        return strcmp($val1['id'], $val2['id']);
    }

    public function facebookFriends( $vars ){

        if(!isset($vars['fb_token']) OR !$this->getSavedVariable('fb_token')){
            return false;
        }

        if(!$vars['fb_token']){
            return false;
        }

        /* friends of the other user */
        $friends = ThirdpartyServices::getUserFbFriends( $vars['fb_token'], $this->appinfo->fb_api_id, $this->appinfo->fb_api_secret );

        /* current users's friends */
        $my_friends = ThirdpartyServices::getUserFbFriends( $this->getSavedVariable('fb_token'), $this->appinfo->fb_api_id, $this->appinfo->fb_api_secret );
        // $my_friends = ThirdpartyServices::getUserFbFriends($vars['fb_token'],$this->appinfo->fb_api_id,$this->appinfo->fb_api_secret);

        if ( empty($friends) OR empty($my_friends) ) {
            return false;
        }

        $matched_friends = array_uintersect(
            $friends,
            $my_friends,
            array( $this, 'compareDeepValue' )
        );

        if ( empty($matched_friends) ) {
            return false;
        }

        $items = array();
        $count = count($friends);
        $usercount = 1;
        $round = 1;

        $items_per_col = round($this->screen_width / (95 + 20));

        foreach ($matched_friends as $friend){
            $id = $friend['id'];

            $filename = 'fb_' . $id . '.jpg';
            if (isset($friend['picture']['data']['url'])) {
                $image = Controller::copyThirdPartyImage($this->gid, $filename, $friend['picture']['data']['url']);
            } else {
                $image = 'anonymous2.png';
            }

            if (isset($friend['name'])) {
                $name = $friend['name'];
            } else {
                $name = '{#anonymous#}';
            }

            $onclick = new stdClass();
            $onclick->action = 'open-url';
            $onclick->action_config = 'https://facebook.com/' . $friend['id'];

            $col[] = $this->getImage($image, array('imgwidth' => '150', 'imgheight' => '150', 'crop' => 'round', 'width' => '80', 'priority' => 9, 'onclick' => $onclick));
            $col[] = $this->getText($name, array('font-size' => '11', 'text-align' => 'center', 'width' => '80', 'color' => '#ffffff', 'margin' => '3 0 0 0'));
            $items[] = $this->getColumn($col, array('margin' => '10 10 10 10', 'width' => '95', 'text-align' => 'center'));
            $temp = $this->getColumn($col, array('margin' => '10 10 10 10', 'width' => '95', 'text-align' => 'center'));
            unset($col);

            if ($usercount == $items_per_col) {
                $page[] = $this->getRow($items, array('margin' => '0 0 0 0'));
                // $page[] = $this->getSwipeNavi(ceil($count / 3), $round, array('navicolor' => 'white'));
                $swipe[] = $this->getColumn($page);
                $round++;
                unset($items);
                unset($page);
                $usercount = 1;
                // showing the last item again on the next page
                $items[] = $temp;
            }

            $usercount++;
        }

        if(isset($items) AND !is_float($count/3)){
            $page[] = $this->getRow($items,array('margin' => '0 0 0 15'));
            // $page[] = $this->getSwipeNavi(ceil($count/3),$round,array('navicolor' => 'white'));
            $swipe[] = $this->getColumn($page,array('width' => '100%'));
        }

        $this->data->scroll[] = $this->getText('{#common_facebook_friends#} (' . count($matched_friends) . ')', array( 'style' => 'total-ratings-heading' ));

        if($count > 3 AND isset($swipe)){
            $this->data->scroll[] = $this->getSwipearea($swipe,array('width' => '100%'));
        } elseif( !empty($items) ) {
            $this->data->scroll[] = $this->getRow($items,array('text-align' => 'center'));
        }
    }

    /*
    * Check the Requests Database for updates.
    * This method would essentially check if a certain row is updated
    * If yes, it would release a lock
    */
    public function checkRequestsDBStatus() {
        $refresh = false;
        $this->rewriteActionField('poll_update_view', 'header');

        $active_date_id = $this->getVariable( $this->date_id_var );

        if ( empty($active_date_id) ) {
            $last_opended_profile_id = $this->getVariable( 'last_opended_profile_id' );
            $remote_user_data = $this->getPlayVariables( $last_opended_profile_id );

            if ( isset($remote_user_data[$this->date_id_var]) ) {
                $active_date_id = $remote_user_data[$this->date_id_var];
            }
        }

        $request = $this->requestsobj->findByPk( $active_date_id );

        if ( empty($request) ) {
            $this->rewriteActionField('poll_update_view', 'all');
            return false;
        }

        $row_last_updated = $request->row_last_updated;
        $request_last_updated = $this->getVariable( 'request_last_updated' );

        if ( $request_last_updated != $row_last_updated ) {
            $this->saveVariable( 'request_last_updated', $row_last_updated );
            $this->rewriteActionField('poll_update_view', 'all');
            $refresh = true;
        }
    }

    public function getPopupClick( $id ) {
        
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = 'open-popup';
        $onclick->open_popup = true;
        $onclick->action_config = $id;
        $onclick->back_button = 1;

        return $onclick;
    }

    public function deleteChatRoom( $chat_room_id ) {
        Aechat::model()->deleteAllByAttributes(array(
            'context_key' => $chat_room_id
        ));

        return true;
    }

    public function cancelAcceptersPlans() {
        $this->saveVariable('date_preferences', 'requestor');
        $this->saveVariable('to_requestor_stamp', time());

        $storage = new AeplayKeyvaluestorage();
        
        $active_requests = $storage->findAllByAttributes(array(
            'value' => $this->playid,
            'key' => 'requested_match'
        ));

        // Failsafe
        if ( empty($active_requests) ) {
            return false;
        }

        foreach ($active_requests as $request) {
            $requester_playid = $request->play_id;
            $chat_room_id = $this->getTwoWayChatId( $requester_playid );
            $this->deleteChatRoom( $chat_room_id );

            // Update the requests table
            $remote_user_data = $this->getPlayVariables( $requester_playid );
            $active_date_id = $remote_user_data[$this->date_id_var];

            $request = $this->requestsobj->findByPk( $active_date_id );

            if ( empty($request) ) {
                continue;
            }

            $respondents = @json_decode( $request->respondents, true );
            unset($respondents[$this->playid]);
            $request->respondents = json_encode($respondents, JSON_FORCE_OBJECT);
            $request->row_last_updated = time();
            $request->update();
        }

        $storage->deleteAllByAttributes(array(
            'value' => $this->playid,
            'key' => 'requested_match'
        ));

        return true;
    }

    public function setChatMenus() {

        $total_messages = 0;
        $user_chats = Aechatusers::getUsersChatsByContext( $this->playid );

        if ( empty($user_chats) ) {
            return false;
        }

        foreach ($user_chats as $chat) {
            $chat_id = $chat['chat_id'];

            $sql = "SELECT * FROM ae_chat_messages
                    WHERE chat_id = :chat_id
                    AND chat_message_is_read = 0
                    AND author_play_id <> :this_playid
                    GROUP BY ae_chat_messages.id";

            $args = array(
                ':chat_id' => $chat_id,
                ':this_playid' => $this->playid,
            );

            $messages = Yii::app()->db
                ->createCommand($sql)
                ->bindValues( $args )
                ->queryAll();

            $total_messages += count( $messages );
        }

        if ( $total_messages == 0 ) {
            return false;
        }

        if ( $total_messages == 1 ) {
            $chat_menu_id = ( isset($this->menus['chat_1_message']) ? $this->menus['chat_1_message'] : 0 );
        } else if ( $total_messages == 2 ) {
            $chat_menu_id = ( isset($this->menus['chat_2_messages']) ? $this->menus['chat_2_messages'] : 0 );
        } else if ( $total_messages == 3 ) {
            $chat_menu_id = ( isset($this->menus['chat_3_messages']) ? $this->menus['chat_3_messages'] : 0 );
        } else if ( $total_messages == 4 ) {
            $chat_menu_id = ( isset($this->menus['chat_4_messages']) ? $this->menus['chat_4_messages'] : 0 );
        } else {
            $chat_menu_id = ( isset($this->menus['chat_more_messages']) ? $this->menus['chat_more_messages'] : 0 );
        }

        $this->rewriteActionConfigField('menu_id', $chat_menu_id);
    }

    public function performMigration() {

        $users = $this->requestsobj->getAllUsers( $this->gid );

        if ( empty($users) ) {
            return false;
        }

        $fields = array(
            'requested_match', 'matches', 'notifications-msg', 'two-way-matches', 'twoway_matches'
        );

        $storage = new AeplayKeyvaluestorage();

        foreach ($users as $user) {

            if ( !isset($user['play_id']) ) {
                continue;
            }

            $play_id = $user['play_id'];

            // $variables = $this->getPlayVariables( $play_id );

            $this->saveRemoteVariable( 'date_phase', 'step-1', $play_id );

            // Clear older entries
            foreach ($fields as $field) {
                // Remove the previous applications of this user
                $storage->deleteAllByAttributes(array(
                    'play_id' => $play_id,
                    'key' => $field
                ));                
            }

        }

        die( 'Migration done!' );
    }

}