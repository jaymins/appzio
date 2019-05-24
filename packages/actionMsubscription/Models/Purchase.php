<?php

namespace packages\actionMsubscription\Models;

use Bootstrap\Models\BootstrapModel;
use packages\actionMregister\Models\CompaniesEmployeeModel;

trait Purchase
{

    public $product_codes = [];
    public $data_mode = false;

    public function setProductCodes()
    {
        $output = [];

        if ($this->data_mode == 'db' OR $this->getConfigParam('data_mode') == 'db') {
            $products = $this->getProducts();
            foreach($products as $product){
                if($product->type == 'monthly_subscription'){
                    $output['ios']['monthly'] = $product['code_ios'];
                    $output['android']['monthly'] = $product['code_android'];
                }elseif($product->type == 'yearly_subscription'){
                    $output['ios']['yearly'] = $product['code_ios'];
                    $output['android']['yearly'] = $product['code_android'];
                }elseif($product->type == 'monthly_subscription_no_renew'){
                    $output['ios']['once'] = $product['code_ios'];
                    $output['android']['once'] = $product['code_android'];
                }
            }
        } else {
            $output['ios']['annual'] = $this->getConfigParam('annual_subscription_code_ios');
            $output['ios']['monthly'] = $this->getConfigParam('monthly_subscription_code_ios');
            $output['android']['annual'] = $this->getConfigParam('annual_subscription_code_android');
            $output['android']['monthly'] = $this->getConfigParam('monthly_subscription_code_android');
        }

        $this->product_codes = $output;
    }

    public function getCode($platform='ios',$period='monthly'){
        if(!$this->product_codes){
            $this->setProductCodes();
        }

        if(isset($this->product_codes[$platform][$period])){
            return $this->product_codes[$platform][$period];
        }

        return false;
    }


    /**
     * Validate if the subscriptions are not expired
     *
     * @param string $subscribeActionPermaname the permaname of the action
     * that holds the Subscription configuration
     */
    public function validateSubscriptions($subscribeActionPermaname = 'subscription')
    {

        
        CompaniesEmployeeModel::saveRegistration($this->getSavedVariable('email'),
            $this->playid, $this->appid);
        
        /** @var BootstrapModel $this->model */

        $config = $this->getActionConfigByPermaname($subscribeActionPermaname);

        if ($this->getSavedVariable('system_source') == 'client_iphone') {
            $this->validateSubscriptionsIOS($config);
        } else {
            $this->validateSubscriptionsAndroid($config);
        }

    }

    /**
     * Check for iOS
     * @param $config object
     */
    protected function validateSubscriptionsIOS($config)
    {

        $annual = $this->getCode('ios','annual');
        $monthly = $this->getCode();

        $this->saveVariable('purchase_yearly', 0);
        $this->saveVariable('purchase_monthly', 0);

        $subscriptionJsonData = $this->getSavedVariable('subscription_info_ios');

        if ( empty($subscriptionJsonData) ) {
            return false;
        }

        $subscriptionData = json_decode($subscriptionJsonData, true);

        if (empty($subscriptionData['info'])) {
            return false;
        }

        foreach ($subscriptionData['info'] as $subscription) {

            if ( !isset($subscription['product_id']) ) {
                continue;
            }

            $today = new \DateTime('now',new \DateTimeZone('Etc/GMT'));
            $expiryDate = new \DateTime($subscription['expires_date']);

            if ($today <= $expiryDate) {

/*                echo($monthly);
                echo('<br>');
                echo($subscription['product_id']);
                echo('<br>');
                echo('<br>');*/

                if ($annual && $subscription['product_id'] == $annual) {
                    $this->saveVariable('purchase_yearly', 1);
                }

                if ($monthly && $subscription['product_id'] == $monthly) {
                    $this->saveVariable('purchase_monthly', 1);
                }

                $this->saveVariable('subscription_expiry', $subscription['expires_date']);
                $this->saveVariable('subscription_expiry', $subscription['purchase_date']);
            }

        }

        return true;
    }

    /**
     * Check for Android
     * @param $config object
     */
    protected function validateSubscriptionsAndroid($config)
    {
        $annual = $this->getCode('android','annual');
        $monthly = $this->getCode('android','monthly');

        $this->saveVariable('purchase_yearly', 0);
        $this->saveVariable('purchase_monthly', 0);

        $subscriptionJsonData = $this->getSavedVariable('subscription_info_android');

        if ( empty($subscriptionJsonData) ) {
            return false;
        }

        $subscriptionData = json_decode($subscriptionJsonData, true);

        if (empty($subscriptionData['info'])) {
            return false;
        }

        foreach ($subscriptionData['info'] as $subscription) {

            if ( !isset($subscription['productId']) ) {
                continue;
            }

            if ($annual && $subscription['productId'] == $annual) {
                $this->saveVariable('purchase_yearly', 1);
            }

            if ($monthly && $subscription['productId'] == $monthly) {
                $this->saveVariable('purchase_monthly', 1);
            }

        }

        return true;
    }

    /**
     * Get the config object of an action by given permaname
     * @param $subscribeActionPermaname string
     * @return mixed
     */
    public function getActionConfigByPermaname($subscribeActionPermaname)
    {
        $subscribeActionId = $this->getActionidByPermaname($subscribeActionPermaname);

        if ( empty($subscribeActionId) )
            return false;

        $subscribeAction = \Aeaction::model()->with('aetype')->findByPk($subscribeActionId);

        if ( isset($subscribeAction->config) AND $subscribeAction->config ) {
            return @json_decode($subscribeAction->config);
        }

        return false;
    }

    /**
     * Get param from given action config object
     * @param $param string
     * @param $config object
     * @return bool
     */
    public function getParamFromConfig($param, $config)
    {
        if (isset($config->$param)) {
            return $config->$param;
        }

        return false;
    }

    public function savePurchase($productid){

        $products = $this->getProducts();

        foreach ($products as $product){
            if($product->code_ios == $productid OR $product->code_android == $productid){
                $product_obj = $product;
                $type = $product->type;
            }

            if(isset($product_obj)){
                break;
            }
        }

        if(!isset($product_obj) OR !isset($type)){
            return false;
        }


        switch($type) {
            case 'monthly_subscription':
                $var = 'purchase_monthly';
                break;

            case 'yearly_subscription':
                $var = 'purchase_yearly';
                break;

            case 'monthly_subscription_no_renew':
                $var = 'purchase_once';
                break;
        }

        if(!isset($var)){
            return false;
        }

        $this->saveVariable($var, 1);

        if($this->getConfigParam('data_mode') == 'db' OR $this->data_mode == 'db'){
            $obj = new PurchaseModel();
            $obj->play_id = $this->playid;
            $obj->app_id = $this->appid;
            $obj->product_id = $product->id;

            if($type == 'monthly_subscription'){
                $obj->subscription = 1;
                $obj->monthly = 1;
                $obj->expiry = $product_obj->currency;
            }

            if($type == 'yearly_subscription'){
                $obj->subscription = 1;
                $obj->yearly = 1;
                $obj->expiry = $product_obj->currency;
            }

            $obj->price = $product_obj->price;
            $obj->currency = $product_obj->currency;
            $obj->email = $this->getSavedVariable('email');
            $obj->insert();
        }

        return true;
    }

    public function getUserSubscriptionStatus()
    {
        $subscriptions = [];

        if ($this->getSavedVariable('purchase_once')) {
            $subscriptions['one_time_purchase'] = true;
        }

        if ($this->getSavedVariable('purchase_monthly')) {
            $subscriptions['monthly_subscription'] = true;
        }

        if ($this->getSavedVariable('purchase_yearly')) {
            $subscriptions['yearly_subscription'] = true;
        }

        return $subscriptions;
    }

    public function getProducts()
    {

        $criteria = new \CDbCriteria(array(
            'condition' => "`type` LIKE '%subscription%' AND `app_id` = :appid",
            'params' => array(
                ':appid' => $this->appid,
            ),
            'order' => 'id ASC'
        ));

        $products = PurchaseProductsModel::model()->findAll($criteria);
        return $products;

    }

}