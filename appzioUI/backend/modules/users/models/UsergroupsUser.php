<?php

namespace backend\modules\users\models;

use Yii;
use backend\components\Helper;
use \backend\models\DbadminQueries;
use \backend\modules\users\models\base\UsergroupsUser as BaseUsergroupsUser;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "usergroups_user".
 */
class UsergroupsUser extends BaseUsergroupsUser
{

  public $args = array();

  private $connection;
  private $and = '';
  private $and_play = '';
  private $custom_users_where;
  private $do_calc_rows = false;
  private $main_sql;
  private $total_count = 0;
  private $users_check_type = 'general';
  private $store_query_after_execuation = false;

  public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public function init() {
        $this->connection = Yii::$app->db;
    }

    public function getUsers( $page, $app_id = null, $play_id = null, $disable_paging = false ) {

        /*
        if ( isset($_GET['query']) AND $this->queryHasParameters() ) {

            $check = $_GET['query'];
            $check['app_id'] = Yii::$app->session['app_id'];
            $md5query = md5(json_encode($check));

            if ( Yii::$app->session['check'] != $md5query ) {
                $page = 1;
            } else {
                Yii::$app->session['check'] = $md5query;
            }

        }
        */

        if ( $app_id ) {
            $this->and = "AND ae_game_play.game_id = :app_id";
            // $this->and .= "\n";
            // $this->and .= " AND usergroups_user.play_id > :last_known_id";
            $this->args = array_merge($this->args, array(
                ':app_id' => $app_id,
                // ':last_known_id' => 112069,
            ));
        }

        if ( $play_id ) {
            $this->and_play = "AND ae_game_play.id = :play_id";
            $this->args = array_merge($this->args, array(
                ':play_id' => $play_id,
            ));
        }

        $this->custom_users_where = $this->getUsersWhereClause();

        $joins = $this->getPlayJoin();
        $play_vars = $joins['play_vars'];
        $game_play_vars = $joins['game_play_vars'];
        $joins_select = $joins['select'];

        $ext_joins = $this->getExtJoins();

        // Determine whether MySQL should calculate the rows
        if ( $joins_select == 'gpv1' ) {
            $this->do_calc_rows = 'SQL_CALC_FOUND_ROWS';
            $this->users_check_type = 'inline_calc';
        }

        $var_wheres = $this->getWheres( 'wheres' );

        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        if ($disable_paging){
            $paging = "";
        }else{
            $paging = "LIMIT $offset, $per_page";
        }
        ;
        $this->main_sql = "SELECT {$this->do_calc_rows} ae_game_play.user_id AS userid,
                $joins_select.*,
                -- usergroups_user.play_id as current_play_id,
                -- usergroups_user.*,
                usergroups_user.username,
                usergroups_user.email as original_email,
                usergroups_user.creation_date,
                usergroups_user.active_app_id
                FROM ae_game_play
                LEFT JOIN usergroups_user ON usergroups_user.id = ae_game_play.user_id
                $play_vars
                $game_play_vars
                $ext_joins
                WHERE
                    ( $joins_select.play_id <> 0 OR $joins_select.play_id <> 0 OR $joins_select.play_id != 0 ) AND
                    ( usergroups_user.play_id <> 0 OR usergroups_user.play_id <> 0 OR usergroups_user.play_id != 0 )
                $this->and
                $this->and_play
                $this->custom_users_where
                $var_wheres
                GROUP BY ae_game_play.id
                ORDER BY ae_game_play.id DESC 
                ". $paging;

        // Get the total count
        if ( $this->users_check_type == 'general' ) {
            $this->getGeneralCount();
        } else {
            $this->getInlineCount();
        }

        $command = $this->connection->createCommand( $this->main_sql );

        if ( !empty($this->args) ) {
            $command->bindValues( $this->args );
        }

        $users = $command->queryAll();

        if ( empty($users) ) {
            return array(
                'users' => array(),
                'count' => 0,
            );
        }

        // Store the currently executed query
        // and get the total_count
        if ( $this->store_query_after_execuation ) {
            $this->storeInlineCount();
        }
        
        return array(
            'users' => $users,
            'count' => $this->total_count,
        );
    }

    public function getExtJoins() {

        if ( !isset($_GET['query']) ) {
            return false;
        }

        $var_params = Helper::getVariablesConfig();

        $include_ext = false;
        $ext_vars = array();

        foreach ($var_params as $var_key => $var_options) {

            if ( $var_options['location'] == 'mobilematching' ) {
                if ( isset($_GET['query'][$var_key]) ) {
                    $include_ext = true;
                    $ext_vars[$var_key] = $var_options;
                }
            }
        }

        if ( empty($include_ext) ) {
            return false;
        }

        return "LEFT JOIN ae_ext_mobilematching matching ON ae_game_play.id = matching.play_id";
    }

    public function getPlayJoin() {
            
        if ( !isset($_GET['query']) ) {
            return array(
                'select' => "ae_game_play_variable",
                'play_vars' => "LEFT JOIN ae_game_variable game_vars ON ae_game_play.game_id = game_vars.game_id",
                'game_play_vars' => "LEFT JOIN ae_game_play_variable ON ae_game_play.id = ae_game_play_variable.play_id",
            );
        }

        $parameters = $_GET['query'];

        $config_params = Helper::getVariablesConfig();

        foreach ($config_params as $cp_key => $cp_config) {
            if ( $cp_config['location'] != 'variables' ) {
                unset( $config_params[$cp_key] );
            }
        }

        $var_params = array_keys( $config_params );

        $join_params = array();

        foreach ($var_params as $vp) {
            if ( isset($parameters[$vp]) AND $parameters[$vp] ) {
                $join_params[$vp] = $parameters[$vp];
            }
        }

        if ( empty($join_params) ) {
            return array(
                'select' => "ae_game_play_variable",
                'play_vars' => "LEFT JOIN ae_game_variable game_vars ON ae_game_play.game_id = game_vars.game_id",
                'game_play_vars' => "LEFT JOIN ae_game_play_variable ON ae_game_play.id = ae_game_play_variable.play_id"
            );
        }

        $count = 0;

        $sql_p_vars = '';
        $sql_gp_vars = '';
            
        foreach ($join_params as $jp_key => $jp_value) {
            $count++;
            $int_key = 'gpv' . $count;

            $sql_p_vars .= "LEFT JOIN ae_game_variable p_{$int_key} ON ( ae_game_play.game_id = p_{$int_key}.game_id )";
            $sql_p_vars .= "\n";

            $sql_gp_vars .= "LEFT JOIN ae_game_play_variable {$int_key} ON ( ae_game_play.id = {$int_key}.play_id )";
            $sql_gp_vars .= "\n";
        }

        return array(
            'select' => "gpv1",
            'play_vars' => $sql_p_vars,
            'game_play_vars' => $sql_gp_vars
        );
    }

    public function getWheres() {

        if ( !isset($_GET['query']) ) {
            return false;
        }

        $query_params = $this->getQueryParams();

        $custom_where_sql = '';
        $count = 0;
        $parameters = $_GET['query'];

        // Don't include the following vars
        $exclude_vars = array(
            'matching_options'
        );

        foreach ($query_params as $param => $match_type) {
            
            if ( !isset($_GET['query'][$param]) OR empty($_GET['query'][$param]) ) {
                continue;
            }

            if ( !in_array($match_type, $exclude_vars) ) {
                $count++;
            }

            $int_key = 'gpv' . $count;

            $get_param = $_GET['query'][$param];

            $sub_select = "( {$int_key}.variable_id = ( SELECT max(id) FROM ae_game_variable WHERE ae_game_variable.name = '{$param}' AND ae_game_variable.game_id = :app_id ) )";

            switch ($match_type) {
                case 'loose_match':
                    $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value LIKE '%{$get_param}%' )";
                    break;
                    
                case 'exact_match':
                    $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value = '{$get_param}' )";
                    break;

                case 'reg_options':

                    if ( $get_param == 'complete' ) {
                        $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value = '{$get_param}' )";
                    } else {
                        $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10) )";
                    }

                    break;

                case 'role_options':

                    if ( $get_param == 'landlord' ) {
                        $sub_select = "( {$int_key}.variable_id = ( SELECT max(id) FROM ae_game_variable WHERE ae_game_variable.name = 'subrole' AND ae_game_variable.game_id = :app_id ) )";
                    }
                    
                    $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value = '{$get_param}' )";

                    break;

                case 'approved_options':

                    if ( $get_param == 'yes' ) {
                        $custom_where_sql .= "AND ( $sub_select AND {$int_key}.value = '1' )";
                    } else {
                        $custom_where_sql .= "AND ( $sub_select AND ( {$int_key}.value = '0' OR {$int_key}.value IS NULL ) )";
                    }

                    break;

                case 'matching_options':

                    if ( $get_param == 'yes' ) {
                        $custom_where_sql .= "AND matching.flag > 0";
                    } else {
                        $custom_where_sql .= "AND matching.flag < 1";
                    }

                    break;
            }

            $custom_where_sql .= "\n";
        }

        return $custom_where_sql;
    }

    public function getUsersWhereClause() {

        $custom_where = '';

        $query_params = array(
            'user_id' => array(
                'dbvar' => 'usergroups_user.id',
                'type' => 'simple',
            ),
            'play_id' => array(
                'dbvar' => 'ae_game_play.id',
                'type' => 'simple',
            ),
            'created_at' => array(
                'dbvar' => 'usergroups_user.creation_date',
                'type' => 'range',
            ),
        );

        foreach ($query_params as $param => $var_options) {
            if ( !isset($_GET['query'][$param]) OR empty($_GET['query'][$param]) ) {
                continue;
            }

            $db_param = $var_options['dbvar'];
            $type = $var_options['type'];

            $get_param = $_GET['query'][$param];

            if ( $type == 'simple' ) {
                $custom_where .= " AND {$db_param} LIKE :{$param}";
                $this->args = array_merge($this->args, array(
                    ":{$param}" => "%{$get_param}%",
                ));
            } else {
                $ranges = explode(' - ', $get_param);

                if ( isset($ranges[1]) ) {
                    $custom_where .= " AND {$db_param} BETWEEN :range_start AND :range_end";
                    $this->args = array_merge($this->args, array(
                        ":range_start" => "{$ranges[0]}",
                        ":range_end" => "{$ranges[1]}",
                    ));
                }

            }

        }

        return $custom_where;
    }

    public function getGeneralCount() {

        $count_sql = "SELECT COUNT(*) as total
                FROM (
                    SELECT ae_game_play.id
                    FROM ae_game_play
                    INNER JOIN usergroups_user ON usergroups_user.id = ae_game_play.user_id
                    INNER JOIN ae_game_play_variable ON ae_game_play.id = ae_game_play_variable.play_id
                    WHERE
                        ( ae_game_play_variable.play_id <> 0 OR ae_game_play_variable.play_id <> 0 OR ae_game_play_variable.play_id != 0 ) AND
                        ( usergroups_user.play_id <> 0 OR usergroups_user.play_id <> 0 OR usergroups_user.play_id != 0 )
                    $this->and
                    $this->custom_users_where
                    GROUP BY ae_game_play.id
                ) subset";

        $hash = $this->SQLToHash( $count_sql );

        $queryModel = new DbadminQueries();
        $queryModel->query_hash = $hash;
        $result = $queryModel->getCachedTotalCount();

        if ( $result ) {
            $this->total_count = $result;
            return true;
        }

        $count_command = $this->connection->createCommand( $count_sql );

        if ( !empty($this->args) ) {
            $count_command->bindValues( $this->args );
        }

        $this->total_count = $count_command->queryScalar();

        $queryModel->query_total = $this->total_count;
        $queryModel->storeTotalCount();

        return true;
    }

    public function getInlineCount() {

        // Strip the query first
        $hash = $this->getCleanSQL();

        $queryModel = new DbadminQueries();
        $queryModel->query_hash = $hash;
        $result = $queryModel->getCachedTotalCount();

        if ( $result ) {

            // If there is a valid result
            // Avoid using SQL_CALC_FOUND_ROWS unnecessary
            $this->main_sql = str_replace(' SQL_CALC_FOUND_ROWS ', ' ', $this->main_sql);

            $this->total_count = $result;

            return true;
        }

        $this->store_query_after_execuation = true;
    }

    public function storeInlineCount() {
        
        $this->total_count = $this->connection
                    ->createCommand('SELECT FOUND_ROWS()')
                    ->queryScalar();

        $hash = $this->getCleanSQL();

        $queryModel = new DbadminQueries();
        $queryModel->query_hash = $hash;
        $queryModel->query_total = $this->total_count;
        $queryModel->storeTotalCount();

        return true;
    }

    public function getCleanSQL() {
        $sql = implode( 'LIMIT', explode('LIMIT', $this->main_sql, -1) );
        $sql = str_replace(' SQL_CALC_FOUND_ROWS ', ' ', $sql);
        return $this->SQLToHash( $sql );
    }

    public function SQLToHash( $sql ) {
        $to_hash = $sql . '{args: '. implode(',', $this->args) .'}';
        return md5( $to_hash );
    }

    public function getQueryParams() {

        $data = Helper::getVariablesConfig();

        if ( empty($data) ) {
            return false;
        }

        $filter_vars = array();

        foreach ($data as $key => $value) {

            if ( !isset($value['match']) ) {
                continue;
            }

            $filter_vars[$key] = $value['match'];
        }

        return $filter_vars;
    }

    public function queryHasParameters() {

        if ( !isset($_GET['query']) ) {
            return false;
        }

        $query = $_GET['query'];

        foreach ($query as $key => $value) {
            if ( $value )
                return true;
        }

        return false;
    }

}