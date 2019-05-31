<?php

/**
 * Default Data model for the action. Extends the frameworks's CActiveRecord model.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\Models;

use CActiveRecord;

class DataModel extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ae_game_play_variable';
    }

    public function getcoutries() {

        if ( !isset($_REQUEST['user_input']) ) {
            return false;
        }

        $input = $_REQUEST['user_input'];

        $call = 'https://restcountries.eu/rest/v2/name/' . urlencode($input);
        $contents = @file_get_contents( $call );

        if ( empty($contents) ) {
            return false;
        }

        $data = @json_decode( $contents, true );

        if ( empty($data) ) {
            return false;
        }

        $coutries = [];

        foreach ($data as $country) {
            $coutries[] = [
                'value' => isset($country['name']) ? $country['name'] : 'N/A',
                'icon' => isset($country['flag']) ? $country['flag'] : '',
            ];
        }
        
        return $coutries;
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

        $action_id = $_REQUEST['actionid'];
        $actionobj = \Aeaction::model()
            ->with('aebranch', 'aetype')
            ->findByPk($action_id);

        if ( empty($actionobj) OR
            (!isset($actionobj->aebranch->game_id) OR empty($actionobj->aebranch->game_id))
        ) {
            return false;
        }

        $common_tags = $this->getCommonTags( $actionobj->aebranch->game_id );
        $user_tags = $this->getUserTags( $actionobj->aebranch->game_id );

        $tags = array_unique(array_merge($common_tags, $user_tags));

        if ( empty($tags) ) {
            return false;
        }

        $search = $_REQUEST['user_input'];
        $sorted = [];

        foreach ($tags as $userTag) {
            if (stripos($userTag, $search) !== false) {
                $sorted[]['value'] = $userTag;
            }
        }

        return $sorted;
    }

    public function getalltags() {

        $required = ['user_input'];

        foreach ($required as $item) {
            if ( !isset($_REQUEST[$item]) ) {
                return false;
            }
        }

        $tags = ItemTagModel::model()->findAll();

        if ( empty($tags) ) {
            return false;
        }

        $search = $_REQUEST['user_input'];
        $sorted = [];

        foreach ($tags as $userTag) {
            if (stripos($userTag->name, $search) !== false) {
                $sorted[]['value'] = $userTag->name;
            }
        }

        return $sorted;
    }

    private function getCommonTags( $app_id ) {
        $vars = \AegameKeyvaluestorage::model()->findByAttributes(array(
            'game_id' => $app_id,
            'key' => 'common_tags'
        ));

        if ( empty($vars) OR empty($vars->value) ) {
            return [];
        }

        return explode(' ', $vars->value);
    }

    private function getUserTags( $app_id ) {
        $play_id = $this->getPlayID();

        if ( empty($play_id) ) {
            return [];
        }

        $user_vars = \AeplayVariable::fetchWithName($play_id, 'user_tags', $app_id);

        if ( empty($user_vars) ) {
            return [];
        }

        if ( $data = @json_decode( $user_vars, 'true' ) ) {
            return $data;
        }

        return [];
    }

    private function getPlayID() {

        if ( !isset($_REQUEST['api_key']) ) {
            return false;
        }

        $token = $_REQUEST['api_key'];

        $token_data = \Aeaccesstokens::model()->findByAttributes([
            'token' => $token
        ]);

        if ( empty($token_data) OR empty($token_data->play_id) ) {
            return false;
        }
        
        return $token_data->play_id;
    }

    public function getemails() {

        $required = [
            'actionid', 'user_input',
        ];

        foreach ($required as $item) {
            if ( !isset($_REQUEST[$item]) ) {
                return false;
            }
        }

        $action_id = $_REQUEST['actionid'];
        $actionobj = \Aeaction::model()
            ->with('aebranch', 'aetype')
            ->findByPk($action_id);

        if ( empty($actionobj) OR
            (!isset($actionobj->aebranch->game_id) OR empty($actionobj->aebranch->game_id))
        ) {
            return false;
        }

        $temp_emails = $this->getUserEmails( $actionobj->aebranch->game_id );

        if ( empty($temp_emails) ) {
            return false;
        }

        $search = $_REQUEST['user_input'];
        $sorted = [];

        foreach ($temp_emails as $user_email) {
            if (stripos($user_email, $search) !== false) {
                $sorted[]['value'] = $user_email;
            }
        }

        return $sorted;
    }

    private function getUserEmails( $app_id ) {
        $play_id = $this->getPlayID();

        if ( empty($play_id) ) {
            return [];
        }

        $emails = \AeplayVariable::fetchWithName($play_id, 'temp_emails', $app_id);

        if ( empty($emails) ) {
            return [];
        }

        if ( $data = @json_decode( $emails, 'true' ) ) {
            return $data;
        }

        return [];
    }

}