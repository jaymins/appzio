<?php

namespace packages\actionMofficernd\Models;
use PHPUnit\Framework\TestCase;

/* make sure to manually include the traits,
for test classes there is no autoloading */

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'OfficeRNDApiHelpers.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'OfficeRNDApi.php');


/**
 * PHPUnit test class for the api. Can be run independently from command line.
 * Refer to documentation of PHPUnit.
 *
 *
 * Class OfficeRNDApiTest
 * @package packages\actionMofficernd\Models
 */

class OfficeRNDApiTest extends TestCase
{

    public $api;

    public static $test_user = 'branimir.parashkevov@appzio.com';
    public static $test_pass = 'testtest';

    public function __construct()
    {
        parent::__construct();
        $this->api = new OfficeRNDApi();

    }

    public function testApiConnectSuccess(){
        $connect = $this->api->loginUser(self::$test_user, self::$test_pass);
        $this->assertArrayHasKey('token', $connect);
    }

    public function testApiConnectIncorrectCredentials(){
        $connect = $this->api->loginUser(self::$test_user. 'test', self::$test_pass.'test');
        $this->assertArraySubset(['type' => 'Authentication required'], $connect);

    }

    public function testGetResources(){
        $result = $this->api->getResources();
        // assuming message means there is some error
        $this->assertArrayNotHasKey('message',$result);
        $this->assertNotEmpty($result);
    }

    //TODO
    /*
    public function testApiLogout(){
        $logout = $this->api->logoutUser();

        $this->assertTrue();
    }
    */
}