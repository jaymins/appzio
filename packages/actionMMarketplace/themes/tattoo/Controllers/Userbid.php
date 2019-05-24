<?php

namespace packages\actionMMarketplace\themes\tattoo\Controllers;

use packages\actionMMarketplace\Models\BidItemImageModel;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\UserBidModel;
use packages\actionMMarketplace\themes\tattoo\Views\View as ArticleView;
use packages\actionMMarketplace\themes\tattoo\Models\Model as ArticleModel;

class Userbid extends \packages\actionMMarketplace\Controllers\Userbid {

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
            $this->model->sessionSet('userBidId',$this->getMenuId());
        }

        $id = $this->model->sessionGet('userBidId');

        if ( empty($id) ) {
            return ['UserBid', $data];
        }

        $user_bid_item = UserBidModel::model()->findByPk($id);

        if ( empty($user_bid_item) ) {
            return ['UserBid', $data];
        }

        if ($id) {
            $userBidObject = UserBidModel::model()->findByPk($id);
            $bidObject = BidItemModel::model()->findByPk($userBidObject->bid_item_id);

            $data['bidObject'] = $bidObject;
            $data['userBidObject'] = $userBidObject;
            $data['user'] = $this->model->foreignVariablesGet($userBidObject->play_id);
            $data['user']['playid'] = $userBidObject->play_id;

            return ['UserBid', $data];
        }
    }

    public function actionSelect() {
        $this->no_output = 1;
        $this->model->userBidSelect($this->getMenuId());
    }

    public function actionDecline() {
        $this->no_output = 1;
        $this->model->userBidDecline($this->getMenuId());
    }

}