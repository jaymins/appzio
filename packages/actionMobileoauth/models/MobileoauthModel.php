<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

class MobileoauthModel extends ArticleModel {

    public $gid;
    public $playid;

    public $foreign_vars;
    public $copy_vars_initial = array('name','real_name','user_language','email','profile_comment','hcp','home_club','gender','country','zip','city','phone','availability','logged_in','registration_status','fb_token');
    public $copy_vars_onlogin = array('name','real_name','user_language','email','profile_comment','hcp','home_club','gender','country','zip','city','phone','availability','logged_in','registration_status','fb_token');

    public $vars;



    /* the actual adding of the code is handled by the api (its a redirect from the oauth provider)

    http://users.singtao.ca/oauth/token
usage: Http Post with following parameters:
client_id: the assigned client ID
client_secret: the assigned client secret
grant_type: must be 'authorization_code'
code: the authorization code returned from the above Authorization api
result: as Json object. if success, the access token will be returned

    */

    public function setAuthToken(){

        $url = $this->getConfigParam('oauth_url');

        if(!isset($this->varcontent['oauth_code'])){
            return false;
        }

        /* if we have the token already, we'll check its validity */
        if($this->getSavedVariable('oauth_token')){
            $info = $this->getOauthUserInfo($this->getSavedVariable('oauth_token'));
            if(isset($info['Id'])){
                return $info['Id'];
            }
        }

        /* get the authentication token */
        $data['client_id'] = $this->getConfigParam('oauth_client_id');
        $data['client_secret'] = $this->getConfigParam('oauth_secret');
        $data['scope'] = $this->getConfigParam('oauth_scope');
        $data['code'] = $this->varcontent['oauth_code'];
        $data['redirect_uri'] = $this->getSavedVariable('oauth_return_url');
        $data['grant_type'] = 'authorization_code';

        /* get the access token */
        $token = ThirdpartyServices::curlFormCall($url,$data);

        $token = json_decode($token,true);
        if(!isset($token['access_token'])){
            return false;
        }

        $token = $token['access_token'];
        AeplayVariable::updateWithName($this->playid,'oauth_token',$token,$this->gid);

        return $this->getOauthUserInfo($token);

    }

    public function getOauthUserInfo($token){

        /* get the info */
        $info = ThirdpartyServices::curlBearerCall($this->getConfigParam('oauth_userinfo_url'),$token);
        AeplayVariable::updateWithName($this->playid,'oauth_raw_info',$info,$this->gid);
        $info = json_decode($info,true);

        if(isset($info['Id'])){
            return $info['Id'];
        }

        return false;

    }


    /* this is called by the api to enrich the url with the proper code */
    public static function setOauthAuthenticationUrl($code,$playid,$gid){

        $url = Appcaching::getGlobalCache('oauth-authurl-'.$playid);
        $saveurl = $url.$code;

        AeplayVariable::updateWithName($playid,'oauth_code',$saveurl,$gid);
        Appcaching::setGlobalCache('oauth-loginurl-'.$playid,$saveurl);
    }



    public function doAppzioUpdate($token){
        $this->foreign_vars = ThirdpartyServices::getAppzioVars($token,$this->getConfigParam('endpoint'));

        /* we need to have the variables availble and registration has to be complete
        some specialty cases to keep in mind: same user has initiated new registration, user has logged out
        */

        //print_r($this->foreign_vars);die();

        if(isset($this->foreign_vars['variables']) AND isset($this->foreign_vars['variables']['reg_phase']) AND $this->foreign_vars['variables']['reg_phase'] == 'complete'
            AND isset($this->foreign_vars['variables']['logged_in']) AND $this->foreign_vars['variables']['logged_in'] == '1'
        ){
            // determine if its the first login
            if($this->getSavedVariable('oauth_connected')){
                $this->copyVars($this->copy_vars_initial);
                return true;
            } else {
                $this->copyVars($this->copy_vars_onlogin);
                return true;
            }
        } else {
            return false;
        }
    }


    public function copyVars($varlist){
        foreach ($varlist AS $var){
            if(isset($this->foreign_vars['variables'][$var])){
                $outvars[$var] = $this->foreign_vars['variables'][$var];
            }
        }

        if(isset($this->foreign_vars['gid'])){
            $outvars['faux_gid'] = $this->foreign_vars['gid'];
        }

        if(isset($this->foreign_vars['play_id'])){
            $outvars['faux_pid'] = $this->foreign_vars['play_id'];
        }

        if(isset($outvars)){
            AeplayVariable::saveVariablesArray($outvars,$this->playid,$this->gid);
        }

        if(isset($this->foreign_vars['variables']['profilepic'])){
            $profilepic = $this->foreign_vars['variables']['profilepic'];

            if($this->getSavedVariable('profilepic') != $profilepic){
                $file = file_get_contents($this->getConfigParam('endpoint').$profilepic);
                $directorypath = Controller::getDocumentsFolder($this->gid) .'/instagram/';
                $dir = '/documents' .DIRECTORY_SEPARATOR .'games' .DIRECTORY_SEPARATOR .$this->gid .'/instagram/';

                if(!is_dir($directorypath)){
                    mkdir($directorypath,0777,true);
                }

                $newpath = $dir .basename($profilepic);
                file_put_contents($directorypath.basename($profilepic),$file);
                AeplayVariable::updateWithName($this->playid,'profilepic',$newpath,$this->gid);
            }
        }

    }


}