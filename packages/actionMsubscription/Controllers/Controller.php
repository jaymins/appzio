<?php

namespace packages\actionMsubscription\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMsubscription\Models\Model as ArticleModel;
use packages\actionMsubscription\Views\View as ArticleView;

class Controller extends BootstrapController
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault()
    {
        $data = array();

        $this->model->validateSubscriptions('subscriptions');

        if ($this->getMenuId() == 'addcode') {
            $this->model->saveVariable('affiliation_code', $this->model->getSubmittedVariableByName('code'));
        }

        if (isset($_REQUEST['purchase_product_id'])) {
            $this->model->handlePurchase();
            return ['Forward', $data];
        }

        $this->model->rewriteActionConfigField('hide_scrollbar', 1);

        $data['header_text'] = $this->model->getConfigParam('header_text','');
        $data['footer_text'] = $this->model->getConfigParam('footer_text','');

        if($this->model->getConfigParam('data_mode') == 'db'){
            $data['products'] = $this->model->getProducts();
            $data['subscription'] = $this->model->getUserSubscriptionStatus();
        }

        $data['purchase_yearly'] = $this->model->getSavedVariable('purchase_yearly');
        $data['purchase_once'] = $this->model->getSavedVariable('purchase_once');
        $data['affiliation_code'] = $this->model->getSavedVariable('affiliation_code');
        $data['subscription_affiliation_code'] = $this->model->getConfigParam('subscription_affiliation_code');
        $data['subscription_terms'] = $this->model->getConfigParam('subscription_terms');
        $data['subscription_price'] = $this->model->getConfigParam('subscription_price');
        $data['annual_subscription_price'] = $this->model->getConfigParam('annual_subscription_price');
        $data['monthly_subscription_price'] = $this->model->getConfigParam('monthly_subscription_price');
        $data['subscription_code_ios'] = $this->model->getConfigParam('subscription_code_ios');
        $data['subscription_code_android'] = $this->model->getConfigParam('subscription_code_android');
        $data['annual_subscription_code_ios'] = $this->model->getConfigParam('annual_subscription_code_ios');
        $data['annual_subscription_code_android'] = $this->model->getConfigParam('annual_subscription_code_android');
        $data['monthly_subscription_code_ios'] = $this->model->getConfigParam('monthly_subscription_code_ios');
        $data['monthly_subscription_code_android'] = $this->model->getConfigParam('monthly_subscription_code_android');

        return ['View', $data];
    }

}