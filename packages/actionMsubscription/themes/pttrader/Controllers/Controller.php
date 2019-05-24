<?php

namespace packages\actionMsubscription\themes\pttrader\Controllers;

use packages\actionMsubscription\themes\pttrader\Models\Model as ArticleModel;
use packages\actionMsubscription\themes\pttrader\Views\Main;
use packages\actionMsubscription\themes\pttrader\Views\View as ArticleView;

class Controller extends \packages\actionMsubscription\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $data = array();

        if (isset($_REQUEST['purchase_product_id'])) {
            $this->model->handlePurchase();
            return ['Forward', $data];
        }

        $data['subscriptions'] = $this->model->getUserSubscriptionStatus();

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