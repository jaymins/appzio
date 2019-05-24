<?php

namespace packages\actionMMarketplace\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\Model;
use packages\actionMMarketplace\Models\UserBidModel;

class Listing extends BootstrapController
{

    /**
     * @var Model
     */
    public $model;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $role = $this->model->getSavedVariable('role');
        $user = \AeplayVariable::getArrayOfPlayvariables($this->playid);
        $bidItems = [];
        $bids = [];

//        $bidItems = $this->getBidItemsByRole($role);

        if($role == 'user'){
            $status =  $this->getOwnerStatusFromActiveTab();
            $bidItems = $this->model->getOwnerBids($status);
            $view = 'UserListing';
        }
        else{
            $status = $this->getArtistStatusFromActiveTab();

            if($status == UserBidModel::AVAILABLE_STATUS) {
                $bidItems = $this->model->getBidItemsForBidding();
            }
            else{
                $bids = $this->model->getBidsByStatus($status);
            }
            $view = 'ArtistListing';
        }
        return [$view, compact('bidItems', 'bids', 'user')];

    }

//    protected function getBidItemsByRole($role)
//    {
//        if($role == 'user'){
//            $status =  $this->getOwnerStatusFromActiveTab();
//            $bidItems = $this->model->getOwnerBids($status);
//        }
//        else{
//            $status = $this->getArtistStatusFromActiveTab();
//            $bidItems = $this->model->getBidItemsForBidding($status);
//        }
//
//        return $bidItems;
//    }


    protected function getOwnerStatusFromActiveTab()
    {
        $activeBids = 1;
        $completedBids = 2;
        $currentTab = $this->current_tab;
        $status = '';

        switch($currentTab){
            case $currentTab == $activeBids:
                $status = BidItemModel::ACTIVE_STATUS;
                break;
            case $currentTab == $completedBids:
                $status = BidItemModel::COMPLETE_STATUS;
                break;
        }
        return $status;
    }

    protected function getArtistStatusFromActiveTab()
    {
        $availableBids = 1;
        $active = 2;
        $completedBids = 3;
        $currentTab = $this->current_tab;
        $status = '';

        switch($currentTab){
            case $currentTab == $availableBids:
                $status = UserBidModel::AVAILABLE_STATUS;
                break;
            case $currentTab == $active:
                $status = UserBidModel::PENDING_STATUS;
                break;
            case $currentTab == $completedBids:
                $status = 'completed';
                break;
        }
        return $status;
    }}