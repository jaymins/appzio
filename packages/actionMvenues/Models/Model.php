<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMvenues\Models;
use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;
    public $output;
    public $editId;

    public function validateVenue(){

    }

    public function saveVenue($update=false)
    {


        $rawdata = $this->sessionGet('venue_raw_address');
        $name = $this->getSubmittedVariableByName('venue_name');
        $address = $this->getSubmittedVariableByName('venue_address');
        $photo = $this->getSavedVariable('venue_photo');
        $description = $this->getSubmittedVariableByName('description');
        $phone = $this->getSubmittedVariableByName('venue_phone');

/*        if($rawdata['address'] != $address){
            $coordinates = \ThirdpartyServices::addressToCoordinates($this->appid, false,false,$address);
        }*/

        if($update){
            $obj = VenuesModel::model()->findByPk($this->sessionGet('venueid'));
            if(is_object($obj)){
                $obj->name = $name;
                $obj->game_id = $this->appid;
                $obj->playid = $this->playid;
                $obj->address = $address;
                // $obj->headerimage1 = $photo;
                $obj->phone = $phone;
                $obj->info = $description;
                $obj->update();
            }
        } else {
            $obj = new VenuesModel();
            $obj->name = $name;
            $obj->game_id = $this->appid;
            $obj->playid = $this->playid;
            $obj->address = $address;
            $obj->lat = $rawdata['lat'];
            $obj->lon = $rawdata['lon'];
            $obj->headerimage1 = $photo;
            $obj->phone = $phone;
            $obj->info = $description;
            $obj->insert();
        }


        $this->saveVariable('venue_photo', '');
        $this->sessionSet('venue_raw_address', '');
        $this->sessionSet('venueid', '');

        return true;

    }

    public function getVenue($venueid)
    {
        return VenuesModel::model()->findByPk($venueid);
    }


    public function getFootballClubs() {
       return VenuesModel::model()->findAll('type = :type ORDER BY name ASC', array(
            ':type' => 'club'
        ));
    }

}