<?php

/*
    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.
*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionListappointments.models.*');

class ListappointmentsController extends ArticleController {

    public $configobj;
    public $data;
    public $branch_id;
    public $action_id;


    /* this gets called when doing branchlisting */
    public function getData(){

        $data = new StdClass();
        $data->header = array();
        $data->scroll = $this->getScroll();

        return $data;
    }


    private function getScroll(){

        $bookings = $this->getFutureBookings();
        $future_bookings = $bookings['bookings'];
        $appts_today = $bookings['appts_today'];
            
        $output = array();

        $output[] = $this->getImage( 'image-appointment.png' );

        $today = time();

        if ( $appts_today ) {
            $count_customers = count($appts_today);
            $customers_text = ( $count_customers > 1 ? 'Customers' : 'Customer' );
            $latest_appoitment = array_pop( $appts_today );

            $stamp = $latest_appoitment['time'];

            $date = new DateTime( date( 'M-d-Y ' . $stamp ) );
            $interval = new DateInterval('PT1H');
            $date->add($interval);
            $est_time = $date->format('H:i');
            $appts_text = $count_customers . ' ' . $customers_text . '. Finish at ' . $est_time;
        } else {
            $appts_text = 'No appointments today';
        }

        $heading_left = $this->getColumn(array(
                $this->getText( date( 'j' ), array( 'style' => 'top_row_left_inner' ) )
            ), array( 'style' => 'top_row_left' ));
        $heading_right = $this->getColumn(array(
                $this->getText( strtoupper(date('M')) . ', ' . date('Y'), array( 'style' => 'trri_1' ) ),
                $this->getText( ucfirst(date('l')), array( 'style' => 'trri_2' ) ),
            ), array( 'style' => 'top_row_right' ));

        $output[] = $this->getRow(array(
                $heading_left, $heading_right
            ), array( 'style' => 'top_row_main' ));

        $output[] = $this->getText( $appts_text, array( 'style' => 'top_row_sub' ) );

        if ( empty($future_bookings) ) {
            // No future bookings
            return $output;
        }

        $count = 0;

        foreach ($future_bookings as $timestamp => $booking_info) {
            
            $count++;

            $class = 'even';
            if ( $count % 2 == 0 ) {
                $class = 'odd';
            }

            // Create a properly formatted date
            $day = date( 'd', $timestamp );
            $day_from_week = date( 'D', $timestamp );

            $row_content_left = $this->getColumn( 
                array(
                    $this->getText( $day, array( 'style' => 'mrl_1' ) ),
                    $this->getText( strtoupper($day_from_week), array( 'style' => 'mrl_2' ) ),
                ),
                array( 'style' => 'main_row_left_' . $class )
            );


            $item_rows = array();

            $booking_info = $this->sortArrayByValue( $booking_info );

            foreach ($booking_info as $info) {                
                $rr_left = $this->getColumn(array(
                    $this->getText( $info['time'], array( 'style' => 'main_row_right_inner_left' ))
                ), array( 'width' => '20%' ));

                $rr_middle = $this->getColumn(array(
                    $this->getText( $info['name'], array( 'style' => 'mrri_1' ) ),
                    $this->getText( $info['type'], array( 'style' => 'mrri_2' ) ),
                ), array( 'style' => 'main_row_right_inner_middle' ));

                // Note: The menuid contains the needed *play_id*, so we could make the query based on it
                $rr_right = $this->getColumn(array(
                    $this->getImagebutton( 'bubble.png', 'playid-' . $info['play_id'], false, array( 'action' => 'open-action', 'config' => $this->configobj->chat_action_id, 'style' => 'main_row_right_inner_right', 'open_popup' => 1, 'sync_open' => 1 ) )
                ), array( 'width' => '15%', ));
                
                $item_rows[] = $this->getRow( array( $rr_left, $rr_middle, $rr_right ), array( 'style' => 'main_row_right_inner' ) );

                $row_content_right = $this->getColumn( $item_rows, array( 'style' => 'main_row_right' ) );
            }


            $output[] = $this->getRow(
                array( $row_content_left, $row_content_right ),
                array( 'style' => 'appointments_main_row' )
            );

        }

        return $output;
    }


    private function getFutureBookings() {

        $model = new ListappointmentsModel();
        $model->gid = $this->gid;

        $bookings = $model->getBookingInfo();

        if ( empty($bookings) ) {
            return false;
        }

        $start_of_day = strtotime( 'today midnight' );

        $tmp_appointments = array();
        $appointments_today = array();

        foreach ($bookings as $booking) {
            $value = unserialize($booking['value']);

            $week   = $value['booking_period'];
            $day    = $value['appointment_day'];
            $time   = $value['appointment_hour'];
            $type   = $value['appointment_type'];
            $name   = $value['name'];
            $phone  = $value['phone'];
            $stamp  = $value['time']; // This is the time, when the booking was originally made

            $tmp_day = $week . $day;
            $booking_stamp = strtotime($tmp_day, $stamp);

            if ( $booking_stamp >= $start_of_day ) {

                // Today
                if ( date('Ymd', $start_of_day) == date('Ymd', $booking_stamp ) ) {
                    $appointments_today[] = array(
                        'stamp' => $booking_stamp,
                        'time'  => $time,
                    );
                }

                $tmp_appointments[] = array(
                    'stamp'     => $booking_stamp,
                    'time'      => $time,
                    'type'      => $type,
                    'name'      => $name,
                    'phone'     => $phone,
                    'play_id'   => $booking['play_id'],
                );
            }
        }

        $future_bookings = array();

        foreach($tmp_appointments as $key => $item) {
           $future_bookings[$item['stamp']][$key] = $item;
        }

        // Sort the keys
        ksort($future_bookings, SORT_NUMERIC);

        return array(
            'bookings' => $future_bookings,
            'appts_today' => $appointments_today,
        );
    }


    private function sortArrayByValue( $array ) {
        $result = $array;
        array_multisort($result, SORT_ASC, $array);
        return $result;
    }


}