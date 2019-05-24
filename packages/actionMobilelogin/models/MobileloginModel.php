<?php

/**
 *
 * Mobile login handles switching of user upon login,
 * finding the correct user and loggin that user in.
*/

class MobileloginModel extends ArticleModel {

    public $userid;
    public $password;
    public $username;
    public $playid;
    public $loginbranchid;
    public $fbid;
    public $fbtoken;
    public $gid;


    public $existing_user;
    public $existing_playid;

    public $thirdpartyid;
    public $debug;


    public function doLogin(){

        $user = strtolower($this->username);

        if(!$user OR !$this->password OR strlen($user) < 3 OR strlen($this->password) < 3){
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

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':user' => $user,
                ':pass' => $this->password,
                ':gid' => $this->gid
            ))
            ->queryAll();

        foreach($rows as $row) {
            $vars = AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */

            if (isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete') {
                $play = $row['play_id'];
            }
        }


        if(isset($play)){
            $this->switchPlay($play);
            return $play;
        }

        return false;

    }

    public function checkTouch($deviceid)
    {

        $sql = "SELECT tbl1.play_id,tbl1.value,tbl2.value,vartable2.name,vartable3.name
	            FROM ae_game_play_variable AS tbl1 
	            LEFT JOIN ae_game_play_variable AS tbl2 ON tbl1.play_id = tbl2.play_id
                LEFT JOIN ae_game_play_variable AS tbl3 ON tbl1.play_id = tbl3.play_id

                LEFT JOIN ae_game_variable AS vartable1 ON tbl1.variable_id = vartable1.id 
                LEFT JOIN ae_game_variable AS vartable2 ON tbl2.variable_id = vartable2.id 
                LEFT JOIN ae_game_variable AS vartable3 ON tbl3.variable_id = vartable3.id 
                    
                WHERE 
                        tbl1.`value` = '$deviceid'
                    AND vartable1.`name` = 'deviceid'
                    AND vartable2.`name` = 'reg_phase'
                    AND tbl2.`value` = 'complete'
                    AND vartable3.`name` = 'touchid'
                    AND tbl3.`value` <> ''
                    AND vartable1.game_id = :gid AND vartable2.game_id = :gid AND vartable3.game_id = :gid GROUP BY tbl1.play_id ORDER BY tbl1.play_id 
                ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':deviceid' => $deviceid,
                ':gid' => $this->gid
            ))
            ->queryAll();


        if(isset($rows[0]['play_id'])){
            return $rows[0]['play_id'];
        }

        return false;

    }


    public function findUser($email=false){

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


        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':email' => $email,
                ':pass' => $this->password,
                ':gid' => $this->gid
            ))
            ->queryAll();

        return $rows[0]['play_id'];

    }

    public function findUserWithoutPassword($email=false){

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


        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':email' => $email,
                ':gid' => $this->gid
            ))
            ->queryAll();

        return $rows[0]['play_id'];

    }



    /*
     *             [username] => viking_99
        [bio] =>
        [website] =>
        [profile_picture] => https://scontent.cdninstagram.com/t51.2885-19/10623741_840213702678508_188281339_a.jpg
        [full_name] =>
        [counts] => stdClass Object
            (
                [media] => 2
                [followed_by] => 78
'instagram_username','instagram_media','instagram_followed_by','instagram_follows','instagram_id','instagram_bio','instagram_website'

     *
     * */



    public function addInstagramInfoToUser($info,$token){

        $userid = $info->data->id;
        $userinfo = json_decode(file_get_contents("https://i.instagram.com/api/v1/users/$userid/info/"));

        if(isset($userinfo->user->hd_profile_pic_url_info->url) AND $userinfo->user->hd_profile_pic_url_info->url){
            $path = Controller::getDocumentsPath($this->gid);
            $img = @file_get_contents($userinfo->user->hd_profile_pic_url_info->url);

            /* simple retry */
            if(!$img){
                $img = @file_get_contents($userinfo->user->hd_profile_pic_url_info->url);
            }

            if(!is_dir($path.'instagram')){
                mkdir($path.'instagram',0777);
            }

            $filename = md5($userinfo->user->hd_profile_pic_url_info->url) .'jpg';

            $filepath = $path . 'instagram/' . $filename;

            if($img){
                file_put_contents($filepath,$img);
            }

            if(file_exists($filepath)){
                AeplayVariable::updateWithName($this->playid,'profilepic',$filename,$this->gid,$this->userid);
                AeplayVariable::updateWithName($this->playid,'instagram_profilepic',$filename,$this->gid,$this->userid);
            }
        }
        
        AeplayVariable::updateWithName($this->playid,'instagram_token',$token,$this->gid,$this->userid);
        AeplayVariable::deleteWithName($this->playid,'instagram_temp_token',$this->gid);
        AeplayVariable::updateWithName($this->playid,'instagram_username',$info->data->username,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'instagram_bio',$info->data->bio,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'instagram_website',$info->data->website,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'real_name',$info->data->full_name,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'instagram_media_count',$info->data->counts->media,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'instagram_followed_by',$info->data->counts->followed_by,$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'instagram_follows',$info->data->counts->follows,$this->gid,$this->userid);
    }


    public function addFbInfoToUser(){
        //UserGroupsUseradmin::addFbInfo($this->userid,$this->fbtoken,$this->gid,$this->playid);
        // !! NOTE: Currently disabling this, in theory it shouldn't effect anything
        // AeplayVariable::updateWithName($this->playid,'fb_universal_login','0',$this->gid,$this->userid);
    }

    public function addOauthInfoToUser(){

        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid);
        if(!isset($vars['oauth_raw_info'])){
            return false;
        }

        $data = json_decode($vars['oauth_raw_info'],true);

        if(!isset($data['Id']) OR !isset($data['Email'])){
            return false;
        }
        
        $output['oauth_userid'] = $data['Id'];
        $output['email'] = $data['Email'];

        AeplayVariable::updateWithName($this->playid,'oauth_userid',$data['Id'],$this->gid,$this->userid);
        AeplayVariable::updateWithName($this->playid,'email',$data['Email'],$this->gid,$this->userid);

        return true;

    }


    public function newPlay(){
        $game = new Aegame;
        $game->gid = $this->gid;
        $game->userid = $this->userid;
        $play = $game->playGame(false,true,true);
        //Datarecipes::createActionsFromRecipe($this->gid,$this->userid,$play,false,'1.7',false);
        $this->switchPlay($play,false);
        return $play;
    }

    public function switchPlay($to_playid,$finish_login = true,$extravars=array()){
        Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');
        Aeplay::updateOwnership($to_playid,$this->userid);

        /* update ownership */
        $usr = UserGroupsUseradmin::model()->findByPk($this->userid);
        $original_play_id = $usr->play_id;
        $usr->play_id = $to_playid;
        $usr->update();

        $original_vars = AeplayVariable::getArrayOfPlayvariables($original_play_id);
        $copyvars = array('system_push_id',
            'user_language','system_source','onesignal_deviceid',
            'system_push_plattform','perm_push','lat','lon','device','deviceid',
            'screen_height','screen_width','touchid','touchid_supported');
        $newvars = $extravars;

        foreach($copyvars as $var){
            if(isset($original_vars[$var]) AND $original_vars[$var]){
                $newvars[$var] = $original_vars[$var];
            }
        }

        $newvars['logged_in'] = 0;

        foreach ($newvars as $key=>$val){
            AeplayVariable::updateWithName($to_playid,$key,$val,$this->gid);
        }

        /* delete the old play if its considered "temporary", ie. the registration is not complete */
        if($original_play_id AND $to_playid AND $original_play_id != $to_playid){
            if(!isset($original_vars['reg_phase']) OR $original_vars['reg_phase'] != 'complete'){
                Aeplay::model()->deleteByPk($original_play_id);
            }
        }

        if($finish_login){
            AeplayVariable::updateWithName($to_playid,'logged_in','1',$this->gid);
            AeplayBranch::closeBranch($this->loginbranchid,$to_playid);
        }
    }



    public function loginWithThirdParty($provider){

        switch($provider){
            case 'facebook':
                $pointer = 'fb_id';
                break;

            case 'instagram':
                $pointer = 'instagram_username';
                $temppointer = 'instagram_temp_token';
                break;

            case 'twitter':
                $pointer = 'twitter_id';
                break;

            case 'google':
                $pointer = 'google_email';
                break;

            case 'oauth':
                $pointer = 'oauth_userid';
                break;

            case 'touchid':
                $pointer = 'touchid';
                break;

            default:
                return false;
                break;
        }

        if(!$this->thirdpartyid OR !$this->gid){
            return false;
        }

        $sql = "SELECT ae_game_play_variable.id, ae_game_play_variable.value, ae_game_variable.id, ae_game_variable.name, ae_game_play_variable.play_id FROM ae_game_play_variable
                LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id

                WHERE `value` = '$this->thirdpartyid'
                AND ae_game_variable.`name` = '$pointer'
                AND ae_game_variable.game_id = $this->gid
                
                ORDER BY play_id DESC
                ";


        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
/*                ':id' => $this->thirdpartyid,
                ':pointer' => $pointer,
                ':gid' => $this->gid*/
            ))
            ->queryAll();


        foreach($rows as $row){
            $vars = AeplayVariable::getArrayOfPlayvariables($row['play_id']);

            /* there might be several registration attempts, so we are looking only
            for the completed registration cases */


            if(isset($vars['reg_phase']) AND $vars['reg_phase'] == 'complete'){
                $play = $row['play_id'];
                switch($provider){
                    case 'instagram':
                        $token = AeplayVariable::fetchWithName($this->playid,'instagram_temp_token',$this->gid);
                        if($this->playid != $play){
                            $this->switchPlay($play,false);
                        }
                        AeplayVariable::updateWithName($play,'instagram_token',$token,$this->gid);
                        break;
                    
                    case 'oauth':
                        $token = AeplayVariable::fetchWithName($this->playid,'oauth_token',$this->gid);
                        $rawinfo = AeplayVariable::fetchWithName($this->playid,'oauth_raw_info',$this->gid);
                        AeplayVariable::updateWithName($play,'oauth_token',$token,$this->gid);
                        AeplayVariable::updateWithName($play,'oauth_raw_info',$rawinfo,$this->gid);
                        if($this->playid != $play){
                            $this->switchPlay($play,false);
                        }
                        break;

                    default:
                        if($this->playid != $play){
                            $this->switchPlay($play,false);
                        }
                        break;
                }

                $this->playid = $play;
                return $play;
            }
        }

        return false;
    }




    /*
     * $fbid,$this->getSavedVariable('fb_token'),$this->userid,$this->playid,$this->getConfigParam('login_branch'))
     * */

/*    public function doFbLogin(){
        $userobj = UserGroupsUseradmin::model()->findByPk($this->existing_user);

        if(is_object($userobj)){
            $userobj->fbtoken_long = $this->fbtoken;
            $userobj->fbid = $this->fbid;
            $userobj->update();

            $this->switchPlay($this->existing_playid);
            return true;
        } else {
            return false;
        }
    }*/


}