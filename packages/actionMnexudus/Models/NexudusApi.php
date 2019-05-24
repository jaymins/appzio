<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

use function GuzzleHttp\Psr7\str;

Trait NexudusApi
{

    public $guzzle;

    /* if this is set, the call will be set to cache & fulfilled from cache
    if ttl is ok. NOTE: the caching functions themselves will set this to
    false after fulfilling from cache / saving to cache */
    public $cache_name;
    public $cache_ttl = 3360;

    /* either global or user. Difference being that global is saved to
    appcaching and user is saved to session */
    public $cache_context = 'global';

    /**
     * Updates an existing booking. NOTE: Nexudus prevents updating a booking which
     * has already been paid.
     * @param $payload
     * @return mixed
     * @url http://help.spaces.nexudus.com/en/api/public/bookings.html#POST-/en/bookings/bookingJson
     */
    public function ApiEditBooking($payload){
        return $this->curlJsonCall("/en/bookings/bookingJson", $payload, 'POST', 'user');
    }

    /**
     * Public version of the coworker info
     * @return mixed
     * @url http://help.spaces.nexudus.com/en/api/public/profile.html#GET-/en/profile?_resource=Coworker
     */
    public function ApiGetUserInfo(){
        return $this->curlJsonCall("en/profile?_resource=Coworker", [], 'GET', 'user');
    }

    /**
     * @return mixed
     * @url http://help.spaces.nexudus.com/en/api/sys/business.html
     */
    public function ApiGetBusinesses(){
        $this->cache_name = 'Businesses';
        $original_endpoint = $this->api_endpoint;
        $this->api_endpoint = 'https://spaces.nexudus.com/';
        $output = $this->curlJsonCall("/api/sys/businesses", [], 'GET', 'admin');
        $this->api_endpoint = $original_endpoint;

        if(isset($output['Records'])){
            return $output['Records'];
        }

        return array();
    }

    /**
     * @param $id - Coworker ID
     * @return mixed
     * @url http://help.spaces.nexudus.com/en/api/spaces/coworker.html#Get-single
     */
    public function ApiGetCoWorker($id){
        $original_endpoint = $this->api_endpoint;
        $this->api_endpoint = 'https://spaces.nexudus.com/';
        $output = $this->curlJsonCall("/api/spaces/coworkers/".$id, [], 'GET', 'admin');
        $this->api_endpoint = $original_endpoint;
        return $output;
    }


    /**
     * @param $id - Coworker ID
     * @param $payload - Needs to include several fields. Best to get ApiGetCoWorker info as a base when updating values.
     * @return mixed
     * @url http://help.spaces.nexudus.com/en/api/spaces/coworker.html#Update
     */
    public function ApiSetCoWorker($id, $payload){
        $original_endpoint = $this->api_endpoint;
        $this->api_endpoint = 'https://spaces.nexudus.com/';
        $payload['Id'] = $id;
        $output = $this->curlJsonCall("/api/spaces/coworkers", $payload, 'PUT', 'admin');
        $this->api_endpoint = $original_endpoint;
        return $output;
    }

    /**
     * This is a curious end-point, requires urlencode for the email and sending it as both query parameter and payload (doh!)
     * @param $email
     * @return bool
     * @url http://help.spaces.nexudus.com/en/api/public/user.html#POST-/api/sys/users/resetPassword?email=:email
     */
    public function ApiResetPass($email)
    {

        $this->api_endpoint = 'https://spaces.nexudus.com/';
        $email = urlencode($email);
        $ret = $this->curlJsonCall("api/sys/users/resetPassword?email=".$email, ['email' => $email], 'POST', 'none');
        return true;

        // api/sys/users/resetPassword?email=:email
    }

    public function ApiGetBookings($start = false, $end = false)
    {

        if (!$start) {
            $start = date('Y-m-d');
        }

        if (!$end) {
            $end = date('Y-m-d', strtotime('next year'));
        }

        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('next year'));

        $payload['start'] = $start;
        $payload['end'] = $end;
        $payload['showAll'] = true;

        $md5 = md5(serialize($payload));
        $this->cache_name = 'bookings'.$md5;

        return $this->curlJsonCall("en/bookings/fullCalendarBookings", $payload, 'POST', 'admin');
    }

    public function ApiGetMyBookings($start = false, $end = false)
    {

        if (!$start) {
            $start = date('Y-m-d');
        }

        if (!$end) {
            $end = date('Y-m-d', strtotime('next year'));
        }

        $payload['start'] = $start;
        $payload['end'] = $end;
        $payload['showAll'] = false;

        return $this->curlJsonCall("en/bookings/fullCalendarBookings", $payload, 'POST', 'user');
    }

    public function ApiGetBooking($id)
    {
        $response = $this->curlJsonCall("en/bookings/booking/" . $id, [], 'GET', 'user');

        if (isset($response['Booking'])) {
            return $response['Booking'];
        }

        return array();
    }

    public function ApiDeleteBooking($id)
    {
        $payload['id'] = $id;
        $this->curlJsonCall("en/bookings/deleteJson", $payload, 'POST', 'user');
        return true;
    }

    public function ApiGetUserToken($password = false)
    {
        if (!$password) {
            $password = $this->getClearTextPass($this->password);
        }

        $return = $this->curlJsonCall("api/sys/users/token/", [
            'grant_type' => 'password',
            'email' => $this->email,
            'password' => $password,
            'validityInMinutes' => '30']);

        if (isset($return['Value'])) {
            return $return['Value'];
        }

        return false;
    }

    public function ApiGetSpaces()
    {

        $this->cache_name = 'Resources2';

        $spaces = $this->curlJsonCall("api/spaces/resources?size=999", false, 'GET', 'admin');

        if (isset($spaces['Records'])) {
            return $spaces['Records'];
        }

        return array();
    }

    public function ApiGetLocations()
    {
        $this->cache_name = 'Businesses2';

        $spaces = $this->curlJsonCall("en?_resource=Businesses ", false, 'GET', 'admin');

        if (is_array($spaces)) {
            return $spaces;
        }

        return array();
    }


    /* todo: proper time filtering */
    public function ApiGetTimeSlots($resource, $from = false, $to = false)
    {

        // 2012-10-08T23:13:38Z

        if ($from) {

        }

        $timeslots = $this->curlJsonCall("api/spaces/resourcetimeslots?ResourceTimeSlot_Id=$resource&size=999", false, 'GET', 'admin');

        if (isset($timeslots['Records'])) {
            return $timeslots['Records'];
        }

        return array();

    }

    public function ApiGetUserId($email)
    {
        $users = $this->ApiGetCoworkers();

        if (is_array($users) AND !empty($users)) {
            foreach ($users as $user) {
                if (isset($user['Email']) AND $user['Email'] == $email) {
                    return $user['Id'];
                }
            }
        }

        return false;
    }


    public function ApiValidateNexudusEmail($email)
    {
        $users = $this->ApiGetCoworkers();

        if (is_array($users) AND !empty($users)) {
            foreach ($users as $user) {
                if (isset($user['Email']) AND $user['Email'] == $email) {
                    return false;
                }
            }

            return true;
        }

        $this->errors[] = 'Could not fetch a list of users';

        return false;
    }

    public function ApiCreateUser()
    {
        $base = $this->returnApiCreateUserJson();
        $base = json_decode($base);

        $this->firstname = ucfirst($this->firstname);
        $this->lastname = ucfirst($this->lastname);
        $this->email = strtolower($this->email);

        $payload = new \StdClass;
        $payload->Coworker = $base;
        $payload->Coworker->FullName = $this->firstname . ' ' . $this->lastname;
        $payload->Coworker->GuessedFirstName = $this->firstname;
        $payload->Coworker->GuessedLastName = $this->lastname;
        $payload->Coworker->GuessedFirstNameForInvoice = $this->firstname;
        $payload->Coworker->GuessedLastNameForInvoice = $this->lastname;
        $payload->Coworker->Email = $this->email;
        $payload->Coworker->Active = true;
        $payload->Coworker->MobilePhone = $this->phone;
        $payload->Coworker->Password = $this->password;
        $payload->Coworker->PasswordConfirm = $this->password;
        $payload->Coworker->GeneralTermsAcceptedOnline = true;
        $payload->Coworker->ApiCreateUser = true;
        $payload->Coworker->BillingEmail = $this->email;
        $payload->Coworker->BillingName = $this->firstname . ' ' . $this->lastname;
        $payload->Coworker->Salutation = $this->firstname;
        $payload->Coworker->SendWelcomeEmail = true;
        $payload->Coworker->tariffguid = '9656ea7e-9975-444a-8884-3b543dc1a09c';
        $payload->Coworker->startdate = date('m-d-Y');
        $payload->base64avatar = null;

        $create = $this->curlJsonCall('en/signup', $payload, 'POST', 'noauth');

        if (isset($create['RedirectTo'])) {
            return true;
        } else {
            return false;
        }
    }


    public function ApiGetBusiness()
    {
        $token = $this->getAdminToken();
        return $this->curlJsonCall("api/business", $token, false, false, true);
    }

    public function ApiActivateAccount($id)
    {
        $payload['Ids'] = [$id];
        $payload['Key'] = 'COWORKER_SENDWELCOME';
        return $this->curlJsonCall('api/spaces/coworkers/runcommand', $payload, 'POST', 'admin');

    }

    /* todo: implement paging */
    public function ApiGetCoworkers()
    {
        $users = $this->curlJsonCall("api/spaces/coworkers?size=999", false, 'GET', 'admin');

        if (isset($users['Records'])) {
            return $users['Records'];
        }

        return array();
    }

    /* todo: implement paging */
    public function ApiGetUsers()
    {
        $users = $this->curlJsonCall("api/sys/users?size=999", false, 'GET', 'admin');

        if (isset($users['Records'])) {
            return $users['Records'];
        }

        return array();
    }

    /**
     * NOTE: this needs to be coworker id, not userid
     * @param $coworkerid
     * @return mixed
     */
    public function ApiDeleteUser($coworkerid)
    {
        $payload['Ids'] = [$coworkerid];
        $payload['Key'] = 'COWORKER_DELETE';
        return $this->curlJsonCall('api/spaces/coworkers/runcommand', $payload, 'POST', 'admin');
    }

    public function createBooking($from, $to, $id)
    {
        $payload = new \stdClass();
        $payload->Booking = new \stdClass();

        $payload->Booking->Resource = new \stdClass();
        $payload->Booking->Resource->Id = $id;
        $payload->Booking->Id = 0;
        $payload->Booking->FromTime = $from;
        $payload->Booking->ToTime = $to;
        $payload->Booking->BookingVisitors = array();
        $payload->Booking->Products = array();

        return $this->curlJsonCall('en/bookings/newbookingJson', $payload, 'POST', 'user');

    }


}
