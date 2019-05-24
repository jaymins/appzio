<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;
use Bootstrap\Models\BootstrapModel;

Trait Chart
{

    public function getMyChart(){

        $date = $this->sessionGet('statistics_date');
        $timeframe = $this->sessionGet('statistics_time');

        if(isset($date['month_value'])){
            $date = $date['month_value'];
        } else {
            $date = time();
        }

        if($timeframe == 'monthly'){
            $startdate = strtotime('First day of last month',$date);
            $enddate = strtotime('Last day of next month',$date);
        } else {
            $startdate = strtotime('First day of last month',$date);
            $enddate = strtotime('Last day of this month',$date);
        }

        $sql = "SELECT *, 
                    category.color as categorycolor, 
                    category.name AS categoryname,entry.id as entryid 
                    FROM ae_ext_calendar_entry entry
                LEFT JOIN ae_ext_fit_program program ON entry.program_id = program.id
                LEFT JOIN ae_ext_fit_program_category category on program.category_id = category.id
                WHERE 
                  entry.play_id = :playid 
                  AND is_completed = 1 
                  AND entry.time > :startdate
                  AND entry.time < :enddate
                  GROUP BY entry.id";

        $rows = @\Yii::app()->db->createCommand($sql)->bindValues(
            [
                ':playid' => $this->playid,
                ':startdate' => $startdate,
                ':enddate' => $enddate
            ]
        )->queryAll();

        $weeks = array();
        $sets = array();
        $categories = array();

        /* set weeks and categories */
        foreach ($rows as $key=>$row){
            
            if($timeframe == 'monthly'){
                $week = date('F',$row['time']);
                $rows[$key]['week'] = $week;
            } else {
                $week = date('W / M',$row['time']);
                $rows[$key]['week'] = $week;
            }
            
            $category = $row['categoryname'];

            if(!in_array($week, $weeks)){
                array_push($weeks,$week);
            }

            if(!in_array($category, $categories)){
                array_push($categories,$category);
            }
        }

        /* format data for chart module */
        foreach($rows as $row){
            $week = $row['week'];
            $pointer = array_keys($weeks,$week);
            $pointer = $pointer[0];
            $category = $row['categoryname'];

            $cat_pointer = array_keys($categories,$category);
            $cat_pointer = $cat_pointer[0];

            $point = new \stdClass();
            $point->x = $pointer+1;
            $point->y = $row['points'];

            $data['name'] = $category;
            $data['color'] = $row['categorycolor'];
            $data['points'][] = $point;

            if(!isset($sets[$cat_pointer])){
                $sets[$cat_pointer] = $data;
            } else {
                $sets[$cat_pointer]['points'][] = $point;
            }

            unset($data);

        }

        if(isset($weeks)){
            $chart['names'] = $weeks;
            $chart['sets'] = $sets;
            return $chart;
        }

        return array();

    }

    public function getTestChart(){

        $point1 = new \stdClass();
        $point1->x = 1;
        $point1->y = 10;

        $point2 = new \stdClass();
        $point2->x = 2;
        $point2->y = 40;

        $chart['names'] = [0 => 'January',1=>'February',2=>'March'];

        $chart['sets'][0]['name'] = 'Training';
        $chart['sets'][0]['color'] = '#9F0821';
        $chart['sets'][0]['points'] = [
            $point1,$point2
        ];

        $chart['sets'][1]['name'] = 'Mindfulness';
        $chart['sets'][1]['color'] = '#D99426';
        $chart['sets'][1]['points'] = [
            $point1,$point2
        ];

        return $chart;
    }


}