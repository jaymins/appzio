<?php

namespace packages\actionMofficernd\Models;


/**
 * API for OfficeRND.
 *
 * todo: Consider using magic methods for different sections of the API, as the api is quite big
 * todo: Keep the test cases in sync when extending the API
 *
 * Class OfficeRNDApi
 * @package packages\actionMofficernd\Models
 */

class OfficeRNDApi
{

    public static $api_endpoint_base = 'https://app.officernd.com/api/v1/';
    public static $organization = 'organizations/appzio-test-account/';

    public static $salt = '19asd!!-k1wasd';
    public static $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjVjMWNjYThlYzdjNzkwMDAxMGMyNGYzZiIsImlhdCI6MTU0NTM5MDczNCwiZXhwIjoxNTc2OTI2NzM0fQ.blebCNHp0Gnkh6WAGmI7VcAqZTw1256e_dzRR5T5ozI';
    public static $logging = true;

    protected $log_path;
    public $log = [];
    public $errors = [];

    use OfficeRNDApiHelpers;

    public function __construct() {
        $this->setLogPath();
    }

    /**
     * LOGGING
     * todo: append to log for one file per user
     */

    public function __destruct()
    {
        if(self::$logging){
            $content = 'Log entries:'.chr(10);
            $content .= implode(chr(10), $this->log);
            $content .= 'Error entries:'.chr(10);
            $content .= implode(chr(10), $this->errors);

            $filename = date('Y-m-d_H-i') .'_officernd.txt';

            file_put_contents($this->log_path . $filename, $content);
        }
    }

    /**
     * Method to set the log path depending on the type of execution - http or cli
     */
    protected function setLogPath(){

        if ( isset($_SERVER['HTTP_HOST']) ) {
            $this->log_path =  $_SERVER['DOCUMENT_ROOT'] .dirname($_SERVER['PHP_SELF']) .'mobile_error_logs/';
        } else {
            $this->log_path = getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;
        }
    }


    /**
     * Simple example call
     * @param string $username
     * @param string $password
     * @return bool|mixed
     */
    public function loginUser(string $username, string $password){
        $payload['username'] = $username;
        $payload['password'] = $password;
        return $this->curlJsonCall('auth/signin', $payload,'POST');
    }

    //TODO need a valid endpoint
    public function logoutUser(){
        return $this->curlCall('auth/logout' );
    }

    public function getResources(){
        return $this->curlJsonCall(self::$organization . 'resources');
    }
}
