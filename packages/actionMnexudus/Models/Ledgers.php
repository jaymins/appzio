<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

Trait Ledgers {

    public function getLedgers(){
        $this->api_endpoint = self::$api_endpoint_rest;
        $response = $this->curlJsonCall("api/billing/coworkerledgerentries/", [], 'GET', 'admin');
        return $response;
    }


    public function addLedger($sum='20',$code='Stripe'){

        $this->api_endpoint = self::$api_endpoint_rest;

        if(!$this->getSavedVariable('coworker_id')){
            $email = $this->getSavedVariable('email');
            $coworkers = $this->curlJsonCall("api/spaces/coworkers?Coworker_Email=".$email, [], 'GET', 'admin');

            if(isset($coworkers['Records'][0])){
                $coworker_id = $coworkers['Records'][0]['Id'];
                $business_id = $coworkers['Records'][0]['InvoicingBusinessId'];
                $this->saveVariable('coworker_id', $coworker_id);
                $this->saveVariable('business_id', $business_id);
            }

        } else {
            $coworker_id = $this->getSavedVariable('coworker_id');
            $business_id = $this->getSavedVariable('business_id');
        }

        if(!isset($coworker_id)){
            $this->log[] = 'COULD NOT GET COWORKERID';
            return false;
        }

        $payload = new \stdClass();
        $payload->CoworkerId = $coworker_id;
        $payload->BusinessId = $business_id;
        $payload->Description = 'Paid with Stripe';
        $payload->Balance = $sum;
        $payload->Credit = $sum;
        $payload->Code = $code;

        $response = $this->curlJsonCall("api/billing/coworkerledgerentries/", $payload, 'POST', 'admin');
        return true;
    }






}
