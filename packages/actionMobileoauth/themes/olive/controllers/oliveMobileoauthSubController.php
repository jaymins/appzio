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

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

/*

    2-legged oauth process:
    - this controller will make sure we have the oauth key firstly (getAuthToken)
    - api thirdparty will add the oauth code which is needed for the user authnetication flow
    -

*/


class oliveMobileoauthSubController extends MobileoauthController {

    public $data;
    public $configobj;
    public $theme;
    public $fields = array('password1' => 'password1', 'password2' => 'password2');

    /** @var MobileloginModel */
    public $loginmodel;

    /* this has the token from the other app */
    public $foreign_token;

    /** @var MobileoauthModel */
    public $oauthmodel;

    public $strings = array('logging_in' => '{#connecting#}');


    public function tab1(){

        $this->data = new StdClass();

        /* case code is not set, enable action and open it */

        /* case code is set, validate it */
        if($this->getSavedVariable('oauth_code')){
            $this->initOauthModel();

            /* connect with the third party */
            $userid = $this->oauthmodel->setAuthToken();

            if($userid){
                $this->initLoginModel();
                $this->loginmodel->thirdpartyid = $userid;
                $is_user = $this->loginmodel->loginWithThirdParty('oauth');

                if($is_user){
                    $this->playid = $is_user;
                    $this->finishLogin(true,false,__LINE__);
                } else {
                    $this->loginmodel->addOauthInfoToUser();
                    $text = '{#connected_create_profile#}';
                    $this->data->scroll[] = $this->getText($text,array( 'style' => 'register-text-step-2'));

                    $complete = new stdClass();
                    $complete->action = 'open-branch';
                    $complete->action_config = $this->getConfigParam('register_branch');
                    $this->data->onload[] = $complete;
                    return $this->data;
                }
            }
        }

        /* case code is set & valid */


        /* close login branch */


        //$this->setHeader();
        //$this->loginButton();




        return $this->data;

    }


    public function setOauthToken(){
        $url = $this->getConfigParam('oauth_url');
        $client_id = $this->getConfigParam('oauth_client_id');
        $scope = $this->getConfigParam('oauth_scope');
        $secret = $this->getConfigParam('oauth_secret');
        $url_userauth = $this->getConfigParam('oauth_userauth_url');


    }

    public function loginButton(){
        $onclick2 = new StdClass();
        $onclick2->id = 'link';
        $onclick2->action = 'open-action';
        $onclick2->sync_open = 1;
        $onclick2->sync_close = 1;
        $onclick2->open_popup = 1;
        $onclick2->action_config = $this->getConfigParam('oauth_action');

        $this->data->scroll[] = $this->getTextbutton('{#login#}',array('id' => 'login','style' => 'button_imate_red','onclick' => $onclick2));

    }


    public function initOauthModel(){
        $this->oauthmodel = new MobileoauthModel();
        $this->oauthmodel->varcontent = $this->varcontent;
        $this->oauthmodel->playid = $this->playid;
        $this->oauthmodel->gid = $this->gid;
        $this->oauthmodel->configobj = $this->configobj;
    }

    public function handleOauthConnection(){

        $this->data->scroll[] = $this->getFullPageLoader();

        if($this->getSavedVariable('logged_in') != 1){
            $onclick2 = new StdClass();
            $onclick2->id = 'link';
            $onclick2->action = 'open-branch';
            $onclick2->sync_open = 1;
            $onclick2->action_config = $this->getConfigParam('login_branch');
            $string = $this->menuid .'_' .$this->action_id;
            $this->saveVariable('oauth_in_progress',$string);

            $this->data->scroll[] = $this->getSpacer(50);
            $this->data->scroll[] = $this->getText('{#please_login_first_oauth#}',array('text-align' => 'center','font-size' => '13'));
            $this->data->scroll[] = $this->getSpacer(20);
            $this->data->scroll[] = $this->getText('{#login#}',array('onclick' => $onclick2,'style' => 'general_button_style'));

            //$this->data->onload[] = $onclick2;

            $onclick2 = new StdClass();
            $onclick2->action = 'list-branches';
            $this->data->onload[] = $onclick2;

        } elseif($this->menuid) {
            $this->oauthReturn($this->menuid);
        } elseif($this->getSavedVariable('oauth_in_progress')){
            $parts = explode('_',$this->getSavedVariable('oauth_in_progress'));
            if(isset($parts[0])){
                $this->oauthReturn($parts[0]);
            } else {
                $this->data->scroll[] = $this->getText('unknown error');
            }
        }
    }

    public function loginForm($error=false)
    {
        return true;
    }

    public function logoutForm($error=false)
    {
        return true;
    }



}