<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

//use GuzzleHttp\guzzle\Client;

Trait NexudusApiHelpers {


    public static $api_endpoint_custom = 'https://myworkbooth.spaces.nexudus.com/';
    public static $api_endpoint_rest = 'https://spaces.nexudus.com/';

    private $salt = '9d13j!2kdsasd';
    public $api_endpoint = 'https://myworkbooth.spaces.nexudus.com/';
    private $admin_user = 't.railo@futurist-labs.com';
    private $admin_pass = 'GJLwavdNGXMD::00726a75964ea0e74dad31942db287de';
    public $log;


    public function getAdminToken(){
        return $this->getToken($this->admin_user, $this->admin_pass);
    }

    public function getClearTextPass($password){
        if(preg_match("/^(.*)::(.*)$/", $password, $regs)) {
            // decrypt encrypted string
            list(, $crypted_token, $enc_iv) = $regs;
            $enc_method = 'AES-128-CTR';
            $enc_key = openssl_digest($this->salt, 'SHA256', TRUE);
            $pass = openssl_decrypt($crypted_token, $enc_method, $enc_key, 0, hex2bin($enc_iv));
            return $pass;
        }

        return false;
    }

    private function getToken($username,$password){
        $pass = $this->getClearTextPass($password);
        return base64_encode($username .':' .$pass);
    }

    public function encryptPassword($pass){
        $enc_key = openssl_digest($this->salt, 'SHA256', TRUE);
        $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CTR'));
        $pass = openssl_encrypt($pass,'AES-128-CTR',$enc_key,null,$enc_iv). "::" . bin2hex($enc_iv);
        return $pass;
    }

    public function getAuthString(){
        if($this->cleartextpassword){
            $pass = $this->cleartextpassword;
        } else {
            $pass = $this->getClearTextPass($this->password);
        }

        $this->log[] = 'Authentication for user:' .$this->email .':'.$pass;

        return $this->email .':' .$pass;
    }

    public function getAdminAuthString(){
        return $this->admin_user .':' .$this->getClearTextPass($this->admin_pass);
    }

    public function checkForSuccess($data){
        $status = isset($data['Status']) ? $data['Status'] : false;

        if($status == '200'){
            return true;
        } else {
            return false;
        }

    }


    public function getNexudusCache(){

        $cachename = 'nexudus_'.$this->cache_name;

        if($this->cache_context == 'global'){
           $cache = \Appcaching::getGlobalCache($cachename);
        }
        
        if($this->cache_context == 'user'){
            $cache = $this->sessionGet($cachename);
        }

        if(isset($cache['time']) AND $cache['time']+$this->cache_ttl > time()){
            $this->cache_name = false;
            return $cache['data'];
        }
        
        return false;
    }

    public function setNexudusCache($value){

        $cachename = 'nexudus_'.$this->cache_name;
        $content['time'] = time();
        $content['data'] = $value;

        if($this->cache_context == 'global'){
           \Appcaching::setGlobalCache($cachename,$content);
        }

        if($this->cache_context == 'user'){
            $this->sessionSet($cachename,$content);
        }

        $this->cache_name = false;
        return true;

    }


    // caching is handled here also
    function curlJsonCall($url,$data=array(),$method='POST',$context='user'){

        if($this->cache_name){
            $cache = $this->getNexudusCache();
            if($cache){
                return $cache;
            }
        }

        $call = $this->curlCall($url, $data, $method, $context);

        if(is_array($call)){
            $this->setNexudusCache($call);
            return $call;
        }

        sleep(1);

        /* simple retry */
        $call = $this->curlCall($url, $data, $method, $context);

        if(is_array($call)){
            $this->setNexudusCache($call);
            return $call;
        }

        return false;

    }

    private function curlCall($url,$data=array(),$method='POST',$context='user'){
        $ch = curl_init($this->api_endpoint.$url);
        $header = array();

        if($context == 'admin'){
            array_push($header,"Authorization: Basic ".base64_encode($this->getAdminAuthString()));
            curl_setopt($ch, CURLOPT_USERPWD, $this->getAdminAuthString());
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        } elseif($context == 'user') {
            $this->log[] = 'Auth string:'.base64_encode($this->getAuthString());
            array_push($header,"Authorization: Basic ".base64_encode($this->getAuthString()));
            curl_setopt($ch, CURLOPT_USERPWD, $this->getAuthString());
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        array_push($header,"Content-Type: application/json");
        array_push($header,"Accept: application/json");

        $original_data = $data;

        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);

        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $data = curl_exec($ch);

        if(curl_error($ch)){
            $this->errors[] = curl_error($ch);
            return false;
        }

        curl_close($ch);

        $test = json_decode($data,true);

        $this->log[] = 'Url:'.$this->api_endpoint.$url;
        $this->log[] = (string)'Headers:'.json_encode($header) .chr(10);
        $this->log[] = (string)'Post:'.json_encode($original_data) .chr(10);
        $this->log[] = (string)'Response:'.$data .chr(10);
        $this->log[] = (string)'Admin auth:'.$this->getAdminAuthString() .chr(10);


        if(is_array($test)){
            return $test;
        }

        return false;

    }

    public function returnApiCreateUserJson(){
        return '{  
      "FullName":"John Doe",
      "GuessedFirstNameForInvoice":"Nexudus",
      "GuessedLastNameForInvoice":"Limited",
      "GuessedFirstName":"John",
      "GuessedLastName":"Doe",
      "Salutation":"John",
      "Address":null,
      "PostCode":null,
      "CityName":null,
      "State":null,
      "Email":"john@example.com",
      "Active":true,
      "DiscountCode":null,
      "RefererGuid":"",
      "ReferenceNumber":null,
      "IsNew":false,
      "CheckedIn":false,
      "SignUpToNewsletter":true,
      "DeleteAvatar":false,
      "DeleteBanner":false,
      "CountryId":1221,
      "SimpleTimeZoneId":2023,
      "IsMember":true,
      "CancellationDate":null,
      "UtcCancellationDate":null,
      "AbsoluteCancellationDate":null,
      "DoNotProcessInvoicesAutomatically":false,
      "MobilePhone":null,
      "LandLine":"56456",
      "NickName":null,
      "BusinessArea":"Urbanism",
      "Position":"Senior Partner",
      "CompanyName":"Landing Limited",
      "ProfileTags":"business management,surveying,resource acquisition,webdesign,copywriting,finance,editing",
      "ProfileTagsSpaces":"business management surveying resource acquisition webdesign copywriting finance editing",
      "ProfileTagsList":[  
         "business management",
         "surveying",
         "resource acquisition",
         "webdesign",
         "copywriting",
         "finance",
         "editing"
      ],
      "ProfileSummary":"Lorem ipsum dolor sit amet",
      "ProfileWebsite":"http://www.somewebsite.com",
      "Gender":"Male",
      "ProfileIsPublic":false,
      "Twitter":null,
      "Skype":null,
      "Facebook":null,
      "Linkedin":null,
      "Google":null,
      "Telegram":null,
      "Github":null,
      "Pinterest":null,
      "Flickr":null,
      "Instagram":null,
      "Vimeo":null,
      "Tumblr":"https://mytumblr2",
      "Blogger":null,
      "HasContactDetails":true,
      "BillingName":"Nexudus Limited",
      "BillingEmail":"finance@example.com",
      "BillingAddress":"Not Available",
      "BillingPostCode":"Not Available",
      "BillingCityName":"Not Available",
      "BillingState":"Not Available",
      "TaxIDNumber":"234",
      "CardNumber":"1111",
      "AccessPincode":"12345",
      "Custom1":"asdasd",
      "Custom2":null,
      "Custom3":null,
      "Custom4":null,
      "Custom5":null,
      "Custom6":null,
      "Custom7":null,
      "Custom8":null,
      "Custom9":null,
      "Custom10":null,
      "Custom11":null,
      "Custom12":null,
      "Custom13":null,
      "Custom14":null,
      "Custom15":null,
      "Custom16":null,
      "Custom17":"",
      "Custom18":null,
      "Custom19":null,
      "Custom20":null,
      "Custom21":null,
      "Custom22":null,
      "Custom23":null,
      "Custom24":null,
      "Custom25":null,
      "Custom26":null,
      "Custom27":null,
      "Custom28":null,
      "Custom29":null,
      "Custom30":null,
      "Password":"NewPassword1234",
      "PasswordConfirm":"NewPassword1234",
      "GeneralTermsAcceptedOnline":false,
      "Avatar":null,
      "TourDate":null
   }';
    }

}
