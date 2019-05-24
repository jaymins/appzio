<?php


/* this is for creating a custom view for mobile matching */

namespace packages\actionMswipematch\Models;

use packages\actionMswipematch\Controllers\Controller;

trait MatchingPivot
{

    public $gid;

    public function createPivotView($pivot_vars = false)
    {

        if ($this->getSavedVariable('recreate_pivot')) {
            $this->reCreatePivot();
            $this->deleteVariable('recreate_pivot');
            return true;
        }

        $viewname = 'matching_' . $this->appid;
        $pivotcache = 'pivot_check_' . $viewname;

        $cache = \Appcaching::getGlobalCache($pivotcache);

        if (!$cache OR $cache + 720 < time()) {
            $this->reCreatePivot();
            return true;
        }

        if (!$pivot_vars) {
            $pivot_vars = $this->pivot_variables;
        }

        if ($this->pivotCheck($pivot_vars, $viewname)) {
            return true;
        }

        $this->reCreatePivot();
    }

    public function reCreatePivot()
    {

        $pivot_vars = $this->pivot_variables;
        $viewname = 'matching_' . $this->appid;
        $pivotcache = 'pivot_check_' . $viewname;

        try {
            @\Yii::app()->db->createCommand("DROP TABLE IF EXISTS $viewname")->query();
        } catch (\CDbException $e) {
            \Controller::sendAdminEmail('Error droppin table', serialize($e));
        }

        try {
            @\Yii::app()->db->createCommand("DROP VIEW IF EXISTS $viewname")->query();
        } catch (\CDbException $e) {
        }

        $field = '';
        $join = '';
        $metajoin = '';
        $datatype = '';
        $change = '';

        foreach ($pivot_vars as $var) {

            if (!isset($this->vars[$var])) {
                continue;
            }

            if (stristr($var, '-')) {
                $var = str_replace('-', '_', $var);
            }

            $varid = $this->vars[$var];
/*            if($var == 'gender' OR $var == 'sexual_orientaion' OR $var == ''){
                $field .= 'CAST(' . $var . 'tbl.value AS MEDIUMTEXT()) AS' . $var . ',' . chr(10);
            } else {*/
                $field .= 'CAST(' . $var . 'tbl.value AS CHAR(255)) AS ' . $var . ',' . chr(10);
            //}

            $tablename = $var . 'tbl';
            $join .= "LEFT JOIN ae_game_play_variable AS $tablename ON ae_ext_mobilematching.play_id = $tablename.play_id AND $tablename.variable_id = '$varid'" . chr(10);
            //$change .= "ALTER TABLE $viewname CHANGE $viewname.$var $viewname.$var VARCHAR( 255 ) DEFAULT '';";
            $change .= "ALTER TABLE $viewname ADD INDEX(`$var`);";
        }

        $change .= "ALTER TABLE $viewname ADD INDEX(`game_id`);";
        $change .= "ALTER TABLE $viewname ADD INDEX(`play_id`);";
        $change .= "ALTER TABLE $viewname ADD INDEX(`lat`);";
        $change .= "ALTER TABLE $viewname ADD INDEX(`lon`);";
        //$change .= "ALTER TABLE $viewname ADD INDEX(`gender`);";

        $sql = "CREATE TABLE $viewname AS
                SELECT 
	            $field 
	            ae_ext_mobilematching.id,
	            ae_ext_mobilematching.game_id,
	            ae_ext_mobilematching.play_id,
	            ae_ext_mobilematching.is_boosted,
	            ae_ext_mobilematching.boosted_timestamp,
	            ae_ext_mobilematching.match_always

	                FROM ae_ext_mobilematching
	                $join
	                $metajoin
	            WHERE reg_phasetbl.value = 'complete'
                GROUP BY ae_ext_mobilematching.play_id
                ORDER BY ae_ext_mobilematching.play_id DESC;
                $change
                ";

        \Yii::app()->db->createCommand($sql)->query();
        \Yii::app()->db->schema->refresh();

        \Appcaching::setGlobalCache($pivotcache, time());

    }

    public function pivotCheck($pivot_vars, $viewname)
    {
        $sql = "SHOW COLUMNS FROM $viewname";

        try {
            $test = @\Yii::app()->db->createCommand($sql)->queryAll();
        } catch (\CDbException $e) {
            return false;
        }

        if ($test) {
            $existing_columns = array();

            foreach ($test as $column) {
                $column = $column['Field'];
                $existing_columns[$column] = true;
            }

            if ($test) {
                foreach ($pivot_vars as $column) {
                    if (!isset($existing_columns[$column]) AND isset($this->vars[$column])) {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        return true;
    }


}