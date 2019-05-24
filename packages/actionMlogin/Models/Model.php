<?php

namespace packages\actionMlogin\Models;

use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel
{

    public function doLogin($username, $password)
    {
        if (!$username OR !$password OR strlen($username) < 3 OR strlen($password) < 3) {
            return false;
        }

        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value FROM ae_game_play_variable AS tbl1
                LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id
                LEFT JOIN ae_game_variable AS vartable2 ON tbl2.variable_id = vartable2.id

                WHERE tbl1.`value` = :user
                AND tbl2.`value` = :pass
                AND vartable1.game_id = :gid
                AND vartable2.game_id = :gid

                ORDER BY tbl1.play_id DESC
                ";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':user' => $username,
                ':pass' => $password,
                ':gid' => $this->appid
            ))
            ->queryAll();

        foreach ($rows as $row) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */

            if (isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete') {
                $play = $row['play_id'];

            }
        }

        if (isset($play)) {
            $this->switchPlay($play);
            return $play;
        }

        return false;
    }

    public function switchPlay($to_playid, $finish_login = true, $extravars = array())
    {

        \Aeplay::updateOwnership($to_playid, $this->userid);

        /* update ownership */
        $usr = \UserGroupsUseradmin::model()->findByPk($this->userid);
        $original_play_id = $usr->play_id;
        $usr->play_id = $to_playid;
        $usr->update();

        $original_vars = \AeplayVariable::getArrayOfPlayvariables($original_play_id);
        $copyvars = array('system_push_id',
            'user_language', 'system_source', 'onesignal_deviceid',
            'system_push_plattform', 'perm_push', 'lat', 'lon', 'device',
            'screen_height', 'screen_width');
        $newvars = $extravars;

        foreach ($copyvars as $var) {
            if (isset($original_vars[$var]) AND $original_vars[$var]) {
                $newvars[$var] = $original_vars[$var];
            }
        }

        $newvars['logged_in'] = 0;

        foreach ($newvars as $key => $val) {
            \AeplayVariable::updateWithName($to_playid, $key, $val, $this->appid);
        }

        /* delete the old play if its considered "temporary", ie. the registration is not complete */
        if ($original_play_id AND $to_playid AND $original_play_id != $to_playid) {
            if (!isset($original_vars['reg_phase']) OR $original_vars['reg_phase'] != 'complete') {
                \Aeplay::model()->deleteByPk($original_play_id);
            }
        }

        if ($finish_login) {
            \AeplayVariable::updateWithName($to_playid, 'logged_in', '1', $this->appid);
            \AeplayBranch::closeBranch($this->actionobj->levelid, $to_playid);
        }
    }

    public function validatePassword($password = '', $confirmPassword = '')
    {

        $error = false;

        if($password != $confirmPassword){
            $error = '{#passwords_dont_match#}';
        } elseif(strlen($password) < 4){
            $error = '{#password_should_be_at_least_four_characters_long#}';
        }

        $this->validation_errors['confirm_password'] = $error;

        return $error;
    }

    public function findUserWithoutPassword($email = false)
    {
        $email = $email ? $email : $this->getSavedVariable('email');

        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value FROM ae_game_play_variable AS tbl1
                LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id
                LEFT JOIN ae_game_variable AS vartable2 ON tbl2.variable_id = vartable2.id

                WHERE tbl1.`value` = :email
                AND tbl2.`value` = 'complete'
                AND vartable1.game_id = :gid
                AND vartable2.game_id = :gid
                GROUP BY tbl1.play_id
                ORDER BY tbl1.play_id DESC
                ";


        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':email' => $email,
                ':gid' => $this->appid
            ))
            ->queryAll();

        return $rows[0]['play_id'];

    }

    public function loginWithFacebook($thirdpartyid)
    {

        $pointer = 'email';

        if (empty($thirdpartyid) OR empty($this->appid)) {
            return false;
        }

        $rows = $this->getThirdParty($pointer, $thirdpartyid);

        foreach ($rows as $row) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */

            if (isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete') {
                $play = $row['play_id'];

                if ($this->playid != $play) {
                    $this->switchPlay($play, false);
                }

                $this->playid = $play;
                return $play;
            }
        }

        return false;
    }

    public function getThirdParty($pointer, $thirdPartyID)
    {
        $sql = "SELECT ae_game_play_variable.id, ae_game_play_variable.value, ae_game_variable.id, ae_game_variable.name, ae_game_play_variable.play_id FROM ae_game_play_variable
                LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id

                WHERE `value` = '$thirdPartyID'
                AND ae_game_variable.`name` = '$pointer'
                AND ae_game_variable.game_id = $this->appid
                
                ORDER BY play_id ASC
                ";

        $rows = \Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array())
            ->queryAll();

        return $rows;
    }

    public function loginWithInstagram($thirdpartyid)
    {
        $pointer = 'instagram_username';

        if ($thirdpartyid OR !$this->appid) {
            return false;
        }

        $rows = $this->getThirdParty($pointer, $thirdpartyid);
        foreach ($rows as $row) {
            $vars = \AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */

            if (isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete') {
                $play = $row['play_id'];

                $token = \AeplayVariable::fetchWithName($this->playid, 'instagram_temp_token', $this->appid);
                if ($this->playid != $play) {
                    $this->switchPlay($play, false);
                }

                \AeplayVariable::updateWithName($play, 'instagram_token', $token, $this->appid);

                $this->playid = $play;
                return $play;
            }
        }

        return false;
    }

}