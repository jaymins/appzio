<?php

/*

    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done

*/

Yii::import('userGroups.*');

class Profile extends ActivationEngineAction {

    public $fields;
    public $userinfo;
    public $playtask;
    public $errors = '';
    public $loginurl;
    public $login = false;
    public $userid;

	public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    public function render(){

        if ( isset($_GET['fblogin']) && $_GET['fblogin'] == 1 ) {
            $this->getFBAccess();
        }

        $this->init();

		 ///colors
		$this->setColors();

        $this->playtask = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));

        $this->userid = $this->playtask->user_id;

        $this->userinfo = UserGroupsUseradmin::model()->findByPk($this->userid);
        $this->userinfo->setScenario('profile');

        $this->loginurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->playtask->id .'&token=' .$this->token .'&login=true';

        $this->userinfo->loginpath = $this->loginurl;

        $this->output = '';

        $msg = ( isset($this->configdata->msg) ? $this->configdata->msg : '' );
		

        $form = $this->regForm();

        /*  render the form using mustache.  */
        $path = '../modules/aelogic/packages/actionProfile/templates/';

        $this->output .= Yii::app()->mustache->GetRender($path .'form', array('form' => $form,'msg' => $msg,'error' => $this->errors,'login'=>$this->login));		
		
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
    		$this->output .='<br />';
    		$this->output .= $this->skipBtn();
		}

        if (Yii::app()->user->isGuest) {

            $fb = $this->getFBObject();
            $helper = $fb->getRedirectLoginHelper();

            $base_url = Yii::app()->getBaseUrl(true);
            $request_url = Yii::app()->request->requestUri;
            $redirect_path = $base_url . $request_url . '&amp;fblogin=1';

            $permissions = array( 'email' ); // Optional permissions
            $loginUrl = $helper->getLoginUrl($redirect_path, $permissions);

            $social_login_btns = array(
                'facebook_login' => 'Facebook Login',
                'twitter_login' => 'Twitter Login',
            );

            $this->output .= '<div class="social-login-buttons">';

            foreach ($social_login_btns as $btn => $label) {
                if ( !isset($this->configdata->$btn) ) {
                    continue;
                }

                if ( $this->configdata->$btn != 1 ) {
                    continue;
                }

                $this->output .= '<a class="'. $btn .'" href="'. $loginUrl .'">'. $label .'</a>';
            }

            $this->output .= '</div>';

        }
		
        return $this->output;
    }
    

    /* this will do login for the user */
    private function handleLogin(){

        $model = new UserGroupsUser('login');

        /* making sure user is active, this is really a rather dirty hack that needs to be sanitized at some point */

/*       $loginuser = UserGroupsUseradmin::model()->findByAttributes(array('username' => $_POST['UserGroupsUseradmin']['username']));

        if(!is_object($loginuser)){
            $this->errors = '{%user_not_found%}';
            return false;
        }

        $loginuser->status = 4;
        $loginuser->update();*/


        if($this->gamedata->cookie_lifetime){
            $cookie_lifetime = $this->gamedata->cookie_lifetime;
        } else {
            $cookie_lifetime = 0;
        }

        $olduser = $this->userid;
        $playid = $this->playid;

        $model->username = $_POST['UserGroupsUseradmin']['username'];
        $model->password = $_POST['UserGroupsUseradmin']['password'];

        if($login = $model->login('regular',false,$cookie_lifetime)) {
            // if user doesn't have this game set, we will transferr it from the account
            // that started playing
            $playobj = @Aeplay::model()->findByAttributes(array('game_id' => $this->gid, 'user_id' => $model->id));

            if(!is_object($playobj)){
                $playbj2 = @Aeplay::model()->findByAttributes(array('game_id' => $this->gid, 'user_id' => $olduser));
                if(is_object($playbj2)){
                    $playbj2->user_id = $model->id;
                    $playbj2->update();
                }
            }

            $this->playGame();

        } else {

            if(isset($model->errors[0]) AND stristr($model->errors[0],'Account not active')){
                $login = $model->login('regular',false,$cookie_lifetime);
            } elseif(isset($model->errors)) {
                $this->errors = serialize($model->errors);
                return false;
            }

            if($login) {
                // if user doesn't have this game set, we will transferr it from the account
                // that started playing
                $playobj = @Aeplay::model()->findByAttributes(array('game_id' => $this->gid, 'user_id' => $model->id));

                if(!is_object($playobj)){
                    $playbj2 = @Aeplay::model()->findByAttributes(array('game_id' => $this->gid, 'user_id' => $olduser));
                    if(is_object($playbj2)){
                        $playbj2->user_id = $model->id;
                        $playbj2->update();
                    }
                }

                $this->playGame();
            } else {
                $this->errors = '{%login_error%}';
            }
        }
    }

    private function saveData(){

        /* todo: add user activation email
        */

        if(isset($_REQUEST['UserGroupsUseradmin']['context']) AND $_REQUEST['UserGroupsUseradmin']['context'] == 'login'){
            if($this->handleLogin()){
                return true;
            } else {
                return false;
            }
        }

        $user = UserGroupsUser::model()->findByPk($this->userid);
        $user->scenario = 'quickregistration';

        if(isset($_POST['UserGroupsUseradmin']['new_username'])){
            $this->userinfo->username = $_POST['UserGroupsUseradmin']['new_username'];
            $this->userinfo->email = $_POST['UserGroupsUseradmin']['new_username'];
        }

        if(isset($_POST['UserGroupsUseradmin']['phone'])){
            $this->userinfo->phone = $_POST['UserGroupsUseradmin']['phone'];
        }

        if(isset($_POST['UserGroupsUseradmin']['timezone'])){
            $this->userinfo->timezone = $_POST['UserGroupsUseradmin']['timezone'];
        }

/*        $this->userinfo->status = UserGroupsUser::WAITING_ACTIVATION;
        $this->userinfo->activation_code = uniqid();
        $this->userinfo->activation_time = date('Y-m-d H:i:s');*/
/*        $mail = new UGMail($this->userinfo, UGMail::ACTIVATION);
        $mail->send();*/


        if($this->userinfo->validate()){
            if(isset($_POST['UserGroupsUseradmin']['new_password'])
                AND isset($_POST['UserGroupsUseradmin']['password_confirm'])
                AND $_POST['UserGroupsUseradmin']['new_password'] == $_POST['UserGroupsUseradmin']['password_confirm']
                AND strlen($_POST['UserGroupsUseradmin']['new_password']) > 3
            ){
                $this->userinfo->setNewPassword($_POST['UserGroupsUseradmin']['new_password']);
                $this->userinfo->status = 4;
            }

            $this->userinfo->update();
        }

        if(isset($_POST['UserGroupsUseradmin']['variable1'])){ $this->saveVariableFromPost('variable1'); }
        if(isset($_POST['UserGroupsUseradmin']['variable2'])){ $this->saveVariableFromPost('variable2'); }
        if(isset($_POST['UserGroupsUseradmin']['variable3'])){ $this->saveVariableFromPost('variable3'); }
        if(isset($_POST['UserGroupsUseradmin']['variable4'])){ $this->saveVariableFromPost('variable4'); }
        if(isset($_POST['UserGroupsUseradmin']['variable5'])){ $this->saveVariableFromPost('variable5'); }
        if(isset($_POST['UserGroupsUseradmin']['variable6'])){ $this->saveVariableFromPost('variable6'); }

        Controller::createUserChannels($this->userinfo->id,$this->userinfo->email,$this->userinfo->phone);
        Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl .'done?tid=' .$this->taskdata->id .'&token=' .$this->token);
        Yii::app()->end();
    }

    private function saveVariableFromPost($name){

        $ft = $name .'_fieldtype';

        if(is_array($_POST['UserGroupsUseradmin'][$name]) AND $this->configdata->$ft!='file'){
            $values = implode(',',$_POST['UserGroupsUseradmin'][$name]);
        } elseif(isset($_FILES['UserGroupsUseradmin']) AND $this->configdata->$ft=='file') {
            $values = $_FILES['UserGroupsUseradmin'];
        } elseif(isset($_POST['UserGroupsUseradmin'][$name])){
            $values = $_POST['UserGroupsUseradmin'][$name];
        } else {
            return true;
        }

        if ($this->configdata->$ft=='file' AND isset($_FILES['UserGroupsUseradmin']['tmp_name'][$name]) AND strlen($_FILES['UserGroupsUseradmin']['tmp_name'][$name]) > 5) {
            $this->saveVariable($this->configdata->$name,$values, false, 'file', $name);
        } elseif($this->configdata->$ft!='file') {
            $this->saveVariable($this->configdata->$name,$values, false, '', $name);
        }

    }


    private function loginForm(){
        $formfields = array();

        if(isset($_GET['UserGroupsUseradmin']['username'])){
            $this->userinfo->username = $_GET['UserGroupsUseradmin']['username'];
        } else {
            $this->userinfo->username = '';
        }

        $formfields['elements']['username'] = array('type' => 'text','maxlength'=>255);
        $formfields['elements']['password'] = array('type' => 'password','maxlength'=>255);
        $formfields['elements']['context'] = array('type' => 'hidden','value'=>'login');

        $formfields['buttons'] = array('save-role' => array('type' => 'htmlSubmit','label' => '{%login%}','htmlOptions'=>array('class'=>'btn btn-success', 'style' => $this->getBtnColor(false))));

        return $formfields;

    }


    private function getFormFields(){

        $formfields = array();
        $formfields['elements']['dummy'] = array('type' => 'hidden','value'=>true);

        if (Yii::app()->user->isGuest) {
            $this->login = true;
            return $this->loginForm();
        }

        if(isset($this->configdata->profile_email) AND $this->configdata->profile_email == 1
            AND $this->userinfo->status == 1 AND !stristr($this->userinfo->username,'@')
        ){
            $formfields['elements']['new_username'] = array('type' => 'text','maxlength'=>255);
        } elseif(isset($this->configdata->profile_email) AND $this->configdata->profile_email == 1){
            $formfields['elements']['username'] = array('type' => 'uneditable','maxlength'=>255);
        }

        /* requesting login form or password change */


        if(isset($_REQUEST['pwchange']) AND isset($this->configdata->profile_password) AND $this->configdata->profile_password == 1){
            $url = '?ptid=' .$this->ptid .'&token=' .$this->token;
            if(isset($_REQUEST['branchid'])){
                $url .= '&branchid=' .$_REQUEST['branchid'];
            }
            $formfields['elements']['username']['hint'] = '<a href="' .$url .'">‹‹ {%cancel%}</a>';
            $formfields['elements']['new_password'] = array('type' => 'password','maxlength'=>255);
            $formfields['elements']['password_confirm'] = array('type' => 'password','maxlength'=>255);
        } elseif(isset($this->configdata->profile_password) AND $this->configdata->profile_password == 1 AND $this->userinfo->status == 1 ){
            $formfields['elements']['new_password'] = array('type' => 'password','maxlength'=>255);
            $formfields['elements']['password_confirm'] = array('type' => 'password','maxlength'=>255);
        } elseif(isset($this->configdata->profile_password) AND $this->configdata->profile_password == 1) {
            $url = '?ptid=' .$this->ptid .'&token=' .$this->token .'&pwchange=1';
            if(isset($_REQUEST['branchid'])){
                $url .= '&branchid=' .$_REQUEST['branchid'];
            }
            $formfields['elements']['username'] = array('type' => 'uneditable','maxlength'=>255, 'hint' => '<a href="' .$url .'">{%click_to_change_pw%}</a>');
        }

        /* when changing password, we are not showing these fields at all */
        if(!isset($_REQUEST['pwchange'])){
                if(isset($this->configdata->profile_phone) AND $this->configdata->profile_phone == 1){
                    $formfields['elements']['phone'] = array('type' => 'text','maxlength'=>255);
                }

            if(isset($this->configdata->profile_timezone) AND $this->configdata->profile_timezone == 1){
                $timezones = CHtml::listData(Controller::getTimeZones_forforms(), 'gmtOffset','name');
                $formfields['elements']['timezone'] = array('type' => 'dropdownlist','items'=>$timezones);
            }

            $formfields = $this->GetVariableField($formfields,'variable1');
            $formfields = $this->GetVariableField($formfields,'variable2');
            $formfields = $this->GetVariableField($formfields,'variable3');
            $formfields = $this->GetVariableField($formfields,'variable4');
            $formfields = $this->GetVariableField($formfields,'variable5');
            $formfields = $this->GetVariableField($formfields,'variable6');
        }

        // this will load values from the request if they have been set
        // ie. if we show error, we don't loose the values user has already entered
        $this->formReload();

        // this will update the variable values that might have been set
        $this->userinfo->update();

        return $formfields;
    }



    private function formReload(){

        $fields = array('new_username', 'new_password', 'password_confirm', 'timezone', 'phone', 'variable1',
            'variable2','variable3','variable4','variable5','variable6');

        while($field = each($fields)){
            $field =  $field[1];
            if(isset($_POST['UserGroupsUseradmin'][$field])){
                $this->userinfo->$field = $_POST['UserGroupsUseradmin'][$field];
            }
        }

    }

    private function GetVariableField($formfields,$name){
        $title = $name .'_title';
        $list = $name .'_list';
        $type = $name .'_fieldtype';

        if(isset($this->configdata->$name) AND is_numeric($this->configdata->$name) AND isset($this->configdata->$title)
            AND strlen($this->configdata->$type) > 1)
            {

            $this->userinfo->$title = $this->configdata->$title;
            if(isset($this->configdata->$list)) {$items = Controller::valueExplode(',', $this->configdata->$list); } else { $items = array(); }
            $formfields['elements'][$name] = array('type' => $this->configdata->$type,'items'=>$items);

            $var = @AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->$name, 'play_id' => $this->playid));

                if(is_object($var) AND isset($this->configdata->$list) AND stristr($this->configdata->$list,',')){
                    $this->userinfo->$name = explode(',',$var->value);
                } elseif(is_object($var) AND $this->configdata->$type != 'file') {
                    $this->userinfo->$name = $var->value;
                } elseif(is_object($var) AND $var->value) {
                    if(stristr($var->value,'.png') OR stristr($var->value,'.jpg')){
                        $formfields['elements'][$name]['hint'] = '<img width="100" src="/img/200/200/?context=user&crop=round&render=image&name=' .$var->value .'">';
                    }
                }
        }

        return $formfields;
    }


    public function addField(){
        return $this->userinfo->username;
    }

    public function startForm(){
        return 'form';

    }

    public function regForm(){

        $fields = $this->getFormFields();

        /* quickregistration is modded method */
        $model = $this->userinfo;

        /* ajax validation for the registration fields */
        if(isset($_POST['ajax']))
        {
            echo Yii::app()->i18n->replaceContent(CActiveForm::validate($model));
            Yii::app()->end();
        }

        if(isset($_POST['UserGroupsUseradmin'])){
            $this->saveData();
        }

        if(!isset($fields['buttons'])){
            $fields['buttons'] = array('save-role' => array('type' => 'htmlSubmit','label' => $this->btntext,'htmlOptions'=>array('class'=>'btn btn-success', 'style' => $this->getBtnColor(false))));
        }
		
		
		
        $formfields = array('elements' => array(
            'gid' => array('type' => 'hidden', 'value' => $this->gameid)
        ));

        $params = Controller::formParameters(false,false,'gameform');
        $params['htmlOptions']['class'] = 'profile';

        $form = TbForm::createForm($fields+$formfields,$model,$params);
        $form->render();

        return $form;
    }


    
		
   public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success"  style="background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';">{%skip_action%}</a>';
		return $output;
    }


    public function getFBObject() {
        // Load Facebook
        require_once( Yii::app()->basePath . '/modules/aelogic/packages/actionProfile/facebook-php-sdk-v4/src/Facebook/autoload.php' );

        return new Facebook\Facebook([
            'app_id' => '856855607738177',
            'app_secret' => '1d2d3189c47bf83516189d38ebbd5048',
            'default_graph_version' => 'v2.2',
        ]);
    }


    public function getFBAccess() {
        
        $fb = $this->getFBObject();
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }

            exit;
        }

        // Logged in
        echo '<h3>Access Token</h3>';
        var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($config['app_id']);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            var_dump($accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');
    }


}