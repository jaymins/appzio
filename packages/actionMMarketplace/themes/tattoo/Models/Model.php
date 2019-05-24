<?php

namespace packages\actionMMarketplace\themes\tattoo\Models;

use packages\actionMitems\Models\ItemModel;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\Model as BootstrapModel;
use packages\actionMMarketplace\Models\UserBidModel;

class Model extends BootstrapModel {


    /**
     * Returns additional items added by the given artist.
     * The given item id is ignored.
     *
     * @param $artistId
     * @param $itemId
     * @return array|mixed|null|static[]
     */
    public function getOtherArtistItems($artistId,$limit = 3)
    {
        return ItemModel::model()->findAll('play_id = :playId LIMIT '.$limit, array(
            ':playId' => $artistId
        ));
    }

    public function userBidSelect($id) {

        $userBidObject = UserBidModel::model()->findByPk($id);
        $userBidObject->status = UserBidModel::ACCEPTED_STATUS;
        $userBidObject->update();

        $allbids = UserBidModel::model()->findAll('bid_item_id = :bid_item_id', array(
            ':bid_item_id' => $userBidObject->bid_item_id
        ));

        foreach ($allbids as $bid) {
            if ($bid->id != $userBidObject->id) {
                $bid->status = UserBidModel::DECLINED_STATUS;
                $bid->update();
            }
        }

        $bidObject = BidItemModel::model()->findByPk($userBidObject->bid_item_id);
        $bidObject->status = BidItemModel::COMPLETE_STATUS;
        $bidObject->update();
    }

    public function userBidDecline($id) {

        $bidObject = UserBidModel::model()->findByPk($id);
        $bidObject->status = UserBidModel::DECLINED_STATUS;
        $bidObject->update();
    }

    public function placeUserBid($bidId) {
        $bidObject = new UserBidModel();
        $bidObject->bid_item_id = $bidId;
        $bidObject->price = $this->getSubmittedVariableByName('price');
        $bidObject->message = $this->getSubmittedVariableByName('message');
        $bidObject->play_id = $this->playid;
        $bidObject->created_date = time();
        $bidObject->status = UserBidModel::PENDING_STATUS;
        $bidObject->save();
    }

    public function cancelBid($id) {
        $bidObject = BidItemModel::model()->findByPk($this->getMenuId());
        $bidObject->status = BidItemModel::CANCLLED_STATUS;
        $bidObject->update();
    }
}