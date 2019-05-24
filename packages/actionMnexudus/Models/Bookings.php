<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMnexudus\Models;

Trait Bookings {


    public function getBooking($id)
    {

        $output['booking'] = [];
        $output['location'] = [];
        $output['space'] = [];

        if ($this->sessionGet('my_booking_' . $id)) {
            return $this->sessionGet('my_booking_' . $id);
        }

        $ret = $this->ApiGetBooking($id);

        if (!isset($ret['ResourceId'])) {
            return $output;
        }

        $resource_id = $ret['ResourceId'];
        $spaces = $this->ApiGetSpaces();

        if (!$spaces) {
            return $output;
        }

        foreach ($spaces as $row) {
            if ($row['Id'] == $resource_id) {
                $space = $row;
            }
        }

        if (!isset($space['BusinessId'])) {
            return $output;
        }

        $businessid = $space['BusinessId'];
        $locations = $this->ApiGetLocations();

        foreach ($locations as $loc) {
            if ($loc['Id'] == $businessid) {
                $location = $loc;
            }
        }

        if (!isset($location)) {
            return $output;
        }

        $output['doorid'] = false;

        if (isset($space['Description'])) {
            $description = html_entity_decode($space['Description']);

            if(stristr($description, '<doorid')){
                $first = substr($description,strpos($description, '<doorid="')+9);
                $doorid = substr($first, 0,strpos($first, '"'));
                if(is_numeric($doorid)){
                    $output['doorid'] = $doorid;
                }

                $space['Description'] = strip_tags($description);
            }
        }

        $output['booking'] = $ret;
        $output['location'] = $location;
        $output['space'] = $space;

        $this->sessionSet('my_booking_' . $id, $output, true);

        return $output;
    }

    public function deleteBooking($id)
    {
        $msg = $this->ApiDeleteBooking($id);

        if (isset($msg['Status']) AND $msg['Status'] == 500) {
            $this->validation_errors['delete_booking'] = $msg['Message'];
            return false;
        } else {
            return true;
            return $msg['Message'];
        }

    }

    public function updateTime(){
        $booking = $this->sessionGet('booking_data');
        $booking['hour'] = $this->getSubmittedVariableByName('hour');
        $booking['minute'] = $this->getSubmittedVariableByName('minute');
        $this->sessionSet('booking_data', $booking);
    }

    public function updateLength(){
        $booking = $this->sessionGet('booking_data');
        $booking['length'] = $this->getSubmittedVariableByName('length');
        $this->sessionSet('booking_data', $booking);
    }

    public function updateDate(){
        $booking = $this->sessionGet('booking_data');
        $booking['date'] = $this->getSubmittedVariableByName('date');
        $this->sessionSet('booking_data', $booking);
    }

    public function setBookingDataFromExistingBooking($id){

        $booking_info = $this->getBooking($id);

        $booking['location'] = $booking_info['location']['Id'];
        $time = strtotime($booking_info['booking']['FromTime']);
        $end_time = strtotime($booking_info['booking']['ToTime']);
        $length = ($end_time-$time)/60;

        $location_name = str_replace('MyWorkBooth - ', '', $booking_info['location']['Name']);
        $api = $booking_info['location']['HomeUrl'] .'/';

        $booking['date'] = $time;
        $booking['hour'] = date('G',$time);
        $booking['minute'] = date('i',$time);
        $booking['length'] = $length;
        $booking['location_name'] = $location_name;
        $booking['api_endpoint'] = $api;
        $booking['edit_id'] = $id;

        $this->sessionSet('booking_data', $booking);

    }

    public function bookingStep1(){
        $booking['location'] = $this->getSubmittedVariableByName('location');
        $booking['date'] = $this->getSubmittedVariableByName('date');
        $booking['hour'] = $this->getSubmittedVariableByName('hour');
        $booking['minute'] = $this->getSubmittedVariableByName('minute');
        $booking['length'] = $this->getSubmittedVariableByName('length');
        $booking['location_name'] = $this->getBusinessName($booking['location']);
        $booking['api_endpoint'] = isset($this->current_space['HomeUrl']) ? $this->current_space['HomeUrl'].'/' : $this->api_endpoint;

        $booking['hour'] = str_replace('am', '', $booking['hour']);
        $booking['hour'] = str_replace('pm', '', $booking['hour']);

        if(!$booking['location']){
            $this->validation_errors['location'] = '{#please_choose_the_location#}';
        }

        if(!$booking['date']){
            $this->validation_errors['date'] = '{#please_choose_the_date#}';
        }

        if(!$booking['hour']){
            $this->validation_errors['hour'] = '{#please_choose_the_hour#}';
        }

        if(!$booking['length']){
            $this->validation_errors['length'] = '{#please_choose_the_length#}';
        }

        if(!$this->validation_errors){
            $this->sessionSet('booking_data', $booking);

            $booths = $this->getBooths();

            if(isset($booths['spaces'][0]['Id'])){
                $booking['booth'] = $booths['spaces'][0]['Id'];
            }

            $this->sessionSet('booking_data', $booking);

            return true;
        }

        return false;
    }

    public function updateBooking(){
        $this->email = $this->getSavedVariable('email');
        $this->password = $this->getSavedVariable('password');

        $data = $this->sessionGet('booking_data');
        $time_stamps = $this->getDateStamps();

        if(!isset($data['edit_id'])){
            return false;
        }

        $payload = new \stdClass();
        $payload->Booking = new \stdClass();
        $payload->Booking->Resource = new \stdClass();
        $payload->Booking->Resource->Id = $data['booth'];
        $payload->Booking->Id = $data['edit_id'];
        $payload->Booking->FromTime = $time_stamps['from'];
        $payload->Booking->ToTime = $time_stamps['to'];

        $result = $this->ApiEditBooking($payload);

        if(isset($result['Status']) AND $result['Status'] == '500'){
            $this->validation_errors['booking'] = $result['Message'];
            return false;
        }

        return true;


    }

    private function getDateStamps(){
        $data = $this->sessionGet('booking_data');

        if(strlen($data['hour']) == 1){
            $hour = '0'.$data['hour'];
        } else {
            $hour = $data['hour'];
        }

        if($data['minute'] == 0){
            $minute = '00';
        } else {
            $minute = $data['minute'];
        }

        $date = date('Y-m-d',$data['date']);
        $from = $date.'T'.$hour.':'.$minute.'Z';
        $simple_from = $date.' '.$hour.':'.$minute;
        $simple_from = strtotime($simple_from);
        $to = date('Y-m-d G:i',$simple_from);

        $end = new \DateTime( $to);
        $end->add(new \DateInterval('PT'.$data['length'].'M'));
        $ending_date = $end->format("Y-m-d\\TH:i");
        $ending_date = $ending_date.'Z';

        return ['from' => $from,'to' => $ending_date];
    }

    public function bookingStep2(){
        $this->email = $this->getSavedVariable('email');
        $this->password = $this->getSavedVariable('password');
        $booth = $this->getMenuId();
        $data = $this->sessionGet('booking_data');
        $price = $data['length']/1.5;

        if(!isset($data['hour'])){
            return false;
        }

        if(!$this->payWithStripe($price)){
            $this->validation_errors['booking'] = '{#there_was_an_error_with_your_payment#}';
            return false;
        }

        $this->addLedger($price,$this->charge_id);
        
        $this->api_endpoint = $data['api_endpoint'];
        $stamps = $this->getDateStamps();
        $result = $this->createBooking($stamps['from'], $stamps['to'], $booth);

        if(isset($result['Status']) AND $result['Status'] == '500'){
            $this->validation_errors['booking'] = $result['Message'];
            return false;
        }

        if(isset($result['Status']) AND $result['Status'] == '200'){
            $this->sessionSet('_restlog', $this->log);
            return true;
        }
        

        $this->validation_errors['booking'] = '{#there_was_an_unknown_error_while_making_the_booking#}';
        return false;
    }


    public function getMyBookings(){
        $this->email = $this->getSavedVariable('email');
        $this->password = $this->getSavedVariable('password');
        $result = $this->ApiGetMyBookings();

        if(is_array($result) AND !empty($result)){
            foreach($result as $booking){
                $full_date = str_replace('T', ' ', $booking['start']);
                $full_date = str_replace('Z', '', $full_date);
                $full_date = strtotime($full_date);

                $full_date_end = str_replace('T', ' ', $booking['end']);
                $full_date_end = str_replace('Z', '', $full_date_end);
                $full_date_end = strtotime($full_date_end);

                $length = ($full_date_end - $full_date) / 60;

                $date = date('j.n.y',$full_date);
                $time = date('G:i',$full_date);

                $row['id'] = $booking['id'];
                $row['length'] = $length;
                $row['date'] = $date;
                $row['time'] = $time;
                $row['full_date'] = $full_date;
                $row['resourceName'] = $booking['resourceName'];
                $output[] = $row;
            }

/*            print_r($this->log);

            print_r($output);

            die();*/

            return $output;
        }

        return array();
    }

    public function getMyNextBooking(){
        $this->email = $this->getSavedVariable('email');
        $this->password = $this->getSavedVariable('password');
        $result = $this->ApiGetMyBookings();

        if(is_array($result) AND !empty($result)){
            foreach($result as $booking){
                $full_date = str_replace('T', ' ', $booking['start']);
                $full_date = str_replace('Z', '', $full_date);
                $full_date = strtotime($full_date);

                $full_date_end = str_replace('T', ' ', $booking['end']);
                $full_date_end = str_replace('Z', '', $full_date_end);
                $full_date_end = strtotime($full_date_end);

                $length = ($full_date_end - $full_date) / 60;

                $date = date('j.n.y',$full_date);
                $time = date('G:i',$full_date);

                $row['id'] = $booking['id'];
                $row['length'] = $length;
                $row['date'] = $date;
                $row['time'] = $time;
                $row['full_date'] = $full_date;
                $row['resourceName'] = $booking['resourceName'];
                $output[] = $row;
            }

            return $output[0];
        }

        return array();
    }




}
