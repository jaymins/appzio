<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMcalendar\Models;

use Bootstrap\Models\BootstrapModel;
use packages\actionMnotifications\themes\swiss8\Models\CalendarNotificationModel;

class Model extends BootstrapModel
{

    use TestData;

    public $validation_errors;
    public $active_date;
    public $active_date_week;
    public $active_date_schedule;
    public $schedule_end_time;

    public $schedule_page = 15;
    public $schedule_request = false;


    public function getActiveDateDay(){
        $time = $this->active_date ? $this->active_date : time();
        return date('Y-m-d',$time);
    }

    public function getActiveDateWeek(){
        //echo(date('m-d-Y',$this->active_date_week));die();
        $time = $this->active_date_week ? $this->active_date_week : time();
        return date('Y\WW',$time);
    }

    public function getActiveDateSchedule(){
        //echo(date('m-d-Y',$this->active_date_week));die();
        $time = $this->active_date_schedule ? $this->active_date_schedule : time();
        return date('Y-m-d',$time);
    }

    public function getDailyCalendar()
    {

        $original_time = $this->active_date ? $this->active_date : time();

        $time = $this->active_date ? strtotime('-1 week Monday',$this->active_date) : strtotime('-1 week Monday');

        if (isset($_REQUEST['world_ending']) AND isset($_REQUEST['swid'])) {
            $time = strtotime($_REQUEST['swid']);
        }

        /* we need to add and remove one day in order not to include duplicate results */
        if (isset($_REQUEST['backwards']) AND $_REQUEST['backwards'] == 1) {
            $endtime = $time - 86400;
            $time = strtotime('-1 week Monday', $time);
        } elseif (isset($_REQUEST['swid'])) {
            $time = $time + 86400;
            $endtime = strtotime('+1 week Sunday', $time);
        } else {
            $endtime = strtotime('+1 week Sunday', $original_time);
        }

        return $this->getWeekForDaily($time, $endtime);

    }

    public function getSchedule($time = false, $endtime = false)
    {

        $this->schedule_request = true;

        if (empty($time)) {
            $time = $this->active_date_schedule ? $this->active_date_schedule : time();
        }

        // todo: implement paging
        $endtime = strtotime('+4 months', $time);
        $output = $this->getWeekData($time, $endtime);

        $this->schedule_end_time = $endtime;

        return $output;
    }

    public function getWeek()
    {

        //echo(date('m-d-Y',$this->active_date_week));

        $time = $this->active_date_week ? strtotime('This week Monday',$this->active_date_week) : time();

        if (isset($_REQUEST['backwards']) AND $_REQUEST['backwards'] == 1) {
            $endtime = strtotime('Last Sunday', strtotime($_REQUEST['swid']));
            $time = strtotime('-2 weeks Monday', $endtime);
        } elseif (isset($_REQUEST['swid'])) {
            $time = strtotime('Next Monday', strtotime($_REQUEST['swid']));
            $endtime = strtotime('+2 weeks Sunday', $time);
        } else {
            $time = strtotime('-2 weeks Monday', $time);
            $endtime = strtotime('+2 weeks Sunday', $time);
        }

        //echo(date('m-d-Y',$time));die();

        $data = $this->getWeekData($time, $endtime);

        foreach ($data as $key => $value) {
            $week = $value['week_num'];
            $output[$week][] = $value;
        }

        return $output;
    }

    private function getWeekData($time, $endtime)
    {
        $output = array();
        $data = $this->getWeekForDaily($time, $endtime);

        $count = 0;

        foreach ($data as $key => $day) {
            $day_time = strtotime($key);

            $output[$key]['day'] = date('j', $day_time);
            $output[$key]['name'] = date('D', $day_time);
            $output[$key]['week_num'] = date('Y\WW', $day_time);
            $output[$key]['items'] = array();
            $output[$key]['notifications'] = array();

            if ($day) {
                $count++;

                if ($count == 1) {
                    $output[$key]['notifications'] = $this->getPendingNotifications();
                }

                $output[$key]['items'] = $day;
            }
        }

        return $output;
    }

    public function getWeekForDaily($time, $endtime)
    {

        @\Yii::app()->db->createCommand(
            "SET time_zone='+00:00'"
        )->query();

        if($this->schedule_request){
            $limit = 'LIMIT '.$this->schedule_page .',15';
        } else {
            $limit = '';
        }


        $sql = "SELECT
                  calendar.*,
                  calendar.id AS entryid,
                  a5.name AS category_name,
                  a5.icon AS category_icon,
                  a5.color AS category_color,
                  calendar.time AS eventtime,
                  a3.name AS title,
                  a3.duration AS exercise_duration,
                  a4.name AS program_name,
                  a6.name AS recipe_name,
                  FROM_UNIXTIME(calendar.time,'%Y-%m-%d') AS readabledate,
                  FROM_UNIXTIME(calendar.time,'%k') AS readablehour,
                  FROM_UNIXTIME(calendar.time,'%i') AS readableminute,
                  DAYOFWEEK(FROM_UNIXTIME(calendar.time)) AS daynumber
                FROM ae_ext_calendar_entry AS calendar
                # LEFT JOIN ae_ext_calendar_entry_type a1 ON calendar.type_id = a1.id
                LEFT JOIN ae_ext_fit_program_exercise a2 ON calendar.exercise_id = a2.id
                LEFT JOIN ae_ext_fit_exercise a3 ON calendar.exercise_id = a3.id
                LEFT JOIN ae_ext_fit_program a4 ON calendar.program_id = a4.id
                LEFT JOIN ae_ext_fit_program_category a5 ON calendar.type_id = a5.id
                LEFT OUTER JOIN ae_ext_food_recipe a6 ON calendar.recipe_id = a6.id
                WHERE play_id = :playId AND 
                calendar.time > :time AND
                calendar.time < :endTime
                GROUP BY calendar.id
                ORDER BY calendar.time
                $limit
                ";

        $rows = @\Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid,
            ':time' => $time,
            ':endTime' => $endtime
        ))->queryAll();
        
        $start = $time;
        $count = 0;

        $maxcount = $endtime ? round(($endtime - $time) / 86400, 0) : 6;

        while ($count <= $maxcount) {
            $key = date('Y-m-d', $start);
            $output[$key] = [];
            $count++;
            $start = $start + 86400;
        }

        if (!$rows) {
            return $output;
        }

        foreach ($rows as $row) {
            $key = $row['readabledate'];
            $output[$key][] = $row;
        }


        return $output;
    }

    public function updateTime($id, $value)
    {
        $obj = CalendarModel::model()->findByPk($id);
        $hour = $value / 2 + 5;

        if (!isset($obj->time) OR !$id OR !$value) {
            return false;
        }

        $original = $obj->time;

        if (strstr($hour, '.')) {
            $parts = explode('.', $hour);
            if ($parts[1] == '5') {
                $hour = $parts[0] . ':' . '30';
            }
        } else {
            $hour = $hour . ':00';
        }

        $time = date('d.m.Y', $original);
        $time .= ' ' . $hour;

        $new_value = strtotime($time);
        $obj->time = $new_value;
        $obj->update();

        return true;
    }

    public function changeTime()
    {
        $vars = $this->getAllSubmittedVariables();
        foreach ($vars as $var) {
            $var = @json_decode($var, true);

            foreach ($var as $entry) {
                $id = key($entry);
                $value = $entry[$id];
                $this->updateTime($id, $value);
            }
        }
    }

    private function getPendingNotifications()
    {
        return CalendarNotificationModel::getPendingNotifications($this->playid);
    }

    public function getSchedulePageNumber()
    {
        if(!isset($_REQUEST['next_page_id'])){
            $this->schedule_page = 0;
        } else {
            $this->schedule_page = $_REQUEST['next_page_id'];
        }

        return $this->schedule_page+25;
    }

}