<?php

namespace packages\actionMswipematch\Models;

use Yii;


Trait MatchingQueries
{

    protected function getWhereQuery2($type = 'exclude', $exclude_skipped = false, $viewname = 'ae_ext_mobilematching')
    {
        $criteria = '';

        $exclude1 = $this->obj_datastorage->get('matches');

        if ($exclude_skipped) {
            $exclude2 = array();
        } else {
            $exclude2 = $this->obj_datastorage->get('un-matches');
        }

        $unmatches = $this->obj_datastorage->get('un-matches-auto');

        if($this->exclude_bookmarked){
            $bookmarked = $this->obj_datastorage->get('bookmark');
            $exclude3 = array_merge($unmatches,$bookmarked);
        } else {
            $exclude3 = $unmatches;
        }


        // Very temporary solution
        switch ($type) {
            case 'olive':
                $criteria = self::getWhereQuery2('exclude');
                //$part2 = " AND `value` = '" .$this->uservars['interests'] ."'";
                //$criteria = $part1 .chr(10) .$part2;
                break;

            case 'exclude':
                $exclude = array_merge($exclude1, $exclude2, $exclude3);

                if (isset($exclude) AND is_array($exclude) AND !empty($exclude)) {
                    $excludeids = '';

                    foreach ($exclude AS $key => $value) {
                        if ($value) {
                            $excludeids .= "'" . $value . "',";
                        }
                    }

                    if (!empty($excludeids)) {
                        $excludeids = (string)$excludeids;
                        $excludeids = substr($excludeids, 0, -1);
                        $criteria = "AND $viewname.play_id NOT IN ($excludeids)";
                    }
                }
                break;

            case 'include':

                $args = array(
                    'play_id' => $this->playid_thisuser,
                    'key' => 'requested_match',
                );

                $storage = new \AeplayKeyvaluestorage();
                $matches = $storage->findAllByAttributes($args);

                if (empty($matches)) {
                    return array();
                }

                $criteria = '';
                $ids = '';

                foreach ($matches as $match) {
                    $ids .= "'" . $match->value . "',";
                }

                if (!empty($ids)) {
                    $ids = (string)$ids;
                    $ids = substr($ids, 0, -1);
                    $criteria = "AND play_id IN ($ids)";
                }

                break;

            case 'requestors':
                $criteria = "AND match_always = 1";
                break;

            case 'acceptors':
                $criteria = "AND match_always = 0";
                break;
        }


        $criteria .= " AND (hide_my_profile IS NULL OR hide_my_profile = '0')";

        return $criteria;
    }

    /*
     * use this for testing if needed:
    set @orig_lat='-26.20410280';
    set @orig_lon='28.04730510';
    set @bounding_distance=360;
    SELECT * ,( 3959 * 1.609344 * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) * sin( radians(`lat`)))) AS `distance` FROM `ae_ext_mobilematching` WHERE ( `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance) AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance) ) AND play_id <> :playID AND game_id = :gameId ORDER BY `distance` ASC limit 50;*/
    public function getUsersNearby($config)
    {

        $distance = $this->getParam('distance', $config, 150);
        $type = $this->getParam('type', $config, 'exclude');
        $sex_depended = $this->getParam('sex_depended', $config, false);
        $match_opposing_roles = $this->getParam('match_opposing_roles', $config, false);
        $sorting = $this->getParam('sorting', $config, false);
        $units = $this->getParam('units', $config, 'km');

        if (empty($this->uservars)) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if (!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $where_clause = $this->getWhereQuery2($type);

        if (empty($where_clause) AND $type == 'include') {
            return array();
        }

        $query_by_sex = $this->getQueryBySex($vars, $sex_depended);
//        $query_by_role = $this->getQueryByRole($vars,$match_opposing_roles);
        $sorting = $this->getSorting($sorting);

        $lat = $vars['lat'];
        $lon = $vars['lon'];


        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT
            *,
            ( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
                * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
                * sin( radians(`lat`)))
            ) AS `distance`
            $this->extra_selects
            FROM `ae_ext_mobilematching`
            $this->extra_join_query
            
            WHERE
            (
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            )

            AND ae_ext_mobilematching.play_id <> :playId
            AND ae_ext_mobilematching.game_id = :gameId
            $query_by_sex
            $where_clause
            
            
            GROUP BY ae_ext_mobilematching.play_id
            HAVING distance <= $distance OR distance IS NULL

            ORDER BY $sorting ASC 
            limit 1000
        ";


        $this->debug = $sql;

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid_thisuser,
            ':gameId' => $this->appid,
            ':unit' => ($units == 'km' ? 1.609344 : 1),
        ))
            ->queryAll();

        return $rows;
    }

    public function getUsersByIDs($user_ids, $units = 'km')
    {

        if (empty($user_ids)) {
            return false;
        }

        if (empty($this->uservars)) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($this->playid_thisuser);
        } else {
            $vars = $this->uservars;
        }

        if (!isset($vars['lat']) OR !isset($vars['lon'])) {
            return false;
        }

        $user_ids_array = implode(',', $user_ids);

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT
            *,( 3959 * :unit * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
               * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
               * sin( radians(`lat`)))) AS `distance`
          
            FROM `ae_ext_mobilematching`
            
            WHERE
            (
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            )
  
            AND ae_ext_mobilematching.play_id IN ($user_ids_array)
            AND ae_ext_mobilematching.play_id <> :playId
            AND ae_ext_mobilematching.game_id = :gameId
            
            GROUP BY ae_ext_mobilematching.play_id

            ORDER BY FIELD(ae_ext_mobilematching.play_id, $user_ids_array)
            limit 1000
        ";

        $this->debug = $sql;

        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid_thisuser,
            ':gameId' => $this->appid,
            ':unit' => ($units == 'km' ? 1.609344 : 1),
        ))
            ->queryAll();

        return $rows;
    }

    public function getSorting($sorting)
    {

        if ($sorting == 'boosted') {
            return '`is_boosted` DESC, `distance`';
        } else if ($sorting == 'active-and-boosted') {
            return '`last_update` DESC, `is_boosted` DESC, `distance`';
        }

        return '`distance`';
    }

    public function getQueryBySex($vars, $sex_depended)
    {
        $query_by_sex = false;

        if ($sex_depended AND isset($vars['gender'])) {
            if ($vars['gender'] == 'man' OR $vars['gender'] == 'male') {
                $sex = 'woman';
                $secondsex = 'female';
            } else {
                $sex = 'man';
                $secondsex = 'male';
            }

            $query_by_sex = "AND (gender = '$sex' OR gender = '$secondsex') ";
        }

        return $query_by_sex;
    }

    public function getQueryByRole($vars, $match_opposing_roles)
    {
        if ($match_opposing_roles AND isset($vars['role'])) {
            if ($vars['role'] == 'influencer') {
                $srch = 'brand';
            } else {
                $srch = 'influencer';
            }

            /* match always is inluced because approving users changes this value */
            $query_by_role = "AND role = '$srch' AND match_always = 1";
        } else {
            $query_by_role = false;
        }

        return $query_by_role;
    }

    public function joinForBookmarks()
    {
        $viewname = 'matching_' . $this->appid;
        return "LEFT JOIN ae_game_play_keyvaluestorage AS bookmarks ON $viewname.`play_id` = bookmarks.`value` AND `key` = 'bookmark' AND bookmarks.play_id = " . $this->playid;
    }

    public function selectForBookmarks()
    {
        return "bookmarks.id as `bookmark`";
    }

    public function getUsersNearbyV2($params = array())
    {

        $type = isset($params['type']) ? $params['type'] : 'exclude';
        $sorting = isset($params['sorting']) ? $params['sorting'] : 'boosted';
        $no_distance = isset($params['no_distance']) ? $params['no_distance'] : false;
        $no_filtering = isset($params['no_filtering']) ? $params['no_filtering'] : false;
        $extra_selects = isset($params['extra_selects']) ? $params['extra_selects'] : '';
        $extra_joins = isset($params['extra_joins']) ? $params['extra_joins'] : '';
        $distance = isset($params['distance']) ? $params['distance'] : 10000;
        $limit = isset($params['limit']) ? $params['limit'] : 50;
        $distance_range = isset($params['distance_range']) ? $params['distance_range'] : false;

        if(!$distance){
            $distance = 10000;
        }

        if(!$limit){
            $limit = 50;
        }

        if ($this->getSavedVariable('units')) {
            $units = $this->getSavedVariable('units');
        } else {
            $units = isset($params['units']) ? $params['units'] : 'km';
        }

        if ($units == 'km') {
            $units = '1.609344';
        } else {
            $units = 1;
        }

        $viewname = 'matching_' . $this->appid;
        $where_clause = $this->getWhereQuery2($type, false, $viewname);

        if (empty($where_clause) AND $type == 'include') {
            return array();
        }

        $sorting = $this->getSorting($sorting);

        $lat = $this->getSavedVariable('lat');
        $lon = $this->getSavedVariable('lon');

        if (!$lat OR !$lon) {
            return array();
        }

        if(isset($distance_range['min']) AND isset($distance_range['max'])){
            $min = $distance_range['min'];
            $max = $distance_range['max'];

            $distance_where_query = "(
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            ) AND ";
            $having_query = "HAVING distance > $min AND distance < $max";
        }elseif (!$no_distance) {
            $distance_where_query = "(
              `lat` BETWEEN (@orig_lat - @bounding_distance) AND (@orig_lat + @bounding_distance)
              AND `lon` BETWEEN (@orig_long - @bounding_distance) AND (@orig_long + @bounding_distance)
            ) AND ";
            $having_query = "HAVING distance <= $distance OR distance IS NULL OR distance = 0";
        } else {
            $distance_where_query = '';
            $having_query = '';
        }

        $distance_query = ",
            ( 3959 * $units * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) ) 
                * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat)) 
                * sin( radians(`lat`)))
            ) AS `distance`";


        if (!$no_filtering) {
            $filters = $this->getFilterQuery();
        } else {
            $filters = '';
        }

        if ($extra_selects) {
            if (substr($extra_selects, 0, 1) != ',') {
                $extra_selects = ',' . $extra_selects;
            }
        }

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT
            *,
            $viewname.play_id as play_id
            $distance_query
            $extra_selects
            FROM $viewname
            $extra_joins
            WHERE
            $distance_where_query
            $viewname.play_id <> $this->playid_thisuser
            AND $viewname.game_id = $this->appid
            AND $viewname.lat <> '0.00000000'
            $filters
            $where_clause
            GROUP BY $viewname.play_id
            $having_query
            ORDER BY $sorting ASC 
            limit $limit
        ";



        $this->debug = $sql;

/*                echo("set @orig_lat=$lat;").'<br>';
                echo("set @orig_long=$lon;").'<br>';
                echo("set @bounding_distance=360;").'<br>';

                echo($sql);
                echo('<pre>');
                echo($this->playid_thisuser);
                echo('<br>');
                echo($this->appid);
                die();*/


        $rows = Yii::app()->db->createCommand($sql)->bindValues(array(
            ':playId' => $this->playid_thisuser,
            ':gameId' => $this->appid,
        ))->queryAll();

        if (!$rows) {
            return array();
        }

        return $rows;
    }

    public function getFilterQuery()
    {

        $sql = '';


        if ($this->getSavedVariable('men') AND $this->getSavedVariable('men') == 1 AND $this->getSavedVariable('women') AND $this->getSavedVariable('women') == 1) {

        } elseif ($this->getSavedVariable('men') AND $this->getSavedVariable('men') == 1) {
            $sql .= " AND gender = 'man'";
        } elseif ($this->getSavedVariable('women') AND $this->getSavedVariable('women') == 1) {
            $sql .= " AND gender = 'woman'";
        }

        $preferences = array(
            'relationship_status',
            'seeking',
            'religion',
            'diet',
            'tobacco',
            'alcohol',
            'zodiac_sign'
        );

        foreach ($preferences as $preference) {
            $pref = 'preference_' . $preference;
            $status = $this->getSavedVariable($pref) ? $this->getSavedVariable($pref) : null;

            if (empty($status)) {
                continue;
            }

            $test = json_decode($status, true);

            if (is_array($test)) {
                $p = '';

                foreach ($test as $stat) {
                    $p .= " $preference = '$stat' OR ";
                }

                if ($p) {
                    $p = substr($p, 0, -3);
                }

                $sql .= " AND ($p)";

            } else {
                $sql .= " AND $preference = $status ";
            }

        }

        if ($this->getSavedVariable('filter_age_start') AND $this->getSavedVariable('filter_age_end')) {
            $start = $this->getSavedVariable('filter_age_start');
            $end = $this->getSavedVariable('filter_age_end');
            $sql .= " AND ((age > $start AND age < $end) OR age IS NULL) ";
        }

        return $sql;
    }

}