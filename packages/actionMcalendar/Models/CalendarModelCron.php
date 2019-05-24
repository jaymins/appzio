<?php

class CalendarModelCron extends CActiveRecord
{

    public $id;
    public $play_id;
    public $type_id;
    public $exercise_id;
    public $program_id;
    public $recipe_id;
    public $notes;
    public $points;
    public $time;
    public $completion;
    public $is_completed;
    public $completed_at;

    public $gid;

    public function tableName()
    {
        return 'ae_ext_calendar_entry';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return [];
    }

    public function setNotifications()
    {
        Yii::import('application.modules.aelogic.models.*');

        $users = $this->getAllUsers();

        if (empty($users)) {
            return false;
        }

        $current_day = date('l');

        switch ($current_day) {
            case 'Thursday':
                $this->getMidWeekNotifications($users);
                break;

            case 'Sunday':
                $this->getWeeklyNotifications($users);
                break;
        }

        return true;
    }

    private function getEntries(int $play_id)
    {
        $criteria = new \CDbCriteria;
        $criteria->alias = 'calendar';
        $criteria->condition = 'calendar.play_id = :play_id AND calendar.time >= :timestamp';
        $criteria->params = [
            ':play_id' => $play_id,
            ':timestamp' => strtotime('-1 week'),
        ];

        $entries = self::model()->findAll($criteria);

        return $entries;
    }

    public function getAllUsers()
    {
        $sql = "SELECT tbl1.play_id
                FROM ae_game_play_variable AS tbl1
                  LEFT JOIN ae_game_variable AS vars ON tbl1.variable_id = vars.id
                WHERE vars.game_id = :app_id
                GROUP BY tbl1.play_id
                ORDER BY tbl1.play_id DESC";

        $bind = [
            ':app_id' => $this->gid
        ];

        $plays = Yii::app()->db
            ->createCommand($sql)
            ->bindValues($bind)
            ->queryAll();

        if (empty($plays)) {
            return false;
        }

        $output = [];

        foreach ($plays as $play) {
            $play_id = $play['play_id'];
            $output[$play_id] = AeplayVariable::getArrayOfPlayvariables($play_id);
        }

        return $output;
    }

    private function getMidWeekNotifications($users)
    {
        foreach ($users as $play_id => $user_data) {
            // TODO: Implement the filtering here
            // notification_activity_completion
            // notification_headspace
            // notification_forum

            $message = 'How are you feeling this week? Remember to log your weekly headspace survey to track progress';

            $notification_id = Aenotification::addUserNotification(
                $play_id,
                'Swiss8 Headspace Reminder',
                $message,
                '+1',
                $this->gid,
                (object) [
                    'action' => 'open-action',
                    'action_config' => 599, // TODO: The id should be updated
                ]
            );

            CalendarNotificationsModelCron::insertNotification(
                $play_id,
                $notification_id,
                $message
            );
        }

        return true;
    }

    private function getWeeklyNotifications($users)
    {
        foreach ($users as $play_id => $user_data) {
            // TODO: Implement the filtering here
            // notification_activity_completion
            // notification_headspace
            // notification_forum

            $missed_entries = 0;

            $entries_last_week = $this->getEntries($play_id);

            if (empty($entries_last_week)) {
                continue;
            }

            /** @var CalendarModelCron $item */
            foreach ($entries_last_week as $item) {
                if (!$item->is_completed) {
                    $missed_entries++;
                }
            }

            if ($missed_entries) {
                $message = 'You missed a couple last week. Lets get this routine tight. Its a new week. lets go hard';
            } else {
                $message = 'You kicked every goal last week. Keep the momentum rolling this week';
            }

            $notification_id = Aenotification::addUserNotification(
                $play_id,
                'Swiss8 Weekly update',
                $message,
                '+1',
                $this->gid
            );

            CalendarNotificationsModelCron::insertNotification(
                $play_id,
                $notification_id,
                $message
            );
        }

        return true;
    }

}