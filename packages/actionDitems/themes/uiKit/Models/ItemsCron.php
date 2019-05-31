<?php

class ItemsCron extends CActiveRecord
{
    public $id;
    public $item_id;
    public $name;
    public $message;
    public $type;
    public $date;
    public $end_date;
    public $created_date;
    public $is_full_day;
    public $parent_reminder_id;
    public $recurring;
    public $frequency;
    public $to_calendar;
    public $is_completed;
    public $notification_sent;
    public $sent_recurring_notifications;

    public $next_date;
    public $gid;

    public function tableName()
    {
        return 'ae_ext_items_reminders';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'item' => array(self::BELONGS_TO, 'ItemCronModel', 'item_id'),
            'pattern' => array(self::HAS_ONE, 'ItemRemindersPatternCronModel', 'item_reminder_id'),
        );
    }

    public function setNotifications()
    {
        Yii::import('application.modules.aelogic.models.*');

        $reminders = $this->getCronReminders();

        $output = [];

        foreach ($reminders as $reminder) {
            $item = $reminder->item;
            $type = $item->type;

            $date = $reminder->date;

            if ($type == 'routine' AND !empty($reminder->pattern)) {
                $date = $this->getNextValidDate($reminder);
            }

            $reminder->next_date = $date;

            $output[] = $reminder;
        }

        // Sort the event's date
        usort($output, array($this, 'nextDateSort'));

        if (empty($output)) {
            return false;
        }

        foreach ($output as $reminder) {

            // Note: $date would be returned in GMT
            $date = $reminder->next_date;
            $play_id = $reminder->item->play_id;

            // Set the correct timezone for the current user
            if ($timezone = AeplayVariable::fetchWithName($play_id, 'timezone_id', $this->gid)) {
                date_default_timezone_set($timezone);
            }

            $time = time();

            // Get future events only
            if ($date < (time() - 86400)) {
                continue;
            }

            $name = ($reminder->name ? $reminder->name : $reminder->item->name);
            $time_left = $date - $time;

            if ($time_left > 960) {
                continue;
            }

            if ($this->notificationIsSent($reminder)) {
                continue;
            }

            if ($reminder->recurring) {
                // Set a flag that a notification is already sent for this specific occurrence date.
                $current_flags = $reminder->sent_recurring_notifications ?
                    json_decode($reminder->sent_recurring_notifications, true) : [];

                $current_flags[] = $reminder->next_date;

                $reminder->sent_recurring_notifications = json_encode($current_flags);
                $reminder->update();
            } else {
                $reminder->notification_sent = 1;
                $reminder->update();
            }

            // Skip sending if the user clicked the disabled button
            // Note that at this point a flag is already stored to the database so we wouldn't perform all checks unnecessary
            $notifications_disabled = AeplayVariable::fetchWithName($play_id, 'disable_notifications', $this->gid);
            if ($notifications_disabled) {
                continue;
            }

            print_r('Notification inserted successfully: ' . $name);

            $type = $reminder->type;
            $description = ($reminder->item->description ? $reminder->item->description : 'routine');

            if ($type == 'reminder') {
                $title = 'Performance Dialog - Follow-Up';
                $msg = date('g:i', $date) . ' - ' . $reminder->name . ' - ' . $reminder->item->name;
            } else if ($type == 'next_visit') {
                $title = 'Performance Dialog - Next Visit';
                $msg = date('g:i', $date) . ' - ' . $reminder->name;
            } else { // routine
                $title = $name;
                $msg = date('g:i', $date) . ' - ' . $description;
            }

            $play_id = $reminder->item->play_id;

            Aenotification::addUserNotification($play_id, $title, $msg, '+1', $this->gid);
        }

        return true;
    }

    public function getCronReminders()
    {
        $timestamp = time();

        $criteria = new \CDbCriteria;
        $criteria->alias = 'reminder';
        $criteria->order = 'item.id DESC';
        $criteria->condition = "(
				item.type = 'routine' AND (
					reminder.date >= $timestamp OR
					$timestamp <= reminder.end_date
				) XOR item.type <> 'routine'
			)
		";
        $criteria->condition .= " AND is_completed <> 1";

        $model = new ItemsCron();

        return $model->with(array(
            'item' => array(
                'alias' => 'item'
            ),
            'pattern' => array(
                'alias' => 'pattern'
            )
        ))->findAll($criteria);
    }

    public function getNextValidDate($reminder)
    {
        $interval = $reminder->pattern->separation_count;
        if (empty($interval))
            $interval = 1;

        // Set the correct timezone for the current user
        // Note that this would effect the date generation
        if (!empty($reminder->item->play_id)) {
            $timezone = AeplayVariable::fetchWithName(
                $reminder->item->play_id,
                'timezone_id',
                $this->gid
            );

            if ($timezone) {
                date_default_timezone_set($timezone);
            }
        }

        // Monthly event
        if ($day_of_month = $reminder->pattern->day_of_month) {
            $rrule = new \RRule([
                'FREQ' => 'MONTHLY',
                'INTERVAL' => $interval,
                'BYMONTHDAY' => $day_of_month,
                'DTSTART' => date('Y-m-d, g:i a', $reminder->date),
                'UNTIL' => date('Y-m-d, g:i a', $reminder->end_date),
            ]);

            foreach ($rrule as $occurrence) {
                $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

                // The Occurence period has already passed
                if ($occurrence_stamp < time()) {
                    continue;
                }

                return $occurrence_stamp;
            }
        } else if ($day_of_week = $reminder->pattern->day_of_week) {

            $map = $this->numberToWeekDays();
            $days = explode(',', $day_of_week);

            $byday = [];

            foreach ($days as $day) {
                $byday[] = $map[$day];
            }

            $rrule = new \RRule([
                'FREQ' => 'WEEKLY',
                'INTERVAL' => $interval,
                'BYDAY' => implode(', ', $byday),
                'DTSTART' => date('Y-m-d, g:i a', $reminder->date),
                'UNTIL' => date('Y-m-d, g:i a', $reminder->end_date),
            ]);

            foreach ($rrule as $occurrence) {
                $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

                // The Occurence period has already passed
                if ($occurrence_stamp < time()) {
                    continue;
                }

                return $occurrence_stamp;
            }

        }

        // The routine wouldn't re-occur for some reason
        return strtotime('+ 1 year');
    }

    public function nextDateSort($a, $b)
    {
        if ($a->next_date == $b->next_date) {
            return 0;
        }

        return ($a->next_date > $b->next_date) ? 1 : -1;
    }

    public function numberToWeekDays()
    {
        return [
            '1' => 'MO',
            '2' => 'TU',
            '3' => 'WE',
            '4' => 'TH',
            '5' => 'FR',
            '6' => 'SA',
            '7' => 'SU',
        ];
    }

    /**
     * Validate if a notification is already sent for a certain period
     *
     * @param $reminder
     * @return bool
     */
    private function notificationIsSent($reminder)
    {
        if ($reminder->recurring) {
            if ($reminder->sent_recurring_notifications) {
                $passed_stamps = json_decode($reminder->sent_recurring_notifications, true);

                if (in_array($reminder->next_date, $passed_stamps)) {
                    return true;
                }
            }
        } else {
            if ($reminder->notification_sent) {
                return true;
            }
        }

        return false;
    }

}