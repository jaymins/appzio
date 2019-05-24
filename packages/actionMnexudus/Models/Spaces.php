<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

Trait Spaces {
    

    public function getBooths(){

        if($this->sessionGet('booth_data')){
            //return $this->sessionGet('booth_data');
        }

        $booking_data = $this->sessionGet('booking_data');
        $locations = $this->ApiGetLocations();

        $output['spaces'] = array();

        foreach($locations as $location){
            if($location['Id'] == $booking_data['location']){
                $this->api_endpoint = $location['HomeUrl'].'/';
                $spaces = $this->ApiGetSpaces();
                $output['spaces'] = $spaces;
            }
        }

        $date = date('Y-m-d',$booking_data['date']);
        $bookings = $this->ApiGetBookings($date,$date);

        if($bookings){
            foreach($bookings as $booking){
                $id = $booking['resourceId'];
                $start = str_replace('T', '', $booking['start']);
                $start = str_replace('Z', '', $start);
                $start = strtotime($start);

                $end = str_replace('T', '', $booking['end']);
                $end = str_replace('Z', '', $end);
                $end = strtotime($end);
                $length = $end - $start;
                $output['bookings'][$id][$start] = $length;
            }
        } else {
            $output['bookings'] = array();
        }

        $this->sessionSet('booth_data', $output);
        return $output;
    }

    public function getSpaceName($id){
        $spaces = $this->ApiGetSpaces();

        foreach ($spaces as $space) {
            if($space['Id'] == $id){
                return $space['Name'];
            }
        }

        return false;
    }

    public function getBusinessName($id){
        $spaces = $this->ApiGetLocations();

        foreach ($spaces as $space) {
            if($space['Id'] == $id){
                $this->current_space = $space;
                return str_replace('MyWorkBooth - ', '', $space['Name']);
            }
        }

        return false;
    }

    public function ApiGetSpaceselectorData(){

        if($this->sessionGet('locations')){
            return $this->sessionGet('locations');
        }

        $spaces = $this->ApiGetLocations();

        $output = ";;";

        foreach ($spaces as $space) {
            $name = str_replace('MyWorkBooth - ', '', $space['Name']);
            $output .= $space['Id'] .';'.$name .';';
        }

        $output = substr($output, 0,-1);

        $this->sessionSet('locations', $output);
        return $output;

    }

    public function getHourSelectorData(){
        return ';;9;09;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18';
    }

    public function getMinuteSelectorData(){
        return ';;0;00;15;15;30;30;45;45';
    }

    public function getLengthSelectorData(){
        return ';;15;15min;30;30min;45;45min;60;1h;75;1h 15min;90min;1h 30min;110;1h 45min;120;2h';
    }





}
