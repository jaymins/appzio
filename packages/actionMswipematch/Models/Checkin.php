<?php

namespace packages\actionMswipematch\Models;

use Bootstrap\Models\BootstrapModel;

use CException;
use Yii;


Trait Checkin
{

    public $placename;
    public $placeaddress;
    public $lat;
    public $lon;
    public $checked_in;

    public function checkoutFromVenue()
    {
        $this->deleteVariable('current_venue');
    }

    public function checkForUpdateLocation(){
        $update_time = $this->sessionGet('location_update_time');

        if(!$update_time OR $update_time+60 < time()){
            $this->sessionSet('location_update_time', time());
            return true;
        }

        return false;

    }

    public function getMyAddress()
    {


        if($this->getSavedVariable('country')){
            $output = '';

            if($this->getSavedVariable('street')){
                $output .= $this->getSavedVariable('street') .', ';
            }

            if($this->getSavedVariable('street')){
                $output .= $this->getSavedVariable('citry') .', ';
            }

            $output .= $this->getSavedVariable('country') .', ';
            return $output;
        }

        $address = $this->coordinatesToAddress($this->getSavedVariable('lat'), $this->getSavedVariable('lon'));

        if (isset($address['street'])) {
            return $address['street'];
        }

        if (isset($address['city'])) {
            return $address['city'];
        }

        if (isset($address['county'])) {
            return $address['county'];
        }

        if (isset($address['country'])) {
            return $address['country'];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getPlacesNearby()
    {
        $places = $this->findClosestVenues($this->getSavedVariable('lat'), $this->getSavedVariable('lon'));
        //$cachename = 'local-venue-cache-' . $this->playid;

        if ($places) {
            foreach ($places as $place) {
                $arr = array();
                $arr['rating'] = isset($place['rating']) ? $place['rating'] : '0';
                $arr['place_id'] = isset($place['place_id']) ? $place['place_id'] : '0';
                $arr['name'] = isset($place['name']) ? $place['name'] : '0';
                $arr['icon'] = isset($place['icon']) ? $place['icon'] : '0';
                $arr['address'] = isset($place['vicinity']) ? $place['vicinity'] : '';
                $output[] = $arr;
            }
        }

        if (isset($output)) {
/*            \Appcaching::setGlobalCache($cachename, $places);*/
            return $output;
        }

        return array();

    }


    /**
     * @param bool $id
     * @return bool
     */
    public function checkinToVenue($id = false)
    {

        if ($id) {
            $cachename = 'local-venue-cache-' . $this->playid;
            $places = \Appcaching::getGlobalCache($cachename);

            if (!$places) {
                $places = $this->getPlacesNearby();
            }

            foreach ($places AS $place) {
                if ($place['place_id'] == $id) {
                    $this->placename = $this->getParam('name', $place);
                    $this->lat = $this->getParam('lat', $place['geometry']['location']);
                    $this->lon = $this->getParam('lng', $place['geometry']['location']);

                    if(!$this->lon){
                        $this->lon = $this->getParam('lon', $place['geometry']['location']);
                    }

                    $this->placeaddress = $this->getParam('vicinity', $place);
                    $this->deleteVariable('venue_temp');
                    $this->saveVariable('current_venue', $this->placename);
                    $this->saveVariable('venue_lat', $this->lat);
                    $this->saveVariable('venue_lon', $this->lon);
                    $this->saveVariable('venue_address', $this->placeaddress);

                    if($this->getConfigParam('update_user_location') AND !empty($this->lat) AND !empty($this->lon)){
                        $this->saveVariable('lat', $this->lat);
                        $this->saveVariable('lon', $this->lon);
                    }

                    $this->checked_in = true;
                    return true;
                }
            }

            return false;
        }

        if ($this->getSavedVariable('venue_temp')) {
            $data = @json_decode($this->getSavedVariable('venue_temp'), true);

            if (is_array($data) AND !empty($data)) {
                $this->placename = $this->getParam('name', $data);
                $this->lat = $this->getParam('lat', $data);
                $this->lon = $this->getParam('lon', $data);
                if(!$this->lon){
                    $this->lon = $this->getParam('lng', $data);
                }
                $this->placeaddress = $this->getParam('address', $data);

                $this->deleteVariable('venue_temp');
                $this->saveVariable('current_venue', $this->placename);
                $this->saveVariable('venue_lat', $this->lat);
                $this->saveVariable('venue_lon', $this->lon);
                $this->saveVariable('venue_address', $this->placeaddress);
                $this->checked_in = true;

                if($this->getConfigParam('update_user_location') AND !empty($this->lat) AND !empty($this->lon)){
                    $this->saveVariable('lat', $this->lat);
                    $this->saveVariable('lon', $this->lon);
                }

                $this->loadVariableContent(true);
            }

            return true;
        }

        return false;
    }


    public function setPlaceSearch()
    {
        if ($this->getSubmittedVariableByName('venue_temp')) {
            $data = @json_decode($this->getSubmittedVariableByName('venue_temp'), true);

            if (is_array($data) AND !empty($data)) {
                $this->placename = $this->getParam('name', $data);
                $this->lat = $this->getParam('lat', $data);
                $this->lon = $this->getParam('lon', $data);
                $this->placeaddress = $this->getParam('address', $data);
                $this->checked_in = false;
                $this->saveVariable('venue_temp', json_encode($data));
            }
        }
    }

    public function setCheckin($skipsubmit = false)
    {

        if($this->getSavedVariable('venue_temp')){
            $data = @json_decode($this->getSavedVariable('venue_temp'),true);

            if (is_array($data) AND !empty($data)) {
                $this->placename = $this->getParam('name', $data);
                $this->lat = $this->getParam('lat', $data);
                $this->lon = $this->getParam('lon', $data);
                $this->placeaddress = $this->getParam('address', $data);
                $this->checked_in = false;
                $this->saveVariable('venue_temp', $data);
            }
        }elseif ($this->getSavedVariable('current_venue')) {
            $this->placename = $this->getSavedVariable('current_venue');
            $this->lat = $this->getSavedVariable('venue_lat');
            $this->lon = $this->getSavedVariable('venue_lon');
            $this->placeaddress = $this->getSavedVariable('venue_address');
            $this->checked_in = true;
        }


        /*if ($this->getSavedVariable('current_venue') AND $this->getSavedVariable('venue_lat')
            AND $this->getSavedVariable('current_venue') == $this->getSubmittedVariableByName('venue_temp')
        ) {
            $this->placename = $this->getSavedVariable('current_venue');
            $this->lat = $this->getSavedVariable('venue_lat');
            $this->lon = $this->getSavedVariable('venue_lon');
            $this->placeaddress = $this->getSavedVariable('venue_address');
            $this->checked_in = true;
        } elseif ($this->getSubmittedVariableByName('venue_temp')) {
            $data = $this->getSubmittedVariableByName('venue_temp');
            $this->saveVariable('venue_temp', $data);

            $data = @json_decode($data, true);

            if (is_array($data) AND !empty($data)) {
                $this->placename = $this->getParam('name', $data);
                $this->lat = $this->getParam('lat', $data);
                $this->lon = $this->getParam('lon', $data);
                $this->placeaddress = $this->getParam('address', $data);
            }
        } elseif ($this->getSavedVariable('venue_temp')) {
            $data = @json_decode($this->getSavedVariable('venue_temp'), true);

            if (is_array($data) AND !empty($data)) {
                $this->placename = $this->getParam('name', $data);
                $this->lat = $this->getParam('lat', $data);
                $this->lon = $this->getParam('lon', $data);
                $this->placeaddress = $this->getParam('address', $data);
            }

        } else {
            $this->lat = $this->getSavedVariable('lat');
            $this->lon = $this->getSavedVariable('lon');
            $address = $this->coordinatesToAddress($this->lat, $this->lon);
            $this->placename = $this->findClosestVenue($this->lat, $this->lon);

            if (isset($address['street'])) {
                $city = isset($address['city']) ? ', ' . $address['city'] : '';
                $display_address = $address['street'] . $city;
            } else {
                $display_address = '';
            }

            $this->placeaddress = $display_address;
        }

        if (!$this->lat AND $this->getSavedVariable('lat')) {
            $this->lat = $this->getSavedVariable('lat');
            $this->lon = $this->getSavedVariable('lon');
        } elseif (!$this->lat OR $this->lat === 0) {
            $this->lat = 42.3;
            $this->lon = 42.3;
        }*/


    }

    public function getCountryList()
    {
        $countrycodes = $this->getCountryCodes();
        $list = '';

        foreach ($countrycodes as $name => $code){
            $list .= $name .';' .$name .';';
        }

        $list = substr($list, 0,-1);

        return $list;
    }

    public function getCityList($country=false){

        if(!$country){
            return array();
        }

        $path = Yii::getPathOfAlias('application.modules.aelogic.packages.actionMobileregister2.sql');
        $file = $path .'/countriesToCities.json';
        $cities = file_get_contents($file);
        $cities = json_decode($cities,true);
        $list = '';

        if(isset($cities[$country])){
            asort($cities[$country]);

            foreach($cities[$country] as $city){
                $list .= $city .';' .$city .';';
            }

            $list = substr($list, 0,-1);

            return $list;
        }

        return array();

    }

    public function setNewAddress()
    {
        $city = $this->getSubmittedVariableByName('city_selected');
        $country = $this->getSavedVariable('temp_country');
        $coordinates = $this->addressToCoordinates($country,$city);

        if(isset($coordinates['lat']) AND isset($coordinates['lon']) AND $coordinates['lat'] AND $coordinates['lon']){
            $this->saveVariable('lat', $coordinates['lat']);
            $this->saveVariable('lon', $coordinates['lon']);
            $this->saveVariable('city', $city);
            $this->saveVariable('country', $country);
            $this->deleteVariable('temp_country');
        } else {
            echo('FAIL');die();
        }

    }


}