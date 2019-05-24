<?php

namespace packages\actionMnexudus\Models;
use PHPUnit\Framework\TestCase;

/* make sure to manually include the traits,
for test classes there is no autoloading */

require_once('NexudusApi.php');
require_once('NexudusApiHelpers.php');

class NexudusTest extends TestCase
{

    use NexudusApi;
    use NexudusApiHelpers;

    public $userid;
    public $spaces;

    private $testdata = array(
        'email' => 't.railo@futurist-labs.com',
        'password' => 'GJLwavdNGXMD::00726a75964ea0e74dad31942db287de',
        'validityInMinutes' => 5,
        'start_date' => '2018-07-01',
        'end_date' => '2018-09-01',
        );

    public $email = 'timorailo7@gmail.com';
    public $password = '91jd1kAsdjas';
    public $cleartextpassword = '91jd1kAsdjas';
    public $firstname = 'Tester7';
    public $lastname = 'FuturistUser7';

    public function testApiGetSpaces(){
        $spaces = $this->ApiGetSpaces();
        $this->spaces = $spaces;
        $this->assertArrayHasKey(0, $spaces);
        $userlist = $this->ApiGetCoworkers();
        $this->assertArrayHasKey(0, $userlist);
    }

    public function skiptestApiCreateUser(){
        $user = $this->ApiCreateUser();
        $this->assertTrue($user,'user creation');

        $userlist = $this->ApiGetCoworkers();
        $this->assertArrayHasKey(0, $userlist);

        $userid = false;

        if(is_array($userlist)){
            foreach($userlist as $record){
                if($record['Email'] == $this->email){
                    $userid = $record['Id'];
                }
            }
        }

        $this->assertNotFalse($userid);
        $activate = $this->ApiActivateAccount($userid);
        $this->assertArrayHasKey('Status', $activate);

        $this->assertTrue($this->checkForSuccess($activate),'User created successfully');
        $this->userid = $userid;

/*        $token = $this->ApiGetUserToken();
        $this->assertTrue(!empty($token));

        $userlist = $this->ApiGetUsers();
        print_r($userlist);*/

/*        $userlist = $this->ApiGetUsers();
        $this->assertArrayHasKey('Records', $userlist);
        print_r($userlist['Records'][0]);*/

/*        echo('errors:');
        print_r($this->errors);
        echo('log:'.chr(10));
        print_r($this->log);
        echo(chr(10));*/
    }

    public function testCreateBooking(){
        $date = date('Y-m-d');
        $start = $date .'T16:00Z';
        $end = $date .'T16:30Z';

        $spaces = $this->ApiGetSpaces();
        $booking = $this->createBooking($start, $end, $spaces[0]['Id']);

        $bookings = $this->ApiGetBookings();
        print_r($bookings);

        echo('errors:');
        print_r($this->errors);
        echo('log:'.chr(10));
        print_r($this->log);
        echo(chr(10));

        $this->assertTrue(true,'Booking creation');

        //$this->assertTrue($this->checkForSuccess($booking),'Booking creation');

    }


/*    public function testApiDeleteUser(){
        $delete = $this->ApiDeleteUser($this->userid);
        $this->assertTrue($this->checkForSuccess($delete),'User deleted successfully');
    }*/



}