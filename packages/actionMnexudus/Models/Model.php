<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

use Bootstrap\Models\BootstrapModel;
use GuzzleHttp;
use packages\actionMnexudus\Controllers\Controller;
use Stripe\ApiOperations\Delete;
use yii\db\Exception;

$sourcepath = \Yii::getPathOfAlias('application.components.stripe');
require_once($sourcepath . DS . 'init.php');

class Model extends BootstrapModel
{

    public $validation_errors;
    public $guzzle;
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    public $phone;
    public $errors = [];
    public $log = [];

    public $logging = false;

    // strictly for use of a UnitTests
    public $cleartextpassword;

    public $current_space;
    public $charge_id;

    use NexudusApi;
    use NexudusApiHelpers;
    use Authentication;
    use Spaces;
    use Bookings;
    use Ledgers;
    use Stripe;

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->email = $this->getSavedVariable('email');
        $this->password = $this->getSavedVariable('password');
    }

    public function __destruct()
    {
        if ($this->logging) {
            $content = 'Log entries:' . chr(10);
            $content .= implode(chr(10), $this->log);
            $content .= 'Error entries:' . chr(10);
            $content .= implode(chr(10), $this->errors);

            $filename = date('Y-m-d_H-i') . '_nexuduslog.txt';
            $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . 'mobile_error_logs/';

            file_put_contents($path . $filename, $content);
        }
    }

    public function getFaqItems()
    {
        $characters = $this->getConfigParam('faq_items');
        $characters = explode(';', $characters);

        $count = 0;

        foreach ($characters as $character) {
            if ($count == 1 AND isset($title)) {
                $output[] = array('title' => $title, 'content' => $character);
                unset($title);
                continue;
            }

            $title = $character;
            $count = 1;
        }

        return $output;

    }

    public function resetPassword($email)
    {
        $this->ApiResetPass($email);
        return true;
    }



    private function setCoWorkerAndBusinessId()
    {

        if (!$this->getSavedVariable('coworker_id')) {
            $coworkers = $this->ApiGetCoworkers();
            foreach ($coworkers as $coworker) {
                if ($coworker['Email'] == $this->getSavedVariable('email')) {
                    $var['coworker_id'] = $coworker['Id'];
                    $var['business_id'] = $coworker['InvoicingBusinessId'];
                    $this->saveNamedVariables($var);
                    return true;
                }
            }
        }

        $coworker = $this->ApiGetCoWorker($this->getSavedVariable('coworker_id'));

        if (isset($coworker['InvoicingBusinessId']) AND $coworker['InvoicingBusinessId'] > 0) {
            $var['business_id'] = $coworker['InvoicingBusinessId'];
            $var['businesses'] = $coworker['Businesses'];
            $this->saveNamedVariables($var);
            return true;
        }

        /* if its set to 0, we assign the first possible business id for it */
        if (isset($coworker['InvoicingBusinessId']) AND $coworker['InvoicingBusinessId'] == 0) {
            $businesses = $this->ApiGetBusinesses();
            if (isset($businesses[0]['Id'])) {
                $bid = $businesses[0]['Id'];
                $coworker['InvoicingBusinessId'] = $bid;
                $this->ApiSetCoWorker($coworker['Id'], $coworker);
                $this->saveVariable('business_id', $bid);
            }
        }

        return false;
    }

    /**
     * Check the businesses variable and set it if its not up to date
     * @return bool
     */
    private function checkBusinesses()
    {
        if (!$this->getSavedVariable('businesses')) {
            if (!$this->getSavedVariable('coworker_id')) {
                $this->setCoWorkerAndBusinessId();
            }

            $coworker = $this->ApiGetCoWorker($this->getSavedVariable('coworker_id'));
            $businesses = $this->ApiGetBusinesses();

            if (!isset($businesses) OR !isset($coworker['Businesses'])) {
                $this->updateBusinesses($coworker);
            } elseif (count($businesses) != $coworker['Businesses']) {
                $this->updateBusinesses($coworker);
            }
        } else {
            $var = @json_decode($this->getSavedVariable('businesses'), true);
            $businesses = $this->ApiGetBusinesses();
            if (count($var) != count($businesses)) {
                $this->updateBusinesses();
            }
        }
    }

    /**
     * Updates all available businesses to user & updates the variable for businesses
     * @param bool $coworker
     */
    private function updateBusinesses($coworker = false)
    {
        $businesses = $this->ApiGetBusinesses();

        if (!$coworker) {
            $coworker = $this->ApiGetCoWorker($this->getSavedVariable('coworker_id'));
        }

        foreach ($businesses as $business) {
            $ids[] = $business['Id'];
        }

        $ids = json_encode(array_values($ids));
        $coworker['AddedBusinesses'] = $ids;
        $this->ApiSetCoWorker($coworker['Id'], $coworker);
        $this->saveVariable('businesses', $ids);
    }

    public function updateUserInfoFields(array $fields)
    {
        $info = $this->ApiGetCoWorker($this->getSavedVariable('coworker_id'));
        $info = array_merge($info, $fields);
        $this->ApiSetCoWorker($this->getSavedVariable('coworker_id'), $info);
        return true;
    }

    private function updateUserInfo()
    {
        $info = $this->ApiGetCoWorker($this->getSavedVariable('coworker_id'));
        $vars['firstname'] = $info['GuessedFirstName'];
        $vars['lastname'] = $info['GuessedLastName'];

        if ($info['MobilePhone']) {
            $vars['phone'] = $info['MobilePhone'];
        } else {
            $this->errors['phone'] = '{#please_input_your_phone#}';
        }

        $this->saveNamedVariables($vars);
    }

    public function setUserInfo()
    {

        //$coworker = $this->ApiGetCoWorker('688194424');

        if (!$this->getSavedVariable('coworker_id') OR !$this->getSavedVariable('business_id')) {
            $this->setCoWorkerAndBusinessId();
        }

        $this->checkBusinesses();
        $this->updateUserInfo();
        return true;


        $coworker = $this->ApiGetCoWorker('688194424');


        $info = $this->ApiGetUserInfo();

        print_r($this->log);
        die();

        // Get businesses
        /*        $businesses = $this->ApiGetBusinesses();

                if (!isset($businesses)) {
                    return false;
                }

                $ids = array();

                foreach ($businesses as $business) {
                    $ids[] = $business['Id'];
                }

                $ids = json_encode(array_values($ids));

                $coworker = $this->ApiGetCoWorker('688194424');
                $coworker['AddedBusinesses'] = $ids;
                $set = $this->ApiSetCoWorker('688194424',$coworker);*/

        $coworkers = $this->ApiGetCoWorker('737501051');

        print_r($coworkers);
        die();


        $coworkers = $this->ApiGetCoworkers();

        foreach ($coworkers as $coworker) {
            $coworker['AddedBusinesses'] = $ids;
            $this->ApiSetCoWorker($coworker['Id'], $coworker);
        }

        print_r($this->log);
        $coworkers = $this->ApiGetCoworkers();

        print_r($coworkers);
        die();

        $coworker = $this->ApiGetCoWorker('688194424');
        $coworker['AddedBusinesses'] = $ids;
        $set = $this->ApiSetCoWorker('688194424', $coworker);

        //$info = $this->ApiGetUserInfo();

        $coworker = $this->ApiGetCoWorker('688194424');

        print_r($coworker);
        die();


        $coworkers = $this->ApiGetCoworkers();

        print_r($coworkers);
        die();

        // Get users's info
        $info = $this->ApiGetUserInfo();

        print_r($this->log);

        print_r($info);
        die();

        // set users variables
        if (!isset($info['GuessedFirstName'])) {
            echo(2);
            die();

            return false;
        }

        $coworker_id = $info['Id'];

        $vars['firstname'] = $info['GuessedFirstName'];
        $vars['lastname'] = $info['GuessedLastName'];
        $vars['phone'] = $info['MobilePhone'];
        $vars['coworker_id'] = $coworker_id;
        $vars['busines_id'] = $coworker_id;

        $coworker_id = $this->getSavedVariable('coworker_id');
        $business_id = $this->getSavedVariable('business_id');

        $this->saveNamedVariables($vars);

        // get coworker info
        $coworker = $this->ApiGetCoWorker($info['Id']);

        if (!isset($coworker['Businesses'])) {
            echo(3);
            die();

            return false;
        }

        if (count($ids) != count($coworker['Businesses'])) {
            $info['Businesses'] = $ids;
            $this->ApiSetCoWorker($coworker_id, $info);
        }

        $info['MobilePhone'] = '';
        $set = $this->ApiSetCoWorker($coworker_id, $info);

        print_r($set);

        $coworker = $this->ApiGetCoWorker($info['Id']);

        print_r($coworker);
        die();
    }

    public function savePhone()
    {

        $phone = $this->getSubmittedVariableByName('phone');

        $phone = trim($phone);
        $phone = str_replace(' ', '', $phone);

        if (!$phone) {
            $this->validation_errors['phone'] = '{#you_need_to_input_your_phone_number#}';
        }

        if (!$this->validation_errors) {

            $phone = str_replace('+', '', $phone);

            if(substr($phone, 0,3) == '044'){
                $phone = substr($phone, 3);
            }elseif(substr($phone, 0,2) == '44'){
                $phone = substr($phone, 2);
            }elseif(substr($phone, 0,4) == '0044'){
                $phone = substr($phone, 4);
            }elseif(substr($phone, 0,2) == '00'){
                $phone = substr($phone, 2);
            }elseif(substr($phone, 0,1) == '0'){
                $phone = substr($phone, 1);
            }

            $vars['phone'] = '+44'.$phone;
            $vars['opendoor_phone'] = '+44' .$phone;
            $vars['verification_sent'] = 1;
            $this->saveNamedVariables($vars);

            /* update info to Nexudus */
            $fields['MobilePhone'] = $vars['phone'];
            $this->updateUserInfoFields($fields);
            return true;
        }

        return false;


    }


}
