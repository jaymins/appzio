<?php

namespace packages\actionMMarketplace\themes\tattoo\Controllers;

use packages\actionMMarketplace\Models\BidItemImageModel;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\UserBidModel;
use packages\actionMMarketplace\themes\tattoo\Views\View as ArticleView;
use packages\actionMMarketplace\themes\tattoo\Models\Model as ArticleModel;

class Controller extends \packages\actionMMarketplace\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $data = [];


        if ($this->getMenuId()) {
            $this->model->sessionSet('bidId',$this->getMenuId());
        }

        $id = $this->model->sessionGet('bidId');

        $bidObject = BidItemModel::model()->findByPk($id);
        $images= BidItemImageModel::model()->findAll('bid_item_id = :bid_item_id', array(
            ':bid_item_id' => $id
        ));

        $data['bidObject'] = $bidObject;
        $data['images'] = $images;

        $data['userBidObjects'] = UserBidModel::model()->findAll('bid_item_id = :bid_item_id AND status <> :status order by created_date DESC', array(
            ':bid_item_id' => $id,
            ':status' => UserBidModel::DECLINED_STATUS
        ));

        return ['View', $data];
    }


    public function actionPlacebid() {
        $this->no_output = 1;
        $this->model->placeUserBid($this->getMenuId());

    }

    public function actionCancel() {
        $this->no_output = 1;
        $this->model->cancelBid($this->getMenuId());

    }
}