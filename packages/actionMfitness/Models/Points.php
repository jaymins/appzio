<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;
use Bootstrap\Models\BootstrapModel;

Trait Points
{

    public function getMyPointsText(){
        if(!$this->points){
            $this->getMyPoints();
        }

        $thismonth = $this->points;
        $lastmonth = $this->getMyPoints(true);


        if($thismonth > $lastmonth){
            $difference = $thismonth - $lastmonth;
            return 'You have earned ' .$difference .' more points than last month at this stage. Keep up the good work!';
        } else {
            $difference = $lastmonth - $thismonth;
            return 'You still need to earn ' .$difference ." points to reach your last month's total";
        }
    }

    public function getMyPoints($lastmonth = false){
        $date = $this->sessionGet('statistics_date');

        if(isset($date['month_value'])){
            $date = $date['month_value'];
        } else {
            $date = time();
        }

        if($lastmonth){
            $startdate = strtotime('First day of last month',$date);
            $enddate = strtotime('Last day of last month',$date);
        } else {
            $startdate = strtotime('First day of this month',$date);
            $enddate = strtotime('Last day of this month',$date);
        }

        $sql = "SELECT ae_ext_calendar_entry.*,sum(points) as points
                FROM ae_ext_calendar_entry
                WHERE is_completed = '1'
                AND play_id = :playid
                AND ae_ext_calendar_entry.time > :startdate AND ae_ext_calendar_entry.time < :enddate 
                ORDER BY points
        ";

        $binds = [
            ':playid' => $this->playid,
            ':startdate' => $startdate,
            ':enddate' => $enddate
        ];

        $rows = @\Yii::app()->db->createCommand($sql)->bindValues($binds)->queryAll();

        if(isset($rows[0]['points'])){
            $this->points = $rows[0]['points'];
            return $rows[0]['points'];
        }

        return 0;
    }

    public function getMonthlyPoints($lastmonth = false){
        $date = $this->sessionGet('statistics_date');

        if(isset($date['month_value'])){
            $date = $date['month_value'];
        } else {
            $date = time();
        }

        $startdate = strtotime('First day of this month',$date);
        $enddate = strtotime('Last day of this month',$date);

        $sql = "SELECT ae_ext_calendar_entry.*,sum(points) as points
                FROM ae_ext_calendar_entry
                WHERE play_id = :playid
                AND is_completed = '1'
                AND ae_ext_calendar_entry.time > :startdate AND ae_ext_calendar_entry.time < :enddate 
                ORDER BY points
        ";

        $binds = [
            ':playid' => $this->playid,
            ':startdate' => $startdate,
            ':enddate' => $enddate
        ];

        $rows = @\Yii::app()->db->createCommand($sql)->bindValues($binds)->queryAll();

        if(isset($rows[0]['points'])){
            $this->points = $rows[0]['points'];
            return $rows[0]['points'];
        }

        return 0;
    }


}