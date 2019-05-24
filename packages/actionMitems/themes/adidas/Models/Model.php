<?php

namespace packages\actionMitems\themes\adidas\Models;

use packages\actionMitems\Models\ItemCategoryModel;
use packages\actionMitems\Models\ItemCategoryRelationModel;
use packages\actionMitems\Models\ItemTagRelationModel;
use packages\actionMitems\Models\Model as BootstrapModel;
use packages\actionMitems\Models\ItemModel;
use packages\actionMvenues\Models\VenuesModel;

class Model extends BootstrapModel
{

    public $key = '565ec012251f932ea4000001971ada892da3402a7d9bf84e5e27cc50';

    public function getPlaces()
    {
        $places = VenuesModel::model()->findAllByAttributes(array('playid' => $this->playid));

        $output = '';

        foreach($places as $place){
            $output .= $place->id .';' .$place->name .';';
        }

        if($output){
            $output = substr($output,0,-1);
        }

        return $output;
    }

    public function getFootballMatches() {

        // Get competitions
        // http://api.football-api.com/2.0/competitions?Authorization= . $this->key
        // 1229 == Bundesliga

        $league = '1229';
        $from = date('d.m.Y', (time() - 43200));
        $time = time() + 86400 * 30;
        $to = date('d.m.Y',$time);

        $matches = \Appcaching::getGlobalCache('football_matches');

        if(isset($matches['time'])){
            if($matches['time'] + 900 < time()){
                return $matches['data'];
            }
        }

        $api_call = 'http://api.football-api.com/2.0/matches?comp_id='. $league .'&from_date='. $from .'&to_date='. $to .'&Authorization=' . $this->key;
        $data = @file_get_contents( $api_call );

        if ( empty($data) ) {
            return false;
        }

        $output = @json_decode($data, true);
        $cache['time'] = time();
        $cache['data'] = $output;

        return $output;
    }

    public function getMatchByID( $match_id ) {

        $api_call = 'http://api.football-api.com/2.0/matches/'. $match_id .'?Authorization=' . $this->key;

        $data = @file_get_contents( $api_call );

        // To do: implement caching here

        if ( empty($data) ) {
            return false;
        }

        return @json_decode($data, true);
    }

    public function validateEvent() {

        $is_valid = true;
        $submitted_data = $this->getAllSubmittedVariablesByName();

        if ( !isset($submitted_data['event_short_description']) OR empty($submitted_data['event_short_description']) ) {
            $this->validation_errors['missing_description'] = '{#please_enter_a_description#}';
            $is_valid = false;
        }

        if ( !$is_valid ) {
            return false;
        }

        return true;
    }

    public function saveMatchEvent() {

        $submitted_data = $this->getAllSubmittedVariablesByName();

        $selected_match = $this->sessionGet('selected_match');
        $match_info = $this->getMatchByID($selected_match);
        $extra_info = $match_info ? json_encode($match_info) : '';

        $event_data = [
            'play_id' => $this->playid,
            'game_id' => $this->appid,
            'status' => 'active',
            'type' => 'match-event',
            'extra_data' => $extra_info,
            'date_added' => time(),
            'place_id' => $submitted_data['place_id'],
            'lat' => $this->getSavedVariable('lat'),
            'lon' => $this->getSavedVariable('lon'),
            'category_id' => null,
            'name' => $submitted_data['match_event_name'],
            'description' => $submitted_data['event_short_description'],
            'time' => '1',
        ];

        $index = 0;
        $images = array();
        while ($index <= 10) {
            $image = $this->getSubmittedVariableByName('event_pic_' . $index);

            if ($image) {
                $images[] = $image;
            }

            $index ++;
        }

        if ( $images ) {
            $event_data['images'] = $images;
        }

        try {
            $item = ItemModel::store($event_data);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


}