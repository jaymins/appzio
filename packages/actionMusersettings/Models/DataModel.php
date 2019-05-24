<?php

/**
 * Default Data model for the action. Extends the frameworks's CActiveRecord model.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMusersettings\Models;

use CActiveRecord;

class DataModel extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ae_game_play_variable';
    }

    public function getstates() {

        if ( !isset($_REQUEST['user_input']) ) {
            return false;
        }

        $input = $_REQUEST['user_input'];

        $states = 'Alabama, Alaska, Arkansas, California, Colorado, Connecticut, Florida, Georgia, Hawaii, Illinois,
            Iowa, Kansas, Kentucky, Louisiana, Maine, Massachusetts, Michigan, Minnesota, Mississippi, Missouri,
            Montana, Nebraska, Nevada, New Hampshire, New Jersey, New Mexico, New York, North Carolina, North Dakota,
            Ohio, Oklahoma, Oregon, Pennsylvania, Rhode Island, South Carolina, South Dakota, Tennessee, Texas, Utah,
            Vermont, Virginia, Washington, West Virginia, Wisconsin, Wyoming';

        $states_array = explode(',', $states);

        $states_sorted = [];

        foreach ($states_array as $state) {
            if (stripos($state, $input) !== false) {
                $states_sorted[]['value'] = trim($state);
            }
        }
        
        return $states_sorted;
    }

}