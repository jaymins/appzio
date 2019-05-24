<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

Trait Stripe {

    public function payWithStripe($amount=15,$currency='GBP')
    {

        $api_key = $this->getMobileConfigItem('stripe_secret_key');

        if(!$api_key){
            $this->validation_errors['payment'] = 'Missing payment key!';
            return false;
        }

        try {
            \Stripe\Stripe::setApiKey($api_key);
        } catch (\Exception $e){
            $this->validation_errors['payment'] = $e->getCode();
            return false;
        }

        $customerid = $this->getSavedVariable('stripe_customer_id');
        $card = $this->getSavedVariable('stripe_card');

        if(!$card){
            $this->validation_errors['payment'] = '{#please_select_payment_method_first#}';
            return false;
        }

        if(!$customerid){
            $this->validation_errors['payment'] = '{#stripe_customer_not_found#}';
            return false;
        }

        try {
            $charge = \Stripe\Charge::create([
                'amount' => $amount.'00',
                'customer' => $customerid,
                'currency' => $currency,
                'source' => $card]);
        } catch (\Exception $e){
            $this->validation_errors['payment'] = $e->getCode();
            return false;
        }

        $this->charge_id = $charge->id;

        if(isset($charge->id) AND $charge->id){
            return $charge->id;
        }

    }





}
