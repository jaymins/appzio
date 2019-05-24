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

class MobilebookingController extends ArticleController {

    public $configobj;
    public $theme;
    public $has_available_hours = true;

    public $gamevariables;

    public function tab1(){

        $this->gamevariables = CHtml::listData(Aevariable::model()->findAllByAttributes(array('game_id' => $this->gid),array('order'=>'name ASC')), 'id','name');

        $data = new StdClass();

        $trigger = 'registration_phase';

        if ( !isset($this->varcontent[$trigger]) ) {
            $this->saveActionVars(array(), 'booking-1');
        }

        $this->handleSubmissions();

        $step = $this->varcontent[$trigger];

        switch ($step) {
            case 'booking-1':
                // Step 1
                $data->scroll = $this->bookingStep1();

                $footer[] = $this->getTextbutton('CONTINUE', array('submit_menu_id' => 'mobilebooking_step_1', 'style' => 'submit-button', 'id' => 'mobilebooking_step_1'));

                $data->footer = $footer;
                break;

            case 'booking-2':
                // Step 2
                $data->scroll = $this->bookingStep2();
                break;

            case 'booking-3':
                // Step 3
                $data->scroll = $this->bookingStep3();

                if ( $this->has_available_hours ) {
                    $footer[] = $this->getTextbutton('CONTINUE', array('submit_menu_id' => 'mobilebooking_step_3', 'style' => 'submit-button', 'id' => 'mobilebooking_step_3'));
                } else {
                    $footer[] = $this->getTextbutton('CHOOSE A DIFFERENT DATE', array('submit_menu_id' => 'mobilebooking_step_3_b', 'style' => 'submit-button', 'id' => 'mobilebooking_step_3_b'));
                }

                $data->footer = $footer;

                break;

            case 'booking-4':
                // Step 4
                $data->scroll = $this->bookingStep4();
                $footer[] = $this->getTextbutton('CONFIRM BOOKING', array('submit_menu_id' => 'mobilebooking_step_4', 'style' => 'submit-button', 'id' => 'mobilebooking_step_4'));
                $data->footer = $footer;
                break;

            case 'booking-5':
                // Step 5
                $data->scroll = $this->bookingStep5();

                if ( isset($this->menuid) AND $this->menuid === 'mobilebooking_step_4' ) {
                    $footer[] = $this->getTextbutton('THANK YOU', array('submit_menu_id' => 'mobilebooking_step_5', 'style' => 'submit-button', 'id' => 'mobilebooking_step_5', 'action' => 'go-home'));
                } else {
                    $footer[] = $this->getTextbutton('CANCEL BOOKING', array('submit_menu_id' => 'mobilebooking_step_5', 'style' => 'submit-button', 'id' => 'mobilebooking_step_5'));
                }

                $data->footer = $footer;
                break;

            case 'booking-6':
                // Step 6 - Cancel the bookings
                $data->scroll = $this->bookingStep6();
                $footer[] = $this->getTextbutton('CANCEL BOOKING', array('submit_menu_id' => 'mobilebooking_step_6', 'style' => 'submit-button', 'id' => 'mobilebooking_step_6', 'action' => 'complete-action'));
                $data->footer = $footer;
                break;
        }

        return $data;
    }

    private function bookingStep1() {

        $output = array();

        $output[] = $this->getImage('booking-heading-step-1.png', array( 'style' => 'booking-top-image' ));

        $content = 'Haircut / Hairdressing;Haircut / Hairdressing; Haircut with Coloring;Haircut with Coloring; Wedding / Special Occasion;Wedding / Special Occasion;Other;Other';
        $output[] = $this->getFieldlist($content, array( 'variable' => 'appointment_type', 'style' => 'field-list-style' ));

        return $output;
    }

    private function bookingStep2() {
        $output = array();

        $output[] = $this->getImage('booking-heading-step-2.png', array( 'style' => 'booking-top-image' ));

        $menu_items = array(
            'mobilebooking_book_this_week' => 'btn-this-week.png',
            'mobilebooking_book_next_week' => 'btn-next-week.png',
            'mobilebooking_book_later_week' => 'btn-later.png',
        );

        foreach ($menu_items as $key => $image) {
            $output[] = $this->getImagebutton($image, $key, false, array('style' => 'menu-style-list') );
        }

        return $output;
    }

    private function bookingStep3() {
        $output = array();

        $booking_period = $this->varcontent['booking_period'];

        $day = '';
        $time = '';

        if ( $booking_period == 'This week' ) {
            $offset = Helper::getTimezoneOffset('Europe/London','Europe/Sofia');
            $now = time() + $offset;
            $day = date( 'l', $now );

            // This would return +1 hour of our current time.
            // This way we could be sure that there wouldn't be any issues with the appointment
            $time = date( 'H', $now );
        }

        $days = $this->getAvailableDays( $day );
        $hours = $this->getAvailableHours( $time );

        if ( empty($days) || empty($hours) ) {

            if ( empty($days) ) {
                $na_text = 'Unfortunately, there aren\'t any available hours for this week. Please choose a different date.';
            } else if ( empty($hours) ) {
                $na_text = 'Please select a different day.';
            }

            $output[] = $this->getText($na_text, array( 'style' => 'booking-no-hours' ));

            $this->has_available_hours = false;

            return $output;
        }

        $output[] = $this->getText('', array('style' => 'booking-spacer'));
        // $output[] = $this->getImage('available-days.png', array('style' => 'booking-image'));
        
        $output[] = $this->getRow(array(
                        $this->getImage('icon-calendar.png', array( 'style' => 'booking-heading-image' )),
                        $this->getText('Available Days', array( 'style' => 'booking-heading-text' )),
                    ), array( 'style' => 'booking-heading' ));

        $output[] = $this->getFieldlist($days, array( 'variable' => 'appointment_day', 'style' => 'field-list-style' ));

        // $output[] = $this->getImage('available-hours.png', array('style' => 'booking-image'));
        $output[] = $this->getRow(array(
                        $this->getImage('icon-clock-main.png', array( 'style' => 'booking-heading-image' )),
                        $this->getText('Available Hours', array( 'style' => 'booking-heading-text' )),
                    ), array( 'style' => 'booking-heading' ));

        $output[] = $this->getFieldlist($hours, array( 'variable' => 'appointment_hour', 'style' => 'field-list-style' ));

        return $output;
    }

    private function bookingStep4() {

        $output = $this->getBookingInfo();
        $output[] = $this->getFieldtextarea('', array( 'style' => 'booking-submit-review', 'hint' => 'Type your message here...', 'variable' => 'appointment_notes' ));

        return $output;
    }

    private function bookingStep5() {
        
        $output = $this->getBookingInfo();
        $output[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText('{{appointment_notes}}', array( 'style' => 'booking-submit-review-inner' ))
            )),
        ), array( 'style' => 'booking-submit-review-text' ));

        return $output;
    }

    private function bookingStep6() {
        $output = array();

        $output[] = $this->getImage('image-cancel-booking.jpg', array('style' => 'booking-image'));
        $output[] = $this->getText('Are you sure you want to cancel your booking? Once you clear your booking, you would be able to book again.', array( 'style' => 'booking-text-cancel' ));

        // Revert the state of the booking, so the user doesn't always end up here
        $this->saveActionVars(array(), 'booking-5');

        return $output;
    }

    private function getBookingInfo() {
        $output = array();

        $content_left = $this->getColumn(array(
                $this->getImage( 'icon-clock.png' )
            ), array( 'style' => 'row-booking-info-left' ));

        $content_right = $this->getColumn(array(
            $this->getText('Date:', array( 'style' => 'row-booking-info-right-heading' )),
            $this->getText('{{booking_period}} - {{appointment_day}}, {{appointment_hour}}', array( 'style' => 'row-booking-info-right-data' )),
        ), array( 'style' => 'row-booking-info-right' ));

        $output[] = $this->getRow(
            array( $content_left, $content_right ),
            array( 'style' => 'row-booking-info' )
        );

        // Second column
        $content_left = $this->getColumn(array(
                $this->getImage( 'icon-scissors.png' )
            ), array( 'style' => 'row-booking-info-left' ));

        $content_right = $this->getColumn(array(
            $this->getText('Appointment Type', array( 'style' => 'row-booking-info-right-heading' )),
            $this->getText(trim('{{appointment_type}}'), array( 'style' => 'row-booking-info-right-data-2' )),
        ), array( 'style' => 'row-booking-info-right' ));

        $output[] = $this->getRow(
            array( $content_left, $content_right ),
            array( 'style' => 'row-booking-info' )
        );

        $output[] = $this->getText('Notes', array( 'style' => 'booking-notes-heading' ));

        return $output;
    }

    public function handleSubmissions() {
        if ( $this->menuid == 'mobilebooking_step_1' ) {
            $variables = array(
                'appointment_type' => array_search('appointment_type', $this->gamevariables)
            );
            $this->saveActionVars($variables, 'booking-2');
        }

        $menu_items = array(
            'mobilebooking_book_this_week' => 'This week',
            'mobilebooking_book_next_week' => 'Next week',
            'mobilebooking_book_later_week' => '+2 weeks',
        );

        foreach ($menu_items as $mi_key => $mi_value) {
            if ( $this->menuid == $mi_key ) {
                AeplayVariable::updateWithId($this->playid, array_search('booking_period', $this->gamevariables), $mi_value);
                $this->saveActionVars(array(), 'booking-3');
            }
        }

        if ( $this->menuid == 'mobilebooking_step_3' ) {
            $variables = array(
                'appointment_day' => array_search('appointment_day', $this->gamevariables),
                'appointment_hour' => array_search('appointment_hour', $this->gamevariables),
            );
            $this->saveActionVars($variables, 'booking-4');
        }

        if ( $this->menuid == 'mobilebooking_step_3_b' ) {
            $this->saveActionVars(array(), 'booking-2');
        }

        if ( $this->menuid == 'mobilebooking_step_4' ) {
            
            $localizationComponent = new Localizationapi();
            $localizationComponent->initLocalization($this->playid,$this->gid,$this->varcontent);

            $booking_data = array(
                'booking_period'    => '{{booking_period}}',
                'appointment_day'   => '{{appointment_day}}',
                'appointment_hour'  => '{{appointment_hour}}',
                'appointment_type'  => '{{appointment_type}}',
                'name'              => '{{name}}',
                'phone'             => '{{phone}}',
                'time'              => '{{time()}}',
            );

            // Store the processed values
            $tmp_booking_info = array();

            foreach ($booking_data as $booking_var => $booking_value) {
                $tmp_booking_info[$booking_var] = $localizationComponent->smartLocalize($booking_value);
            }

            AeplayVariable::updateWithId($this->playid, array_search('complete_booking_info', $this->gamevariables), serialize($tmp_booking_info), false, $this->gid);
            $variables = array(
                'appointment_notes' => array_search('appointment_notes', $this->gamevariables),
            );
            $this->saveActionVars($variables, 'booking-5');
        }

        if ( $this->menuid == 'mobilebooking_step_5' ) {
            $this->saveActionVars(array(), 'booking-6');
        }

        $this->loadVariableContent();
    }

    public function saveActionVars($variables, $reg_phase = false) {
        $data = array();

        if ( !empty($variables) ) {
            foreach ($variables as $key => $var) {
                if ( isset($this->submitvariables[$var]) AND !empty($this->submitvariables[$var]) ) {
                    $data[$key] = $this->submitvariables[$var];
                }
            }
        }

        if ( $reg_phase ) {
            $data['registration_phase'] = $reg_phase;
        }
    
        AeplayVariable::saveVariablesArray($data,$this->playid,$this->gid,'normal');
    }

    private function getAvailableDays( $day ) {

        $days = array(
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'
        );

        if ( !empty($day) ) {

            if ( $day == 'Saturday' || $day == 'Sunday' ) {
                return false;
            }

            $index = array_search($day, $days);
            $days = array_slice($days, $index);
        }

        $result = '';

        foreach ($days as $i => $day) {
            $result .= $day . ';';
            $result .= $day . ( $i+1 != count($days) ? ';' : '' );
        }

        return $result;
    }

    private function getAvailableHours( $time ) {

        $hours = array(
            '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'
        );

        /*
        if ( !empty($time) ) {
            $time = $time . ':00';

            // No available hours
            if ( $time > end($hours) ) {
                return false;
            }

            $index = array_search($time, $hours);
            $hours = array_slice($hours, $index);
        }
        */

        $result = '';

        foreach ($hours as $i => $hour) {
            $result .= $hour . ';';
            $result .= $hour . ( $i+1 != count($hours) ? ';' : '' );
        }

        return $result;
    }

}