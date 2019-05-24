<?php

class dittoMobiledatesSubController extends MobiledatesController {

    public $time_left;
    public $time_left_for_action;

    public $current_time;
    public $default_time = 86400;
    public $default_time_for_action = 86400;

    public $error = false;
    public $funds_error = false;
    public $funds_error_code;

    public $places_key = 'AIzaSyAA1XKkJQY4MbQfmqF1l1a4qQlj3JhW6NY';

    public function ditto(){        

        // Migrate older users
        // We should remove this in future
        $this->migrateUsers();

        $this->askPermissions();

        $this->loadVariableContent(true);

        $field_check_failed = $this->checkDBUserData();
        if ( $field_check_failed ) {
            $this->showDBUserDataView();
            return false;
        }

        // Auto-refresh
        $this->updateScreen();

        if ( $this->menuid == 'mute-notifications' ) {
            $this->saveVariable( 'notify', 0 );
        }

        if ( $this->menuid == 'unmute-notifications' ) {
            $this->saveVariable( 'notify', 1 );
        }

        if ( !isset($this->varcontent['lat']) ) {
            $this->getErrorView( 'Coordinates' );
            $this->updateLocation(false);
            return false;
        }

        if ( !isset($this->varcontent['age']) ) {
            $this->getErrorView( 'Age' );
            return false;
        }

        $this->current_time = time();

        $this->date_type = ( empty($this->date_type) ? 'requestor' : $this->date_type );
        $this->phase = ( empty($this->phase) ? 'step-1' : $this->phase );

        if ( $this->date_type == 'acceptor' ) {
            $this->resetMatchAlways();
        }

        $this->setChatMenus();

        switch ($this->date_type) {
            case 'acceptor':
                $this->runAcceptorLogic();
                break;
            
            case 'requestor':
                $this->runRequestorLogic();
                break;
        }

        return true;
    }

    public function runAcceptorLogic() {

        // Check if the Acceptor has an active Date
        $plan = $this->checkAcceptorsMatch();
        $active_date_play_id = $plan['id'];
        $plan_status = $plan['status'];

        if ( $plan_status == 'date-active' AND $this->phase != 'step-3' ) {
            $this->saveVariable( 'date_phase', 'step-date' );
            $this->viewDate( $active_date_play_id );
            return true;
        } else if ( $plan_status == 'activity-ended' OR $plan_status == 'activity-ended-completely' ) {
            $this->saveVariable( 'date_phase', 'step-3' );
            $this->endDate( $active_date_play_id );
            return true;
        }

        // If the Requester cancels a date or the date expires
        if ( $this->phase == 'step-2' OR $this->phase == 'step-accept' ) {
            if ( !$this->verifyRequest() ) {
                $this->clearTimers();
                $this->saveVariable( 'date_phase', 'step-1' );
                $this->accepterStep1();
                return true;
            }

            if ( !$this->checkUserValidity() ) {
                $this->saveVariable( 'date_phase', 'step-1' );
                $this->accepterStep1();
                return true;
            }
        }

        switch ($this->phase) {
            case ( preg_match('/step-1/', $this->phase) ? true : false ):
                $this->clearDateResults();
                $this->accepterStep1();
                break;

            case ( preg_match('/step-2/', $this->phase) ? true : false ):
                $this->viewProfileAccepter();
                break;

            case ( preg_match('/step-accept/', $this->phase) ? true : false ):
                $this->viewAcceptUser();
                break;
            case ( preg_match('/step-3/', $this->phase) ? true : false ):
                $this->endDate( $active_date_play_id );
                break;
        }

    }

    public function runRequestorLogic() {

        $plan = $this->checkRequestorsMatch();
        $plan_status = $plan['status'];

        if ( $plan_status == 'date-active' AND $this->phase != 'step-end-date' ) {
            $this->viewDate();
            return true;
        } else if ( $plan_status == 'activity-ended' OR $plan_status == 'activity-ended-completely' ) {
            $this->saveVariable( 'date_phase', 'step-end-date' );
            $this->endDate();
            return true;
        }

        switch ($this->phase) {
            case ( preg_match('/step-1/', $this->phase) ? true : false ):
                $this->clearDateResults();
                $this->requesterStep1();
                break;
            
            case 'step-2':
                $skip_step = $this->handleTimerRequester();

                if ( $skip_step ) {
                    $this->requesterStep1();
                } else {
                    // $this->removeMatch();

                    // Send push notifications for all qualified accepters
                    $this->sendInvigationsToAccepters();

                    $this->requesterStep2();
                }

                break;

            case ( preg_match('/step-3/', $this->phase) ? true : false ):

                $this->viewProfileRequester();

                break;

            case ( preg_match('/step-4/', $this->phase) ? true : false ):
                $this->acceptMatch();
                $this->viewDate();                
                break;

            case 'step-end-date':
                $this->endDate();
                break;
        }

    }

    public function requesterStep1() {

        $this->clearChatFlags();
        $this->refreshLocation();

        $this->deleteVariable( 'last_opended_profile_id' );
        $this->deleteVariable( $this->date_id_var );

        $fields = array(
            'activity_idea' => 'pick-date-field',
            'activity_address' => 'pick-date-field',
        );

        if ( isset($this->menuid) AND $this->menuid == 'create-date' ) {

            foreach ($this->submitvariables as $var_id => $var_value) {
                $var_name = array_search( $var_id, $this->vars );

                if ( isset($fields[$var_name]) AND empty($var_value) ) {
                    $fields[$var_name] = 'pick-date-field-error';
                    $this->error = true;
                } else {
                    $this->saveVariable( $var_id, $var_value );
                }
            }

            if ( !$this->error ) {
                $this->createDBRequest();
                $this->createMatchRequest();

                $this->saveVariable( 'date_phase', 'step-2' );
                $this->phase = 'step-2';

                $this->runRequestorLogic();
                return true;
            }

        }

        $this->data->scroll[] = $this->getText( '', array( 'style' => 'row-spacer' ) );

        $label_act = ( $this->getVariable( 'activity_idea' ) ? $this->getVariable( 'activity_idea' ) : ' ' );
        $this->data->scroll[] = $this->getText( 'What do you have in mind?', array( 'style' => 'pick-date-heading' ) );
        $this->data->scroll[] = $this->getText($label_act, array(
            'style' => $fields['activity_idea'],
            'variable' => 'activity_idea',
            'onclick' => $this->getPopupClick($this->getConfigParam( 'action_activity_popup' ))
        ));

        $label_loc = ( $this->getVariable( 'activity_address' ) ? $this->getVariable( 'activity_address' ) : ' ' );
        $this->data->scroll[] = $this->getText( 'Where do you plan to meet?', array( 'style' => 'pick-date-heading' ) );
        $this->data->scroll[] = $this->getFieldtext('', array(
            'variable' => 'activity_address_filter',
            'width' => 1,
            'height' => 1,
        ));
        $this->data->scroll[] = $this->getText($label_loc, array(
            'variable' => 'activity_address',
            'style' => $fields['activity_address'],
            'onclick' => $this->getPopupClick($this->getConfigParam( 'action_location_popup' ))
        ));

        $this->data->scroll[] = $this->getFieldtext($this->getVariable( 'activity_idea' ), array(
            'variable' => 'activity_idea',
            'width' => 1,
            'height' => 1,
            'opacity' => 0
        ));

        $this->data->scroll[] = $this->getFieldtext($this->getVariable( 'activity_address' ), array(
            'variable' => 'activity_address',
            'width' => 1,
            'height' => 1,
            'opacity' => 0
        ));

        $this->data->footer[] = $this->getTextbutton('Find My Ditto!', array('id' => 'create-date', 'style' => 'date-button'));
        $this->data->footer[] = $this->getTextbutton('Change status to accepter', array('id' => 'make-accepter', 'style' => 'button-text-red'));

    }

    public function requesterStep2() {

        // $this->rewriteActionConfigField('hide_menubar', 1);

        $this->checkRequestsDBStatus();

        // Initialize the "activity timer"
        // This method would check for active "matches" as well
        $this->checkActivityTimer();

        $this->data->scroll[] = $this->getText( 'Finding Your Ditto!', array( 'style' => 'timer-heading' ) );
        $this->getDateDescription();

        $this->requesterGetMatchlist();

        $this->data->footer[] = $this->getTextbutton('Cancel request', array('id' => 'step-1|end-date|cancel-date', 'style' => 'date-button'));
    }

    public function getMatchesArray() {
        $db_matches = $this->getMatchedUsers();

        $storage = new AeplayKeyvaluestorage();

        if ( empty($db_matches) ) {
            return false;
        }

        $matches = array();

        // Check if somebody else already confirmed the "matched" user
        foreach ($db_matches as $match) {
            $plans = $this->checkActivePlans( $match['play_id'] );

            if ( $plans ) {
                continue;
            }

            $requested_date = $storage->findByAttributes(array(
                'play_id' => $this->playid,
                'value' => $match['play_id'],
                'key' => 'requested_match',
            ));

            $stamp = 0;
            if ( $requested_date ) {
                $stamp = strtotime($requested_date->timestamp);
            }

            $match['time_requested'] = $stamp;
            $matches[] = $match;
        }

        usort($matches, array( $this, 'sortByStamp' ));

        return $matches;
    }

    public function sortByStamp($a, $b) {
        return $a['time_requested'] - $b['time_requested'];
    }

    public function requesterGetMatchlist() {

        $matches = $this->getMatchesArray();

        if ( empty($matches) ) {
            return false;
        }

        $sorted_matches = $this->getPendingAccepters( $matches );

        if ( $sorted_matches['pending_accepters'] ) {
            $this->renderMatches( $sorted_matches['pending_accepters'] );
        } else if ( $sorted_matches['accepters_with_chat'] ) {
            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->id = 'open-active-chats';
            $onclick->back_button = 1;
            $onclick->action_config = $this->getActionidByPermaname( 'chats' );
            $this->data->scroll[] = $this->getText('Preview your active requests', array( 'style' => 'listing-heading-button', 'onclick' => $onclick ));
        }

        // $this->data->scroll[] = $this->getSpacer( 15 );
        // $this->data->scroll[] = $this->getTextbutton('Ignore all', array('id' => 'step-2|all|decline', 'style' => 'comments-submit-btn'));
    }

    public function getPendingAccepters( $matches ) {

        $pending_accepters = array();
        $accepters_with_chat = array();

        foreach ($matches as $match) {

            if ( !isset($match['play_id']) ) {
                continue;
            }

            $play_id = $match['play_id'];

            if ( $this->checkForChatMessages( $play_id ) ) {
                $accepters_with_chat[] = $match;
            } else {
                $pending_accepters[] = $match;
            }

        }

        return array(
            'pending_accepters' => $pending_accepters,
            'accepters_with_chat' => $accepters_with_chat,
        );
    }

    public function renderMatches( $matches ) {
        $this->checkIfMatches( $matches );


        foreach ($matches as $user) {
            $col_left = array();
            $col_center = array();
            $col_right = array();
            $user_center_text = array();

            $accepter_id = $user['play_id'];

            $user_data = $this->getPlayVariables( $accepter_id );

            $distance = $this->getDistance( $user['distance'] );

            $image = 'image-placeholder.png';
            if ( isset($user_data['profilepic']) AND !empty($user_data['profilepic']) ) {
                $image = $user_data['profilepic'];
            }
            
            // IDs
            $view_id = 'step-3|' . $accepter_id;

            $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes', 'priority' => '9',));

            $col_left = $this->getColumn( array(
                $this->getImage( 'overlay.png', array( 'crop' => 'round', 'floating' => '1' ) ),
                $this->getImage( $listing_image, array( 'crop' => 'round', 'margin' => '1 1 1 1', 'priority' => '9' ) ),
            ), array( 'style' => 'listing-cell-left' ) );

            $user_center_text[] = $this->getText( $this->getFirstName( $user_data['name'] ), array( 'style' => 'lcc-heading' ));
            $user_center_text[] = $this->getText( $distance, array( 'style' => 'lcc-description' ));

            if ( isset($user_data[$this->tip_var_with_fee]) ) {
                $user_center_text[] = $this->getText( 'Tip: ' . $user_data[$this->tip_var_with_fee] . ' DC', array( 'style' => 'lcc-description' ));
            }

            $message = $this->getLatestMessage( $accepter_id );
            if ( $message ) {
                $user_center_text[] = $this->getText( 'Message: ' . Helper::truncateWords( $message->chat_message_text, 2 ), array( 'style' => 'lcc-description' ));
            }

            $col_center = $this->getColumn($user_center_text, array( 'style' => 'listing-cell-center' ));

            $results = $this->getUserVoteResults( $accepter_id );

            $col_right = $this->getColumn(array(
                $this->getImage('star-full-large.png', array( 'floating' => 1, 'vertical-align' => 'middle', 'text-align' => 'center' )),
                $this->getText($results, array('style' => 'star-text')),
            ), array( 'style' => 'listing-cell-right' ));

            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = $view_id;
            $onclick->back_button = 1;

            $this->data->scroll[] = $this->getRow(
                array( $col_left, $col_center, $col_right ),
                array( 'style' => 'listing-main-row', 'onclick' => $onclick )
            );

            unset($col_left);
            unset($col_center);
            unset($col_right);
            unset($user_center_text);
        }

    }

    public function accepterStep1() {

        $this->clearChatFlags();

        if ( !$this->allow_refresh ) {
            $this->refreshLocation();
            $this->viewNoRequests();
            return false;
        }

        $this->deleteVariable( 'last_opended_profile_id' );
        $this->deleteVariable( $this->date_id_var );
        
        $this->refreshLocation();

        $rq_data = $this->getSortedRequesters();

        $active_requesters = $rq_data['active_requesters'];
        $total_matches = $rq_data['total_matches'];

        if ( empty($total_matches) ) {
            $this->viewNoRequests();
            return false;
        }

        $label = ( $total_matches > 1 ? 'requests' : 'request' );

        $this->data->scroll[] = $this->getText( 'You have '. $total_matches .' activity plan ' . $label , array('style' => 'listing-heading'));

        if ( empty($active_requesters) ) {
            $onclick = new StdClass();
            $onclick->action = 'open-action';
            $onclick->id = 'open-active-chats';
            $onclick->back_button = 1;
            $onclick->action_config = $this->getActionidByPermaname( 'chats' );
            $this->data->scroll[] = $this->getText('Preview your active requests', array( 'style' => 'listing-heading-button', 'onclick' => $onclick ));
        }

        foreach ($active_requesters as $i => $user) {
            $requester_id = $user['play_id'];
            $user_data = $this->getPlayVariables( $requester_id );

            if ( !isset($user_data['timer']) ) {
                continue;
            }

            $timer_started = $user_data['timer'];
            $seconds_left = $this->default_time - ( $this->current_time - $timer_started );

            $distance = $this->getDistance( $user['distance'] );

            $image = 'image-placeholder.png';
            if ( isset($user_data['profilepic']) AND !empty($user_data['profilepic']) ) {
                $image = $user_data['profilepic'];
            }
            
            // IDs
            $view_id = 'step-2|' . $requester_id;

            $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes', 'priority' => '9',));
            $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes'));

            $left_column_output = array();

            $left_column_output[] = $this->getImage( 'overlay.png', array( 'crop' => 'round', 'floating' => '1' ) );
            $left_column_output[] = $this->getImage( $listing_image, array( 'crop' => 'round', 'margin' => '1 1 1 1', 'priority' => '9', 'image_fallback' => $fallback_image_path ) );

            if ( isset($user_data[$this->tip_var_with_fee]) AND !empty($user_data[$this->tip_var_with_fee]) ) {
                $left_column_output[] = $this->getText('Bonus: ' . $user_data[$this->tip_var_with_fee] . ' DC', array( 'style' => 'bonus-text' ));
            }

            $col_left = $this->getColumn( $left_column_output, array( 'style' => 'listing-cell-left' ) );

            $col_center = $this->getColumn( array(
                $this->getText($this->getFirstName( $user_data['name'] ), array( 'style' => 'lcc-heading' )),
                $this->getText($user_data['activity_idea'], array( 'style' => 'lcc-description' )),
                $this->getText($distance, array( 'style' => 'lcc-description' )),
            ), array( 'style' => 'listing-cell-center-acc' ) );

            $options['mode'] = 'countdown';
            $options['style'] = 'pick-date-timer-inner';
            $options['timer_id'] = 'acceptor-timer-' . $timer_started;

            $col_right = $this->getColumn(array(
                $this->getRow(array(
                    $this->getColumn(array(
                        $this->getImage('timer-background-inner.png'),
                    ), array( 'floating' => 1, 'vertical-align' => 'middle', 'text-align' => 'center' )),
                    $this->getColumn(array(
                        $this->getText('Expire in', array('style' => 'circle-text-timer')),
                        $this->getTimer($seconds_left, $options),
                    ), array( 'vertical-align' => 'middle', 'text-align' => 'center' ))
                )),
            ), array( 'style' => 'listing-cell-right' ));

            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = $view_id;
            $onclick->back_button = 1;

            $this->data->scroll[] = $this->getRow(
                array( $col_left, $col_center, $col_right ),
                array( 'style' => 'listing-main-row', 'onclick' => $onclick )
            );

            unset($col_left);
            unset($col_center);
            unset($col_right);
            unset($onclick);
            unset($options);
        }

        $storage = new AeplayKeyvaluestorage();
        $requested_date = $storage->findByAttributes(array(
            'value' => $this->playid,
            'key' => 'requested_match',
        ));

        if ( !$requested_date ) {
            $this->data->footer[] = $this->getTextbutton( 'Make a new plan', array( 'id' => 'make-a-plan', 'style' => 'no-matches-text-red' ) );
        } else {
            $this->data->footer[] = $this->getText( 'Make a new plan', array(
                'id' => 'make-a-plan',
                'style' => 'no-matches-text-red',
                'onclick' => $this->getPopupClick($this->getActionidByPermaname( 'confirmationpopup' ))
            ));
        }

    }

    public function getSortedRequesters() {
        $current_user_data = $this->varcontent;

        $lat = $this->varcontent['lat'];
        $lon = $this->varcontent['lon'];

        if ( empty($this->requestsobj) ) {
            $this->requestsobj = new MobiledatesModel();   
        }

        // $requestors = $this->getUsersToMatch();
        $requestors = $this->requestsobj->getActiveDates( $lat, $lon );

        if ( empty($requestors) ) {
            return array(
                'active_requesters' => array(),
                'requesters_with_application' => array(),
                'matched_users' => array(),
                'total_matches' => 0,
            );
        }

        $all_requesters = array();
        $active_requesters = array();
        $requesters_with_application = array();
        $matched_users = array();

        foreach ($requestors as $rq) {

            $remote_user_data = $this->getPlayVariables( $rq['play_id'] );

            if (
                ( !isset($remote_user_data['timer']) OR empty($remote_user_data['timer']) ) OR
                !$this->locationFilterPassed( $current_user_data, $remote_user_data ) OR
                empty($this->matchPreferences( $current_user_data, $remote_user_data )) OR
                !$this->ageFilterPassed( $current_user_data, $remote_user_data ) OR
                !$this->bonusFilterPassed( $current_user_data, $remote_user_data )
            ) {
                continue;
            }
            
            if ( !$this->checkUserValidity( $remote_user_data ) ) {
                continue;
            }

            $requested = $this->playkeyvaluestorage->valueExists( 'requested_match', $this->playid, $rq['play_id'] );
            $matched = $this->playkeyvaluestorage->valueExists( 'twoway_matches', $this->playid, $rq['play_id'] );

            $timer_started = $remote_user_data['timer'];
            $seconds_left = $this->default_time - ( $this->current_time - $timer_started );

            if ( $seconds_left < 1 ) {
                continue;
            }

            $all_requesters[] = $rq;

            if ( $requested ) {
                $requesters_with_application[] = $rq;
            } else if ( $matched ) {
                $matched_users[] = $rq;
            } else {
                $active_requesters[] = $rq;
            }
        }

        return array(
            'active_requesters' => $active_requesters,
            'requesters_with_application' => $requesters_with_application,
            'matched_users' => $matched_users,
            'total_matches' => count( $all_requesters ),
        );
    }

    public function viewProfileAccepter() {
        $this->checkRequestsDBStatus();

        $this->rewriteActionField( 'subject', 'Profile' );

        $user_data = $this->getRemoteUserPlayData( 'profile' );

        $this->data->scroll[] = $this->getColumn(array(
            $this->getImageSlider( $user_data ),
        ), array( 'style' => 'date-profile-heading' ));

        $this->getCurrentUserInfo( $user_data );

        $this->getAccepterDateDetails( $user_data );
        $this->getProfileButtonsAccepter( $user_data );
    }

    public function viewProfileRequester() {

        // Initialize the timer;
        $timer_started = $this->getVariable( 'timer_action' );
        $diff = $this->current_time - $timer_started;
        $this->time_left_for_action = $this->default_time_for_action - $diff;

        if ( $this->time_left_for_action <= 0 ) {
            $this->handleIgnoredState();
            $this->saveVariable( 'date_phase', 'step-1' );
            $this->requesterStep1();
            return true;
        }
        
        $user_data = $this->getRemoteUserPlayData( 'profile' );

        $play_id = false;
        if ( isset($user_data['user_play_id']) AND !empty($user_data['user_play_id']) ) {
            $play_id = $user_data['user_play_id'];
        }

        if (
            empty($play_id) OR
            $this->checkActivePlans( $play_id ) OR
            ( isset($user_data['date_preferences']) AND $user_data['date_preferences'] == 'requestor' )
        ) {
            $this->saveVariable( 'date_phase', 'step-2' );
            $this->requesterStep2();
            return true;
        }

        $this->checkRequestsDBStatus();

        $this->rewriteActionField( 'subject', 'Profile' );

        $this->data->scroll[] = $this->getColumn(array(
            $this->getImageSlider( $user_data ),
        ), array( 'style' => 'date-profile-heading' ));

        $this->getCurrentUserInfo( $user_data );

        if ( isset($user_data['profile_comment']) AND !empty($user_data['profile_comment']) ) {
            $this->data->scroll[] = $this->getText($user_data['profile_comment'], array( 'style' => 'pick-date-text' ));
        }

        if ( isset($user_data[$this->tip_var_with_fee]) AND !empty($user_data[$this->tip_var_with_fee]) ) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage( 'icon-dollar.png', array( 'width' => '20', 'margin' => '0 5 0 0' ) ),
                $this->getText( 'Tips: ' . $user_data[$this->tip_var_with_fee] . ' DC', array( 'style' => 'ditto-white-text' ) ),
            ), array( 'style' => 'ditto-info-row' ));
        }

        $message = $this->getLatestMessage( $play_id );

        if ( $message ) {
            $this->data->scroll[] = $this->getRow(array(
                $this->getImage( 'icon-message.png', array( 'width' => '20', 'margin' => '0 10 0 0' ) ),
                $this->getText( 'Message: ' . $message->chat_message_text, array( 'style' => 'ditto-white-text-medium' ) ),
            ), array( 'style' => 'ditto-info-row' ));
        }

        $this->getProfileButtonsRequestor( $user_data );
    }

    public function viewAcceptUser() {
        
        $this->checkRequestsDBStatus();

        $this->rewriteActionField( 'subject', 'Profile' );

        $user_data = $this->getRemoteUserPlayData( 'profile' );

        $message_field_class = 'ditto-message-form';
        $tip_field_class = 'ditto-tip-field';
        $var_id = $this->getVariableId( $this->tip_var );

        if ( $this->menuid == 'validate-accept-form' ) {

            if ( empty($this->submitvariables['request-note']) ) {
                $message_field_class = 'ditto-message-form-error';
                $this->error = true;
            }

            if ( !empty($this->submitvariables[$var_id]) AND !is_numeric($this->submitvariables[$var_id]) ) {
                $tip_field_class = 'ditto-tip-field-error';
                $this->error = true;
            } else if ( is_numeric($this->submitvariables[$var_id]) ) {
                    
                $funds_validation = $this->validateFunds( $this->submitvariables[$var_id] );

                if ( $funds_validation['success'] == false ) {
                    $this->funds_error = true;
                    $this->funds_error_code = $funds_validation['code'];
                }

            } elseif ( empty($this->submitvariables[$var_id]) ) { // Empty value
                // Delete "funds with fee" variable
                $this->deleteVariable( $this->tip_var_with_fee );
            }

            if ( !$this->error AND !$this->funds_error ) {

                $this->saveVariable( $var_id, $this->submitvariables[$var_id] );

                // Store the message as Chat entry
                $this->addMessageToUser( $user_data );

                $this->saveVariable( 'date_phase', 'step-1' );
                $this->phase = 'step-1';

                $this->connectUsers( $user_data['user_play_id'] );

                // Push - Found matches                
                if ( isset($user_data[$this->date_id_var]) AND !empty($user_data[$this->date_id_var]) ) {
                    $name = isset($this->varcontent['name']) ? $this->getFirstName( $this->varcontent['name'] ) : 'Anonymous';
                    $this->sendPlanPush( $user_data['user_play_id'], 'Found your ditto!', $name . ' said yes to your plans', 1, $user_data[$this->date_id_var] );
                }

                $this->data->onload = $this->getRedirect( $this->getActionidByPermaname( 'chats' ) );

                return true;
            }

        }

        $image = 'image-placeholder.png';
        if ( isset($user_data['profilepic']) AND !empty($user_data['profilepic']) ) {
            $image = $user_data['profilepic'];
        }

        $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes', 'priority' => '9',));
        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes'));

        $col_left = $this->getColumn(array(
            $this->getImage( 'overlay.png', array( 'crop' => 'round', 'floating' => '1' ) ),
            $this->getImage( $listing_image, array( 'crop' => 'round', 'margin' => '1 1 1 1', 'priority' => '9', 'image_fallback' => $fallback_image_path ) ),
        ), array( 'style' => 'listing-cell-left' ) );

        $col_center = $this->getColumn(array(
            $this->getText($this->getFirstName( $user_data['name'] ), array( 'style' => 'profile-right-cell-heading' )),
            $this->getText($user_data['activity_idea'], array( 'style' => 'profile-right-cell-description' )),
        ), array( 'style' => 'profile-right-cell' ));

        $this->data->scroll[] = $this->getRow(
            array( $col_left, $col_center ),
            array( 'style' => 'listing-main-row-transparent', )
        );

        $this->data->scroll[] = $this->getHairline('#734065', array(
            'margin' => '0 0 20 0'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText( 'Place a tip', array( 'style' => 'ditto-white-text' ) ),
                $this->getText( '(optional)', array( 'style' => 'ditto-white-text' ) )
            ), array(
                'width' => '50%',
                'vertical-align' => 'middle',
            )),
            $this->getColumn(array(
                $this->getFieldtext($this->getVariable( $this->tip_var ), array(
                    'style' => $tip_field_class,
                    'variable' => $var_id,
                    'hint' => 'Optional',
                    'input_type' => 'number',
                )),
            ), array(
                'width' => '50%'
            )),
        ), array(
            'margin' => '0 10 0 10'
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getText( 'Message', array( 'style' => 'ditto-white-text' ) )
        ), array(
            'margin' => '25 10 2 10'
        ));
        $this->data->scroll[] = $this->getFieldtextarea('', array( 'hint' => 'Send a message', 'style' => $message_field_class, 'variable' => 'request-note' ));

        $this->data->footer[] = $this->getTextbutton('« Back to Profile', array('id' => 'step-2', 'style' => 'button-text-plain'));

        $this->getFundsErrors();

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'validate-accept-form';
        $onclick->back_button = 1;
        $this->data->footer[] = $this->getText('I\'m keen', array( 'style' => 'date-button', 'onclick' => $onclick ));

    }

    public function getProfileButtonsAccepter( $user_data ) {

        if ( !isset($user_data['timer']) ) {
            return false;
        }

        $timer_started = $user_data['timer'];
        $seconds_left = $this->default_time - ( $this->current_time - $timer_started );

        $view_id = 'step-1';
        $accept_id = 'step-accept';

        $requested = $this->playkeyvaluestorage->valueExists( 'requested_match', $this->playid, $user_data['user_play_id'] );

        $this->facebookFriends( $user_data );

        $this->data->footer[] = $this->getTextbutton('« Back to Listing', array('id' => $view_id, 'style' => 'button-text-plain'));

        $matched = $this->playkeyvaluestorage->valueExists( 'requested_match', $this->playid, $user_data['user_play_id'] );

        if ( $matched ) {
            $chat_room_id = $this->getTwoWayChatId( $user_data['user_play_id'] );
            $this->getChatButton( $chat_room_id );
        }

        if ( $requested ) {
            $this->data->footer[] = $this->getText('Waiting confirmation', array( 'style' => 'date-button' ));
        } else {
            $onclick = new StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = $accept_id;
            $onclick->back_button = 1;

            $this->data->footer[] = $this->getRow(array(
                $this->getColumn(array(
                    $this->getText('I\'m keen', array( 'style' => 'rwt-heading', )),
                    $this->getRow(array(
                        $this->getText('(', array( 'style' => 'listing-heading-text', )),
                        $this->getTimer($seconds_left, array( 'style' => 'listing-heading-text', 'mode' => 'countdown', 'timer_id' => 'acceptor-timer-' . $timer_started )),
                        $this->getText(' mins remaining)', array( 'style' => 'listing-heading-text', )),
                    ), array( 'text-align' => 'center' )),
                )),
            ), array( 'style' => 'row-with-timer', 'onclick' => $onclick ));
        }

    }

    public function getChatButton( $chat_room_id ) {
        $chat_btn_params = array(
            'id' => $chat_room_id,
            'action' => 'open-action',
            'viewport' => 'bottom',
            'sync_open' => 1,
            'sync_close' => 1,
            'config' => $this->configobj->chat_action_id,
            'style' => 'date-button',
        );

        $chatModel = new Aechat();
        $chatModel->play_id = $this->playid;
        $chatModel->context = 'action';
        $chatModel->context_key = $chat_room_id;
        $chatModel->game_id = $this->gid;
        $new_msgs = $chatModel->getUnreadMessages();

        $chat_btn_text = 'Reply';
        if ( $new_msgs ) {
            $chat_btn_text = 'Reply (' . count($new_msgs) . ')';
        }

        $this->data->footer[] = $this->getTextbutton($chat_btn_text, $chat_btn_params);
    }

    public function getProfileButtonsRequestor( $user_data ) {

        if ( empty($user_data) ) {
            return false;
        }

        $this->facebookFriends( $user_data );
            
        $view_id = 'step-2';
        $accept_id = 'step-4|' . $user_data['user_play_id'] . '|accept';
        $decline_id = 'step-2|' . $user_data['user_play_id'] . '|decline';

        $this->data->footer[] = $this->getTextbutton('« Back to Listing', array('id' => $view_id, 'style' => 'button-text-plain'));
        $timer_started = $this->getVariable( 'timer_action' );

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = $accept_id;
        $onclick->back_button = 1;

        $chat_room_id = $this->getTwoWayChatId( $user_data['user_play_id'] );
        $this->getChatButton( $chat_room_id );

        /*
        $this->data->footer[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getTextbutton('Confirm?', array( 'id' => $accept_id, 'style' => 'rwt-heading', )),
                $this->getRow(array(
                    $this->getText('(', array( 'style' => 'listing-heading-text', )),
                    $this->getTimer($this->time_left_for_action, array( 'style' => 'listing-heading-text', 'mode' => 'countdown', 'timer_id' => 'requestor-timer-' . $timer_started )),
                    $this->getText(' mins remaining)', array( 'style' => 'listing-heading-text', )),
                ), array( 'text-align' => 'center' )),
            )),
        ), array( 'style' => 'row-with-timer', 'onclick' => $onclick ));
        */

        // $this->data->footer[] = $this->getTextbutton('Ignore', array('id' => $decline_id, 'style' => 'button-text-red'));
    }

    public function viewNoRequests() {
        $this->data->scroll[] = $this->getText( 'You have no activity plan requests', array( 'style' => 'no-matches-text' ) );
        $this->data->footer[] = $this->getTextbutton( 'Make a new plan', array( 'id' => 'make-a-plan', 'style' => 'no-matches-text-red' ) );
    }

    public function viewDate( $play_id = false ) {
        
        $this->checkRequestsDBStatus();

        $this->rewriteActionField( 'subject', 'Profile' );

        if ( !empty($play_id) ) {
            $user_data = $this->getPlayVariables( $play_id );
        } else {
            $user_data = $this->getRemoteUserPlayData();
            $play_id = ( isset($user_data['user_play_id']) ? $user_data['user_play_id'] : $this->playid );
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getImageSlider( $user_data ),
        ), array( 'style' => 'date-profile-heading' ));

        $this->getCurrentUserInfo( $user_data, $play_id );

        if ( $this->date_type == 'acceptor' ) {
            $this->getAccepterDateDetails( $user_data );
        } else {
            if ( isset($user_data['profile_comment']) AND !empty($user_data['profile_comment']) ) {
                $this->data->scroll[] = $this->getText($user_data['profile_comment'], array( 'style' => 'pick-date-text' ));
            }
        }

        $chat_room_id = $this->getTwoWayChatId( $play_id );
        $this->playkeyvaluestorage->set( 'chat-room-' . $this->playid, $chat_room_id, $this->playid );
        // $this->getChatButton( $chat_room_id );

        $step = ( $this->date_type == 'requestor' ? 'step-end-date' : 'step-3' );
        $this->data->footer[] = $this->getTextbutton('End activity', array('id' => $step, 'style' => 'button-text-red'));
    }

    public function endDate( $play_id = false ) {

        $this->stopScreenUpdate();

        if ( !empty($play_id) ) {
            $user_data = $this->getPlayVariables( $play_id );
        } else {
            $user_data = $this->getRemoteUserPlayData( 'state-end-date' );
            $play_id = ( isset($user_data['user_play_id']) ? $user_data['user_play_id'] : $this->playid );
        }

        $storage = new AeplayKeyvaluestorage();
        $active_chat_room = $storage->findByAttributes(array(
            'play_id' => $this->playid,
            'key' => 'chat-room-' . $this->playid,
        ));

        // Temporary disable the chat
        if ( $active_chat_room AND !empty($active_chat_room->value) ) {
            $chat_room_id = $active_chat_room->value;
            if ( $chat_room_id AND stristr($chat_room_id, '-') ) {
                $play_ids = explode('-', $chat_room_id);
                $this->playkeyvaluestorage->set( 'chat-flag', 1, $play_ids[0] );
                $this->playkeyvaluestorage->set( 'chat-flag', 1, $play_ids[2] );
            }
        }

        $this->checkChatMessages( $play_id );

        // Push - Date Ended
        if ( !preg_match('~rate-~', $this->menuid) ) {
            $this->sendPlanPush( $play_id, 'Activity ended', 'How was your experience?', 1 );
        }

        $image = 'image-placeholder.png';
        if ( isset($user_data['profilepic']) AND !empty($user_data['profilepic']) ) {
            $image = $user_data['profilepic'];
        }

        $listing_image = $this->getImageFileName($image, array('debug' => false,'imgwidth' => 400,'imgheight'=> 400, 'imgcrop' => 'yes', 'priority' => '9',));

        if(isset($user_data['name'])){
            $first_name = explode( ' ', $user_data['name'] );
        }

        $col_left = $this->getColumn( array(
            $this->getImage( 'overlay.png', array( 'crop' => 'round', 'floating' => '1' ) ),
            $this->getImage( $listing_image, array( 'crop' => 'round', 'margin' => '1 1 1 1', 'priority' => '9' ) ),
        ), array( 'style' => 'end-date-cell-left' ) );

        $name = isset($first_name[0]) ? $first_name[0] : '{#anonymous#}';
        $text = 'You have ended your activity with '. $name .', did you have a good experience?';
        $col_right = $this->getColumn( array(
            $this->getText($text, array( 'style' => 'ed-heading' )),
        ), array( 'style' => 'end-date-cell-right' ) );

        $this->data->scroll[] = $this->getRow(
            array( $col_left, $col_right ),
            array( 'style' => 'end-date-main-row' )
        );
        
        $this->data->scroll[] = $this->getText('', array('style' => 'row-divider-black'));

        $this->data->scroll[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText( 'Poor', array( 'style' => 'rlc-left' ) ),
            ), array( 'style' => 'rl-left-column' )),
            $this->getColumn(array(
                $this->getText( 'Best', array( 'style' => 'rlc-right' ) ),
            ), array( 'style' => 'rl-right-column' )),
        ), array( 'style' => 'ratings-legend' ));
        $this->data->scroll[] = $this->getStarsRatings( $play_id );

        $this->getCommentsForm();

        $this->data->footer[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getTextbutton('No Show', array( 'id' => 'step-1|end-date|no-show', 'style' => 'date-button-cancel' )),
            ), array( 'width' => '40%', 'margin' => '0 10 0 0' )),
            $this->getColumn(array(
                $this->getTextbutton('Cancel Plans', array( 'id' => 'step-1|end-date|cancel-plans', 'style' => 'date-button-cancel' )),
            ), array( 'width' => '40%', 'margin' => '0 0 0 10' )),
        ), array( 'text-align' => 'center' ));

    }

    public function viewInvalidRequest() {
        $this->data->scroll[] = $this->getText('Unfortunately the current user isn\'t valid!', array( 'style' => 'matches-heading' ));
        $this->data->scroll[] = $this->getImage('no-matches.png', array( 'style' => 'no-matches-image' ));

        $this->data->footer[] = $this->getTextbutton('Back', array('id' => 'step-1', 'style' => 'date-button'));
    }

    public function getAccepterDateDetails( $user_data ) {

        if ( isset($user_data['profile_comment']) AND $user_data['profile_comment'] ) {
            $this->data->scroll[] = $this->getText($user_data['profile_comment'], array( 'style' => 'pick-date-text' ));
        }

        $activity = isset($user_data['activity_idea']) ? $user_data['activity_idea'] : '';
        $location = isset($user_data['activity_address']) ? $user_data['activity_address'] : '';
        $tip = ( isset($user_data[$this->tip_var_with_fee]) ? $user_data[$this->tip_var_with_fee] : '' );
        $text = $activity . ' at ' . $location;
        $this->data->scroll[] = $this->getText($text, array( 'style' => 'pick-date-text' ));

        if ( $tip ) {
            $this->data->scroll[] = $this->getText('Tip amount: ' . $tip, array( 'style' => 'pick-date-text' ));
        }
    }

    public function viewDittoNotFound() {
        $this->clearTimers();
        $this->rewriteActionConfigField('hide_menubar', 1);
        $this->data->scroll[] = $this->getText('Unfortunately you have not found your ditto this time', array( 'style' => 'matches-heading' ));
        $this->data->scroll[] = $this->getImage('no-matches.png', array( 'style' => 'no-matches-image' ));
        $this->getDateDescription();
        $this->data->footer[] = $this->getTextbutton('Back', array('id' => 'step-1|change-acceptor', 'style' => 'date-button'));
    }

    public function getCommentsForm() {

        $classname = 'comments-form';
        $notes_var = 'feedback-notes';
        $submitted = false;
        $has_error = false;

        if ( isset($this->menuid) AND preg_match('~submit-form~', $this->menuid) ) {
            $submitted = true;
            if ( !empty($this->submitvariables[$notes_var]) ) {
                // $this->sendAdminNotifications();
            } else {
                // $has_error = true;
                // $classname = 'comments-form-error';
            }
        }

        $this->data->scroll[] = $this->getFieldtextarea('', array( 'hint' => 'Write a comment........', 'style' => $classname, 'variable' => $notes_var ));

        $profile_type = $this->varcontent['date_preferences'];

        if ( $profile_type == 'requestor' ) {
            $text = 'Submit Comment and Pay';
            $button_id = 'step-1|end-date|charge';
        } else {
            $text = 'Submit Comment and End Plan';
            $button_id = 'step-1|end-date|end-plan';
        }

        if ( $this->menuid == 'submit-form' AND !$has_error ) {
            
            $text = 'Thank you!';

            $onload = new StdClass();
            $onload->action = 'submit-form-content';
            $onload->id = $button_id;

            $this->data->onload[] = $onload;
        }
        
        $current_rating = $this->getRequestRatings();

        if ( $current_rating ) {
            $this->data->scroll[] = $this->getTextbutton($text, array( 'style' => 'comments-submit-btn', 'id' => 'submit-form' ));
        } else {
            $this->data->scroll[] = $this->getText($text, array( 'style' => 'comments-submit-btn' ));
        }

    }

    public function sendInvigationsToAccepters() {

        if ( $this->getVariable( 'invitations_sent' ) ) {
            return false;
        }

        $acceptors = $this->getAcceptors();

        if ( empty($acceptors) ) {
            return false;
        }

        $this->addToDebugLocal( 'Executed:', $this->mobilematchingobj->debug, 1050, 'dates-sub' );

        $cud = $this->varcontent;

        $idea = $cud['activity_idea'];
        $location = $cud['activity_address'];

        foreach ($acceptors as $acceptor) {
            $user_data = $this->getPlayVariables( $acceptor['play_id'] );

            $name = ( isset($user_data['name']) ? $user_data['name'] . '-' . $acceptor['play_id'] : '' );

            $this->addToDebugLocal( $name, 'Initialy in the list', 1057, 'dates-sub' );

            // Double check the preferences
            if ( !isset($user_data['date_preferences']) OR $user_data['date_preferences'] == 'requestor' ) {
                $pref = ( isset($user_data['date_preferences']) ? $user_data['date_preferences'] : 'Has missing date_preferences' );
                $this->addToDebugLocal( $name, $pref, 1065, 'dates-sub' );
                continue;
            }

            $this->addToDebugLocal( $name, 'Is Acceptor', 1065, 'dates-sub' );

            // Check if user has active plans
            if ( $this->checkActivePlans( $acceptor['play_id'] ) ) {
                continue;
            }

            $this->addToDebugLocal( $name, 'Has no active plans', 1072, 'dates-sub' );

            if ( !isset($user_data['logged_in']) OR $user_data['logged_in'] == 0 ) {
                continue;
            }

            $this->addToDebugLocal( $name, 'Is logged in', 1078, 'dates-sub' );

            $notify = AeplayVariable::fetchWithName($acceptor['play_id'], 'notify', $this->gid);
            if ( !$notify ) {
                $this->addToDebugLocal( $name, 'Has muted the notifiications', 1136, 'dates-sub' );
                continue;
            }

            // Filters
            if (
                !$this->locationFilterPassed( $user_data, $cud, $name ) OR
                empty($this->matchPreferences( $user_data, $cud, $name )) OR
                !$this->ageFilterPassed( $user_data, $cud, $name ) OR
                !$this->bonusFilterPassed( $user_data, $cud, $name )
            ) {
                continue;
            }

            $title = $this->getFirstName( $cud['name'] ) . ', ' . $cud['age'];
            $notification_text = ucfirst($idea) . ' at ' . $location;
            $this->sendPlanPush( $acceptor['play_id'], $title, $notification_text, 1 );
        }

        $this->saveVariable( 'invitations_sent', 1 );
        
        return true;
    }

    public function getCurrentUserInfo( $user_data, $play_id = false ) {

        if ( empty($user_data) ) {
            return false;
        }

        $remote_user_play_id = ( $play_id ? $play_id : $user_data['user_play_id'] );

        $remote_user_vars = $this->getPlayVariables( $remote_user_play_id );
        $vote_results = $this->getUserVoteResults( $remote_user_play_id, $remote_user_vars );

        if(isset($remote_user_vars['lat']) AND $remote_user_vars['lon']){
            $distance = $this->calculateDistance( $remote_user_vars['lat'], $remote_user_vars['lon'] );
            $distance = round($distance, 1) . ' KM';
        } else {
            $distance = 'distance unknown';
        }

        $age = isset($user_data['age']) ? $user_data['age'] : '?';

        $name = isset($user_data['name']) ? $this->getFirstName( $user_data['name'] ) : 'Anonymous';
        $this->data->scroll[] = $this->getRow(array(
            $this->getText($name, array( 'style' => 'total-ratings-heading' )),
            $this->getText(', ' . $age, array( 'style' => 'total-ratings-heading' )),
        ), array( 'margin' => '15 0 5 0', 'text-align' => 'center', 'vertical-align' => 'middle' ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('star-full.png', array( 'style' => 'total-ratings-results-image' )),
            $this->getText($vote_results . '   ', array( 'style' => 'total-ratings-results-text' )),
            $this->getImage('icon-location.png', array( 'style' => 'total-ratings-results-image' )),
            $this->getText($distance, array( 'style' => 'total-ratings-results-text' )),
        ), array( 'margin' => '3 0 5 0', 'text-align' => 'center', 'vertical-align' => 'middle' ));

    }

    public function getDateDescription() {

        if ( !isset($this->varcontent['activity_idea']) OR !isset($this->varcontent['activity_address']) ) {
            return false;
        }

        $timer_started = $this->getVariable( 'timer' );
        $options['timer_id'] = 'counter-id-' . $timer_started;
        $options['mode'] = 'countdown';
        $options['style'] = 'pick-date-timer';

        $desc_text = $this->varcontent['activity_idea'] . ' at ' . $this->varcontent['activity_address'];

        $this->data->scroll[] = $this->getText(ucfirst($desc_text) . '. Your request would expire in:', array( 'style' => 'timer-footer-small' ));
        $this->data->scroll[] = $this->getTimer($this->time_left, $options);
    }

    public function handleTimerRequester() {

        $this->loadVariables();

        $this->time_left = $this->default_time;

        $timer_started = $this->getVariable( 'timer' );

        // Failsafe
        if ( empty($timer_started) ) {
            return false;
        }

        $diff = $this->current_time - $timer_started;
        $this->time_left = $this->default_time - $diff;

        if ( $this->time_left <= 0 ) {
            // Move to the next step
            // Save the variable in order to store the latest position
            $this->saveVariable( 'date_phase', 'step-1' );

            $this->clearDateResults( 'end-date', 'time-expired' );

            return true;
        }

        return false;
    }

    public function checkIfMatches( $matches ) {

        // If Matches are found - don't do anything here
        if ( $matches ) {
            return false;
        }

        // Push - No Ditto Found
        $this->sendPlanPush( $this->playid, "Haven't found your ditto", 'Why not make another plan?' );

        // Update the Date in the Database
        if ( preg_match('~all|decline~', $this->menuid) ) {
            $this->cancelDBRequest( false, 'Ignored ( all users declined )' ); // User declined them all
        } else {
            $this->cancelDBRequest(); // No Respondents
        }

        $this->resetMatchAlways();

        $this->addToDebugLocal( 'Plan canceled ( no matches ) ' . $this->varcontent['name'] . '-' . $this->playid, 'Time: ' . date( 'Y-m-d H:i:s' ), 1325, 'dates-sub' );

        return true;
    }

    public function checkActivityTimer() {

        $this->time_left_for_action = $this->default_time_for_action;
        $timer_started = $this->getVariable( 'timer_action' );

        if ( $timer_started ) {

            $diff = $this->current_time - $timer_started;
            $this->time_left_for_action = $this->default_time_for_action - $diff;

            if ( $this->time_left_for_action < 0 ) {
                $this->handleIgnoredState();
                return false;
            }

        } else {
            $init_time = $this->getVariable( 'timer' );
            $this->saveVariable( 'timer_action', $init_time );
        }

        return true;
    }

    public function getErrorView( $type ) {

        $sibling = ( strtolower($type) == 'age' ? 'is' : 'are' );

        $info[] = $this->getText($type, array( 'style' => 'validation-text-home-bold' ));
        $info[] = $this->getText(' ' . $sibling . ' missing!', array( 'style' => 'validation-text-home' ));

        $this->data->scroll[] = $this->getRow($info, array( 'text-align' => 'center', 'margin' => '20 20 10 20' ));
    }

    public function getFundsErrors() {

        if ( empty($this->funds_error) ) {
            return false;
        }

        if ( $this->funds_error_code == 3 ) {
            $this->data->footer[] = $this->getText('Unfortunately you don\'t have enough credits.', array( 'style' => 'validation-text' ));
            $this->data->footer[] = $this->getText('You may buy additional credits using the App Settings.', array( 'style' => 'validation-text' ));
            
            $this->data->footer[] = $this->getSpacer( 7 );
            $this->data->footer[] = $this->getTextbutton('Buy More Credits', array(
                'id' => 'open-settings',
                'action' => 'open-action',
                'config' => $this->getConfigParam( 'settings_action_id' ),
                'style' => 'date-button-green',
            ));

        } else if ( $this->funds_error_code == 1 ) {
            $this->data->footer[] = $this->getText('Your Tip bonus should be more than 250 DC', array( 'style' => 'validation-text' ));
            $this->data->footer[] = $this->getSpacer( 7 );
        } else if ( $this->funds_error_code == 2 ) {
            $this->data->footer[] = $this->getText('Your Tip bonus should be less than 12 000 DC', array( 'style' => 'validation-text' ));
            $this->data->footer[] = $this->getSpacer( 7 );
        }

    }

    public function handleIgnoredState() {
        // Delete all users, who "requested a match" for this person's play
        $this->playkeyvaluestorage->del( 'requested_match' );

        // Cancel the Date in the Database
        $this->cancelDBRequest( false, 'Ignored ( no user picked )' );
        $this->resetMatchAlways();

        $this->addToDebugLocal( 'Plan canceled ( no users selected ) ' . $this->varcontent['name'] . '-' . $this->playid, 'Time: ' . date( 'Y-m-d H:i:s' ), 1488, 'dates-sub' );
    }

    public function checkDBUserData() {

        $show_screen = false;

        $required = array(
            'email', 'age', 'gender'
        );

        foreach ($required as $var) {
            if ( !isset($this->varcontent[$var]) OR empty($this->varcontent[$var]) ) {
                $show_screen = true;
            }
        }

        if ( !$show_screen ) {
            return false;
        }

        return true;
    }

    public function showDBUserDataView() {

        // $this->rewriteActionConfigField( 'refresh_on_open', false );

        if ( !isset($this->varcontent['name']) ) {
            return false;
        }

        $onload = new StdClass();
        $onload->action = 'open-action';
        $onload->id = 'open-details-popup';
        $onload->open_popup = true;
        $onload->action_config = $this->getConfigParam( 'details_action_id' );
        $this->data->onload[] = $onload;

        $name = $this->varcontent['name'];
        $first_name = $this->getFirstName( $name );

        $this->data->scroll[] = $this->getText( 'Hey '. $first_name .', it looks like we missed some of your details at the start. Complete them here to start finding your Ditto!', array( 'style' => 'missing-details-text' ) );

        $args = array(
            'id' => 'open-details-popup',
            'action' => 'open-action',
            'open_popup' => true,
            'config' => $this->getConfigParam( 'details_action_id' ),
            'style' => 'date-button'
        );

        $this->data->scroll[] = $this->getTextbutton( 'Submit details', $args );
    }

    public function addMessageToUser( $user_data ) {

        if ( !isset($user_data['user_play_id']) OR !isset($user_data['active_date_id']) OR empty($this->submitvariables['request-note']) ) {
            return false;
        }

        $date_id = $user_data['active_date_id'];
        $chat_room_id = $this->getTwoWayChatId( $user_data['user_play_id'] );
        $this->initMobileChat( 'action', $chat_room_id );

        $current_time = time();
        $time = date('Y-m-d H:i:s', $current_time);
        $message_text = $this->submitvariables['request-note'];

        $message_id = $this->mobilechatobj->addMessage( $message_text, $time );
        return $message_id;
    }

    public function getLatestMessage( $author_id ) {

        $chat_room_id = $this->getTwoWayChatId( $author_id );
        $chat = Aechatusers::model()->findByAttributes(array(
                    'context' => 'action',
                    'context_key' => $chat_room_id,
                ));
            
        if ( empty($chat) OR !isset($chat->chat_id) ) {
            return false;
        }
        
        $sql = "SELECT * FROM ae_chat_messages
                WHERE chat_id = $chat->chat_id
                # AND author_play_id = $author_id ORDER BY id DESC
                ORDER BY id DESC";

        $message = Aechatmessages::model()->findBySql( $sql );

        if ( empty($message) ) {
            return false;
        }

        return $message;
    }

    public function checkForChatMessages( $remote_user_play_id ) {
        
        $chat_room_id = $this->getTwoWayChatId( $remote_user_play_id );
        $chat = Aechatusers::model()->findByAttributes(array(
                    'context' => 'action',
                    'context_key' => $chat_room_id,
                ));
            
        if ( empty($chat) OR !isset($chat->chat_id) ) {
            return false;
        }
        
        $sql = "SELECT * FROM ae_chat_messages
                WHERE chat_id = $chat->chat_id
                AND author_play_id = $this->playid ORDER BY id DESC";

        $message = Aechatmessages::model()->findBySql( $sql );

        if ( empty($message) ) {
            return false;
        }

        return true;
    }

    public function createMatchRequest() {
        $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $this->playid ) );

        if ( empty($user) ) {
            return false;
        }
        
        $user->match_always = 1;
        $user->update();

        // Handle timers
        $this->addToDebugLocal( 'Created new request by ' . $this->varcontent['name'] . '-' . $this->playid, 'Time: ' . date( 'Y-m-d H:i:s' ), 1183, 'dates-sub' );

        $current_time = time();
        $active_request_id = $this->getVariable( $this->date_id_var );
        $this->saveVariable( 'timer', $current_time );

        $params = json_encode(array(
            'playid' => $this->playid,
            'app_id' => $this->gid,
            'request_id' => $active_request_id,
            'date_created_at' => $current_time, // The time, when the date request was created
            'time_to_date' => $this->default_time, // How long a date request would last
            'type' => 'open-date',
        ));

        Aetask::registerTask($this->playid, 'mobiledates:create_plan', $params, 'async', 90000);

        return true;
    }

}