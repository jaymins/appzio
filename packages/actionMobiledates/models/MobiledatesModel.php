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
Yii::import('application.modules.aelogic.models.*');
Yii::import('application.modules.aelogic.article.models.*');
Yii::import('application.modules.aechat.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobiledatesModel extends ArticleModel {
// class MobiledatesModel {

    public $id;
    public $game_id;
    public $gid;
    public $actionid;
    public $debug;
    public $playid;
    public $lat;
    public $lon;
    

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'ae_ext_requests';
    }

    /*
    public function relations() {
        return array(
            'aeaction' => array(self::BELONGS_TO, 'Aeaction', 'playtask_id'),
            'aegame' => array(self::BELONGS_TO, 'Aegame', 'game_id'),
            'user' => array(self::BELONGS_TO, 'UserGroupsUseradmin', 'user_id'),
        );
    }
    */

    public function getAllUsers( $gid ) {
        $sql = "SELECT * FROM ae_ext_mobilematching
                WHERE game_id = :game_id
                ORDER BY id ASC";

        $args = array(
            ':game_id' => $gid,
        );

        $results = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( $args )
            ->queryAll();


        return $results;
    }

    public function getRequest( $play_id, $latest = true ) {
        $model  = new MobiledatesModel();

        $sql = "SELECT id
                FROM ae_ext_requests
                WHERE requester_playid = :play_id
                ORDER BY id DESC";

        $args = array(
            ':play_id' => $play_id,
        );

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( $args )
            ->queryAll();

        if ( empty($rows) ) {
            return false;
        }

        if ( $latest ) {
            return $rows[0]['id'];
        }

        return $rows;
    }

    public function getActiveDates( $lat, $lon ) {

        // Allow updates anyway
        if ( empty($lat) OR empty($lon) ) {
            return true;
        }

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT id, requester_playid as play_id,
                ( 3959 * 1.609344 * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
                * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat))
                * sin( radians(`lat`)))) AS `distance`
                FROM ae_ext_requests
                WHERE
                (
                  `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
                  AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
                )
                AND status = 'Active'
                AND (confirmed_respondent = NULL OR confirmed_respondent is NULL)
                GROUP BY play_id
                HAVING distance < 100
                ORDER BY id DESC";

        $command = Yii::app()->db->createCommand($sql);
        $results = $command->queryAll();

        return $results;
    }

    public function getUsersTotalPlans( $play_id, $consider_confirmations = false ) {

        $where = '';
        if ( $consider_confirmations ) {
            $where = "AND ( confirmed_respondent <> NULL OR confirmed_respondent != NULL OR confirmed_respondent IS NOT NULL )";
        }

        $sql = "SELECT COUNT(*) as total
                FROM ae_ext_requests
                WHERE requester_playid = :play_id
                $where
                ORDER BY id ASC";

        $args = array(
            ':play_id' => $play_id,
        );

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( $args )
            ->queryAll();

        if ( $rows ) {
            return $rows[0]['total'];
        } else {
            return 0;
        }
    }

    public function getUsersActivityInPlans( $play_id ) {
        $sql = "SELECT COUNT(*) as total
                FROM ae_ext_requests
                WHERE respondents LIKE :play_id
                ORDER BY id ASC";

        $args = array(
            ':play_id' => "%$play_id%",
        );

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( $args )
            ->queryAll();

        if ( $rows ) {
            return $rows[0]['total'];
        } else {
            return 0;
        }
    }

    public function getExpiredPlans() {
        $sql = "SELECT id, timestamp, status, confirmed_respondent
                FROM ae_ext_requests
                WHERE status = 'Active'
                # AND timestamp < DATE_SUB(NOW(), INTERVAL 1 HOUR)
                AND timestamp < DATE_SUB(NOW(), INTERVAL 1 DAY)
                AND (
                    confirmed_respondent IS NULL
                    OR confirmed_respondent = NULL
                    OR confirmed_respondent = ''
                )
                ORDER BY id ASC";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        return $rows;
    }

    public function resetInactiveRequestors() {

        $sql = "SELECT * FROM ae_ext_requests ORDER BY id ASC LIMIT 1";

        $request = $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        if ( empty($request) ) {
            return false;
        }

        $requester_playid = $request[0]['requester_playid'];

        $play_obj = Aeplay::model()->findByPk( $requester_playid );

        if ( !is_object($play_obj) ) {
            return false;
        }

        $gid = $play_obj->game_id;

        if ( empty($gid) ) {
            return false;
        }

        $sql = "SELECT * FROM ae_ext_mobilematching
                WHERE game_id = :game_id
                ORDER BY id ASC";

        $args = array(
            ':game_id' => $gid,
        );

        $app_users = $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues( $args )
            ->queryAll();

        if ( empty($app_users) ) {
            return false;
        }

        $current_time = time();

        foreach ($app_users as $user) {

            if ( empty($user['play_id']) ) {
                continue;
            }

            $play_id = $user['play_id'];

            $variables = AeplayVariable::getArrayOfPlayvariables( $play_id );

            if ( !isset($variables['to_requestor_stamp']) OR empty($variables['to_requestor_stamp']) ) {
                continue;
            }

            if ( !isset($variables['date_preferences']) OR !isset($variables['date_phase']) ) {
                continue;
            }

            $stamp = $variables['to_requestor_stamp'];
            
            if ( $variables['date_preferences'] == 'requestor' AND $variables['date_phase'] == 'step-1' ) {
                // ~ one hour has passed
                if ( ($current_time - $stamp) > 86400 ) {
                    AeplayVariable::updateWithName( $play_id, 'date_preferences', 'acceptor', $gid );
                }
            }

        }

    }

    public function checkTimerRespondents() {

        $plansModel = new MobiledatesModel();
        $tasksModel = new Aetask();

        $tasks = $tasksModel->findAllByAttributes(array(
                'status' => 0,
                'task' => 'mobiledates:create_plan'
            ));

        if ( empty($tasks) ) {
            return false;
        }

        foreach ($tasks as $task) {
            $play_id = $task->play_id;

            if ( empty($task->parameters) ) {
                continue;
            }
            
            $options = json_decode( $task->parameters, true );

            if ( !isset($options['request_id']) OR empty($options['request_id']) ) {
                continue;
            }

            $type = $options['type'];

            // Find the corresponding request
            $request_id = $options['request_id'];
            $request = $plansModel->findByPk( $request_id );

            if ( empty($request) ) {
                continue;
            }

            $stamp = strtotime( $request->timestamp );
            $current_time = time();

            $diff = $current_time - $stamp;
            $time_left = $options['time_to_date'] - $diff;

            if ( in_array($time_left, range(580, 600)) ) {
                self::send10MinPush( $request, $play_id );
            }

            if ( $time_left > 0 ) {
                continue;
            }

            if ( $type == 'open-date' ) {
                self::handleOpenDateOperations( $request, $options, $play_id );
            } else {
                self::handleClosedDateOperations( $request, $options, $play_id );
            }

            $task->status = 1;
            $task->update();
        }

        return true;
    }

    public static function send10MinPush( $request, $play_id ) {

        if ( empty($request->respondents) ) {
            return false;
        }

        $text = 'Hurry up!';
        $description = 'Pick you date';

        $pushes = $request->sent_pushes;
        $pushes = ( empty($pushes) ? array() : json_decode( $pushes, true ) );

        $db_string = $play_id . $text . $description;
        $db_string = strtolower( str_replace(' ', '-', $db_string) );

        if ( !in_array($db_string, $pushes) ) {

            $variables = AeplayVariable::getArrayOfPlayvariables( $play_id );

            if ( isset($variables['notify']) AND $variables['notify'] ) {
                Aenotification::addUserNotification( $play_id, $text, $description, 1 );
                $pushes[] = $db_string;
                $request->sent_pushes = json_encode( $pushes );
                $request->update();
            }

        }

        return true;
    }

    public static function handleOpenDateOperations( $request, $options, $play_id ) {

        // This Date has active respondents, don't do anything
        if ( $request->confirmed_respondent ) {
            return false;
        }

        $app_id = $options['app_id'];

        if ( $request->respondents ) {
            $respondents = json_decode( $request->respondents, true );
            
            foreach ($respondents as $requester_play_id => $stamp) {
                $chat_room_id = self::getTwoWayChatId( $play_id, $requester_play_id );
                Aechat::model()->deleteAllByAttributes(array(
                    'context_key' => $chat_room_id
                ));
            }
        }

        $request->status = 'Timed Out';
        $text = "Haven't found your ditto";
        $description = 'Why not make another plan?';

        $pushes = $request->sent_pushes;
        $pushes = ( empty($pushes) ? array() : json_decode( $pushes, true ) );

        $db_string = $play_id . $text . $description;
        $db_string = strtolower( str_replace(' ', '-', $db_string) );
        
        if ( !in_array($db_string, $pushes) ) {

            $variables = AeplayVariable::getArrayOfPlayvariables( $play_id );

            if ( isset($variables['notify']) AND $variables['notify'] ) {
                Aenotification::addUserNotification( $play_id, $text, $description );
                $pushes[] = $db_string;
                $request->sent_pushes = json_encode( $pushes );
            }

            $request->update();
        }

        // Logic for the current user
        $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $play_id ) );
        $user->match_always = 0; // Prevent showing up in the listing and re-enable notifications
        $user->update();

        AeplayVariable::deleteWithName( $play_id, 'activity_tip_amount', $app_id );
        AeplayVariable::deleteWithName( $play_id, 'activity_tip_amount_with_fee', $app_id );

        AeplayVariable::updateWithName( $play_id, 'date_preferences', 'acceptor', $app_id );
        AeplayVariable::updateWithName( $play_id, 'date_phase', 'step-1', $app_id );

        $storage = new AeplayKeyvaluestorage();
        $storage->deleteAllByAttributes(array(
            'play_id' => $play_id,
            'key' => 'requested_match'
        ));

        return true;
    }

    public static function handleClosedDateOperations( $request, $options, $play_id ) {
        $text = '';
        $description = '';
        $badge_count = 0;
        $app_id = $options['app_id'];

        if ( empty($request->respondents) ) {
            $request->status = 'Timed Out';
            $text = "Haven't found your ditto";
            $description = 'Why not make another plan?';
        } else if ( $request->respondents ) {
            $text = 'Found your ditto!';
            $description = 'See who said yes to your plans';
            $badge_count = 1;
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
            Aenotification::addUserNotification( $play_id, $text, $description, $badge_count );
            $pushes[] = $db_string;
            $request->sent_pushes = json_encode( $pushes );
            $request->update();

            // Change the user's status to "Accepter"
            if ( empty($request->respondents) ) {
                
                // AeplayVariable::updateWithName( $play_id, 'date_preferences', 'acceptor', $app_id );
                // AeplayVariable::updateWithName( $play_id, 'date_phase', 'step-1', $app_id );

                // Logic for the current user
                $user = MobilematchingModel::model()->findByAttributes( array( 'play_id' => $play_id ) );
                $user->match_always = 0; // Prevent showing up in the listing and re-enable notifications
                $user->update();

            } else if ( $request->respondents ) {
                AeplayVariable::updateWithName( $play_id, 'timer_action', time(), $app_id );
            }
        }

    }

    public function checkAccepterState() {

        $plansModel = new MobiledatesModel();
        $tasksModel = new Aetask();

        $tasks = $tasksModel->findAllByAttributes(array(
                'status' => 0,
                'task' => 'mobiledates:said_yes'
            ));

        if ( empty($tasks) ) {
            return false;
        }

        $text = 'You have not been confirmed';
        $description = 'Why not make your plans instead?';
        $badge_count = 0;

        foreach ($tasks as $task) {

            $play_id = $task->play_id;
            // $created = $task->launchtime;
            
            if ( empty($task->parameters) ) {
                continue;
            }
            
            $options = json_decode( $task->parameters, true );

            if ( !isset($options['request_id']) OR empty($options['request_id']) ) {
                continue;
            }

            $created = $options['date_created_at'];
            $default_time_sec = $options['time_to_date'];

            // We should wait for the date to expire
            if ( $created + $default_time_sec > time() ) {
                continue;
            }

            // Find the corresponding request
            $request_id = $options['request_id'];
            $request = $plansModel->findByPk( $request_id );

            if ( empty($request) ) {
                continue;
            }

            $status = $request->status;
            $respondents = $request->respondents;
            $confirmed_person = $request->confirmed_respondent;

            if (
                empty($confirmed_person) OR
                $confirmed_person != $play_id OR
                ($status == 'Canceled' AND !empty($respondents))
            ) {
                $pushes = $request->sent_pushes;

                if ( empty($pushes) ) {
                    $pushes = array();
                } else {
                    $pushes = json_decode( $pushes, true );
                }

                $db_string = $play_id . $text . $description;
                $db_string = strtolower( str_replace(' ', '-', $db_string) );

                if ( !in_array($db_string, $pushes) ) {
                    Aenotification::addUserNotification( $play_id, $text, $description, $badge_count );
                    $pushes[] = $db_string;
                    $request->sent_pushes = json_encode( $pushes );
                    $request->update();

                    $task->status = 1;
                    $task->update();
                }
            }

        }

    }

    public static function getTwoWayChatId( $id, $playid ) {
        
        if ( $id < $playid ) {
            $chatid = $id . '-chat-' . $playid;
        } else {
            $chatid = $playid . '-chat-' . $id;
        }

        return $chatid;
    }

}