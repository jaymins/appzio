<?php

/**
 * Default Data model for the action. Extends the frameworks's CActiveRecord model.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfood\Models;

use CActiveRecord;

class DataModel extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ae_game_play_variable';
    }

    public function gettags() {

        $required = [
            'actionid', 'user_input',
        ];

        foreach ($required as $item) {
            if ( !isset($_REQUEST[$item]) ) {
                return false;
            }
        }

        $search = $_REQUEST['user_input'];
        $sorted = [];


        $criteria = new \CDbCriteria();
        $criteria->condition = "`name` COLLATE utf8_general_ci LIKE '%".$search."%'";
        $criteria->order = 'name';

        $results = IngredientModel::model()->findAll($criteria);

        foreach ($results as $result) {
            $sorted[]['value'] = $result->name;
        }

        return $sorted;
    }


}