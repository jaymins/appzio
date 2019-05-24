<?php

class bonapetiMobileloginSubController extends MobileloginController {

    public $auth_code;
    public $fields = array(
        'access_token', 'token_scope', 'refresh_token',
    );

    public function tab1(){

        $this->data = new StdClass();
        $this->initLoginModel();

        $this->auth_code = $this->getVariable( 'initial_auth_code' );

        if ( empty($this->auth_code) ) {
            $this->auth_code = $this->getAuthCode();
        }

        switch($this->menuid){
            case 'do-remote-login':
                $this->doRemoteLogin();
                break;

            case 'login-form':
                $this->loginForm();
                break;

            default:
                $this->loginForm();
                break;
        }

        return $this->data;
    }

    public function loginForm($error=false){

        $loggedin = $this->getSavedVariable('logged_in');

        if($loggedin == 1){
            $this->logoutForm();
            return true;
        }

        if($this->getConfigParam('only_logout')){
            $this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }


        $this->data->scroll[] = $this->getSpacer( 50 );

        $this->data->scroll[] = $this->getImage( 'bonapeti-logo.png', array( 'style' => 'bonapeti-logo' ) );

        // $this->data->scroll[] = $this->getImage( 'fb-button.png', array( 'priority' => 1, 'margin' => '0 20 5 20', 'onclick' => $fbclick ) );
        
        $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->getVariableId('username'),'{#username#}',$error);
        $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png',$this->getVariableId('password_code'),'{#password#}',false,'password');
        $this->data->scroll[] = $this->getSpacer('5');
        $this->data->scroll[] = $this->getTextbutton('{#login#}',array('style' => 'general_button_style','id' => 'do-remote-login'));

        $content4[] = $this->getTextbutton('{#forgot_password#}',array('id' => 'reset-password-form','style' => 'text_link'));
        $content4[] = $this->getTextbutton('{#signup#}', array('id' => '393933', 'action' => 'open-branch', 'config' => $this->requireConfigParam('register_branch'), 'style' => 'text_link_right'));
        // $this->data->scroll[] = $this->getRow($content4,array('margin' => '20 42 0 42'));
    }

    public function doRemoteLogin(){

        $username_var_id = $this->getVariableId('username');
        $password_var_id = $this->getVariableId('password_code');

        $username = $this->getSubmitVariable( $username_var_id );
        $password = $this->getSubmitVariable( $password_var_id );

        if ( empty($username) OR empty($password) ) {
            $this->loginForm('{#please_fill_all_fields#}');
            return false;
        }

        $token_get = $this->getToken( $username, $password );

        if ( empty($token_get) ) {
            $this->loginForm('{#user_not_found_or_password_wrong#}');
            return false;
        }

        $this->saveVariable('name', $username);
        $this->saveVariable('real_name', $username);

        $this->finishLogin(true, false, __LINE__);

        return true;
    }

    public function getAuthCode() {
        $url = 'http://api.bonapeti.bg/authorize';

        $data = array(
            'response_type' => 'code',
            'client_id' => 'appzio',
            'authorize' => '1',
            'redirect_uri' => 'https://appzio.com/gettoken.php',
            'state' => 'd284a2e4e9c517e9e50525809d38b2d5',
            'scope' => 'http://bonapeti.bg/scope/customer',
        );

        $header = array();

        array_push($header,"POST /authorize HTTP/1.1");
        array_push($header,"Host: api.bonapeti.bg");
        array_push($header,"User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0");
        array_push($header,"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");
        array_push($header,"Accept-Language: en-US,en;q=0.5");
        # array_push($header,"Accept-Encoding: gzip, deflate");
        array_push($header,"Referer: http://api.bonapeti.bg/");
        array_push($header,"Cookie: PHPSESSID=72579ae0515c2c6fc3ed02d2dd2971ed");
        array_push($header,"Connection: keep-alive");
        array_push($header,"Upgrade-Insecure-Requests: 1");

        $process = curl_init();
        curl_setopt($process, CURLOPT_HTTPHEADER,$header);
        curl_setopt($process, CURLOPT_URL,$url);
        curl_setopt($process, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);
        # curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        # curl_setopt($process, CURLOPT_USERPWD, "$login:$password");
        $result = curl_exec($process);
        curl_close($process);

        if ( isset($result) AND !empty($result) ) {
            $result = json_decode($result, true);
            $this->saveVariable( 'initial_auth_code', $result['code'] );

            return $result['code'];
        }

        return false;
    }

    public function getToken( $username, $password ) {
        $data = array(
            'grant_type' => 'password',
            'client_id' => 'appzio',
            'client_secret' => 'appziopass',
            'redirect_uri' => 'https://appzio.com/gettoken.php',
            'code' => $this->auth_code,
            // 'username' => 'dogostz@gmail.com',
            // 'password' => 'bonpass',
            'username' => $username,
            'password' => $password,
            'authorize' => '1',
        );

        $url = 'http://api.bonapeti.bg/token';

        $process = curl_init();
        curl_setopt($process, CURLOPT_URL,$url);
        curl_setopt($process, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);
        # curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        # curl_setopt($process, CURLOPT_USERPWD, "$login:$password");
        $result = curl_exec($process);
        curl_close($process);

        $this->saveAPIResponse( $result );

        if ( $result ) {
            $result = json_decode( $result );

            if ( isset($result->error) ) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function saveAPIResponse( $result ) {

        if ( empty($result) ) {
            return false;
        }

        $result = json_decode($result, true);

        foreach ($this->fields as $field) {
            if ( isset($result[$field]) AND !empty($result[$field]) ) {
                $this->saveVariable( $field, $result[$field] );
            }
        }

        return true;
    }
    
}