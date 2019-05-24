<?php

namespace packages\actionMMarketplace\Models;

use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel
{

    public $validation_errors;

    public function getBidItemsForBidding($distance = 1000)
    {
        $userData = \AeplayVariable::getArrayOfPlayvariables($this->playid);
        $lat = !empty($userData['lat']) ? $userData['lat'] : 0;
        $lon = !empty($userData['lat']) ? $userData['lat'] : 0;
        $bidItems = [];
        $params = array(
            ':playId' => $this->playid,
            ':status' => BidItemModel::ACTIVE_STATUS
        );
//        if($status == UserBidModel::AVAILABLE_STATUS){
        $condition = "t.status = :status ";
        if ($lat && $distance) {
//                \Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
//                \Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
//                \Yii::app()->db->createCommand("set @bounding_distance=360")->execute();
            $select = array("*",
                "distance" => "( 3959  * acos( 
                     cos( radians($lat) ) 
                     * cos( radians(`lat`) )
                    * cos( radians(`lon`) - radians($lon)) 
                    + sin(radians($lat)) 
                    * sin( radians(`lat`)))
                ) AS distance");
        } else {
            $select = "*";
        }
        //Only in miles
//                $select = array("*",
//                    "distance" => "( 3959  * acos( cos( radians(@orig_lat) ) * cos( radians(`lat`) )
//                    * cos( radians(`lon`) - radians(@orig_long)) + sin(radians(@orig_lat))
//                    * sin( radians(`lat`)))
//                ) AS distance");
////                $params['unit'] = 1 ;
//            } else {
//                $select = "*";
//            }

        $condition .= "AND t.id NOT IN (SELECT ae_ext_user_bids.bid_item_id FROM ae_ext_user_bids WHERE ae_ext_user_bids.play_id = :playId)";


        $criteria = new \CDbCriteria();
        $criteria->select = $select;
        $criteria->condition = $condition;

        if ($distance && $lat) {
            $criteria->having = 'distance <= ' . $distance . ' OR distance IS NULL';
        }

        $criteria->group = 't.id';
        $criteria->order = 't.id DESC';
        $criteria->params = $params;
        $bidItems = BidItemModel::model()->findAll($criteria);
//        }

        return $bidItems;
    }

    public function getBidsByStatus($status)
    {
        $bids = [];
        if($status == UserBidModel::PENDING_STATUS){
            $params = array(
                ':playId' => $this->playid,
                ':status' => UserBidModel::PENDING_STATUS
            );
            $condition = "t.status = :status AND t.play_id = :playId";

            $criteria = new \CDbCriteria();
            $criteria->condition = $condition;
            $criteria->group = 't.id';
            $criteria->order = 't.id DESC';
            $criteria->params = $params;

            $bids = UserBidModel::model()->with('bidItem')->findAll($criteria);
        }

        if($status == 'completed'){
            $params = array(
                ':playId' => $this->playid,
                ':status' => UserBidModel::PENDING_STATUS,
            );
            $condition = "t.play_id = :playId AND t.status != :status";

            $criteria = new \CDbCriteria();
            $criteria->condition = $condition;
            $criteria->group = 't.id';
            $criteria->order = 't.id DESC';
            $criteria->params = $params;

            $bids = UserBidModel::model()->with('bidItem')->findAll($criteria);
        }

        return $bids;
    }

    public function getOwnerBids($status = BidItemModel::ACTIVE_STATUS)
    {
        $bids = BidItemModel::model()
            ->with(array(
                'images',
                'bids' => array(
                    'together' => false,
                    'condition' => 'bids.status != :decline',
                    'params' => array(':decline' => UserBidModel::DECLINED_STATUS)
                )
            ))
            ->findAll(array(
                'condition' => 't.play_id = :playId AND t.status = :status',
                'order' => 't.id DESC',
                'params' => array(
                    ':playId' => $this->playid,
                    ':status' => $status
                )
            ));

        return $bids;
    }

    public function fillPresetData($variables): array
    {
        $data = array();

        foreach ($variables as $variable => $value) {
            $data[$variable] = $value;
        }

        return $data;
    }


    public function bidToItem($bidItemID)
    {
        $artistBid = new UserBidModel;
        $artistBid->play_id = $this->playid;
        $artistBid->bid_item_id = $bidItemID;
        $artistBid->price = $this->getSubmittedVariableByName('price');
        $artistBid->message = $this->getSubmittedVariableByName('message');
        $artistBid->status = UserBidModel::PENDING_STATUS;
        $artistBid->created_date = time();
        $artistBid->save();

        return $artistBid->getPrimaryKey();
    }

    public function createBidItem()
    {
        $styles = $this->getSavedVariable('itemcategory');

        $bidItem = new BidItemModel;
        $bidItem->description = $this->getSubmittedVariableByName('description');
        $bidItem->play_id = $this->playid;
        $bidItem->title = $this->getSubmittedVariableByName('title');
        $bidItem->styles = $styles;
        $bidItem->lat = $this->getSavedVariable('lat', 0);
        $bidItem->lon = $this->getSavedVariable('lon', 0);
        $bidItem->status = BidItemModel::ACTIVE_STATUS;
        $bidItem->valid_date = $this->getSubmittedVariableByName('date', strtotime( '+ 7 days' ));
        $bidItem->save();

        $itemID = $bidItem->getPrimaryKey();

        $images = $this->getItemImages();

        if(!empty($images)){
            foreach ($images as $image){
                $bidImage = new BidItemImageModel;
                $bidImage->bid_item_id = $itemID;
                $bidImage->image = $image;
                $bidImage->save();
            }
        }

        return $itemID;
    }

    public function removeFromVariable($variable,$value){

        $var = $this->getSavedVariable($variable);

        if($var){
            $var = json_decode($var,true);
            if(is_array($var) AND !empty($var)){
                $remove = false;

                foreach ($var as $key => $search) {
                    if ($value == $search) {
                        $remove = $key;
                    }
                }
                if ($remove !== false) {
                    unset($var[$remove]);
                }
            }
        }

        if(!is_array($var) OR empty($var)){
            $var = array();
        }

        $var = json_encode($var);
        $this->saveVariable($variable,$var);

    }

    public function getItemImages(): array
    {
        $images = array();

        if ($this->getSavedVariable('itempic')) {
            // Save main item image
            $images['itempic'] = $this->getSavedVariable('itempic');
        }

        for ($i = 2; $i < 7; $i++) {
            $picture = 'itempic' . $i;
            if (empty($images['itempic'])) {
                $picIndex = 'itempic';
            } else {
                $picIndex = $picture;
            }

            if ($this->getSavedVariable($picture)) {
                $images[$picIndex] = $this->getSavedVariable($picture);
            }
        }

        return $images;
    }

    public function setBackgroundColor($color = '#edac34')
    {
        $this->rewriteActionConfigField('background_color', $color);
    }

    public function validateInput()
    {
        $requiredVariables = array(
            'title' => 'empty|length:20',
            'description' => 'empty'
        );

        $submittedVariables = $this->getAllSubmittedVariablesByName();
        $categories = array();

        foreach ($submittedVariables as $key => $value) {
            if (strstr($key, 'category|') && !empty($value)) {
                $categories[] = $value;
            }

            foreach ( $requiredVariables as $var => $validation_type ) {

                $validations = explode('|', $validation_type);

                foreach ( $validations as $validation ) {

                    if ( $key != $var ) {
                        continue;
                    }

                    if ( $validation == 'empty' AND empty($value) ) {
                        $this->validation_errors[$key] = 'The ' . $key . ' field is required';
                    } else if ( $value AND preg_match('~length~', $validation) ) {
                        $rq_chars = str_replace('length:', '', $validation);
                        if ( strlen($value) > $rq_chars )
                            $this->validation_errors[$key] = 'The ' . $key . ' field should contain ' . $rq_chars . ' characters max';
                    }
                }

            }

        }

        if (empty($categories)) {
            $this->validation_errors['categories'] = 'Please add at least one category';
        }

        if (isset($this->validation_errors['categories'])) {

            $categories = json_decode($this->getSavedVariable('itemcategory'),1);
            if (!empty($categories)) {
                unset($this->validation_errors['categories']);
            }
        }

        $images = $this->getItemImages();

        if (empty($images)) {
            $this->validation_errors['images'] = 'Add at least one image';
        }

    }

}