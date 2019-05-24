<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;
use Bootstrap\Models\BootstrapModel;

Trait Leaderboard
{


    public function setLeaderBoardFiltering(){
        $vars = $this->submitvariables;

        foreach($vars as $key=>$value){
            if($value){
                $filter[$value] = 1;
            }
        }

        if(isset($filter)){
            $this->sessionSet('leaderboard_filtering', $filter);
        }
    }


    public function clearLeaderBoardFiltering(){
        $this->sessionUnset('leaderboard_filtering');
        return true;
    }

    public function getLeaderboardFilters(){
        $veteran = $this->getSavedVariable('veteran_status');
        $occupation = $this->getSavedVariable('occupation');
        $zip = $this->getSavedVariable('zip');
        $country = $this->getSavedVariable('country');

        $output = array();

        if($veteran == 'Not a veteran'){
            $output['veteran'] = '{#other_non_veterans#}';
        } else {
            $output['veteran'] = '{#other#} '.$veteran.'s';
        }

        $output['occupation'] = '{#other#} '.$occupation .'s';
        $output['zip'] = '{#others_with_post_code#} '.$zip .'s';
        $output['country'] = '{#others_from#} '.$country;

        return $output;

    }


    public function getLeaderboard(){

        $date = $this->sessionGet('statistics_date');

        if(isset($date['month_value'])){
            $date = $date['month_value'];
        } else {
            $date = time();
        }

        $startdate = strtotime('First day of this month',$date);
        $enddate = strtotime('Last day of this month',$date);

        $sql = "SELECT 
                nametbl.*,nametbl.value AS real_name,
                countrytbl.*,countrytbl.value AS country,
                veterantbl.*,veterantbl.value AS veteran,
                ziptbl.*,ziptbl.value AS zip,
                occupationtbl.*,occupationtbl.value AS occupation,
                sum(points) AS points
                FROM ae_ext_calendar_entry
                LEFT JOIN ae_game_play_variable as nametbl ON (ae_ext_calendar_entry.play_id = nametbl.play_id AND nametbl.variable_id = :nameid)
                LEFT JOIN ae_game_play_variable as countrytbl ON (ae_ext_calendar_entry.play_id = countrytbl.play_id AND countrytbl.variable_id = :country)
                LEFT JOIN ae_game_play_variable as veterantbl ON (ae_ext_calendar_entry.play_id = veterantbl.play_id AND veterantbl.variable_id = :veteran_status)
                LEFT JOIN ae_game_play_variable as ziptbl ON (ae_ext_calendar_entry.play_id = ziptbl.play_id AND ziptbl.variable_id = :zip)
                LEFT JOIN ae_game_play_variable as occupationtbl ON (ae_ext_calendar_entry.play_id = occupationtbl.play_id AND occupationtbl.variable_id = :occupation)
        WHERE is_completed = 1
        AND ae_ext_calendar_entry.time > :startdate AND ae_ext_calendar_entry.time < :enddate 
        GROUP BY nametbl.play_id
        ORDER BY points desc
        ";

        $binds = [
            ':nameid' => $this->getVariableId('real_name'),
            ':country' => $this->getVariableId('country'),
            ':veteran_status' => $this->getVariableId('veteran_status'),
            ':zip' => $this->getVariableId('zip'),
            ':occupation' => $this->getVariableId('occupation'),
            ':startdate' => $startdate,
            ':enddate' => $enddate
        ];

        $rows = @\Yii::app()->db->createCommand($sql)->bindValues($binds)->queryAll();

        $filtering = $this->sessionGet('leaderboard_filtering');

        /* apply filters */
        if($filtering){
            foreach($rows as $key=>$row){
                foreach($filtering as $filter_name=>$filter){
                    if($this->getSavedVariable($filter_name)){
                        if($this->getSavedVariable($filter_name) != $row[$filter_name]){
                            unset($rows[$key]);
                        }
                    }
                }
            }
        }

        $rows = array_values($rows);

        /* return only 10 nearest items */
        if(count($rows) > 10){
            $count = 0;

            foreach ($rows as $row){
                if($row->play_id == $this->playid){
                    $marker = $count;
                }
                $count++;
            }
        }

        if(isset($marker)){
            if($marker > 5){
                $start = $marker-5;
            } else {
                $start = 0;
            }

            $inclusion_count = 0;

            while($inclusion_count < 10){
                if(isset($rows[$start])){
                    $output[$start] = $rows[$start];
                    $start++;
                }
                $inclusion_count++;
            }

            if(isset($output)){
                return $output;
            }
        }

        return $rows;

    }


}