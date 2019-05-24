<?php

namespace packages\actionMbooking\themes\tattoo\Components;

use Bootstrap\Components\BootstrapComponent;

trait BookingTimePopup
{
    public function bookingTimeDiv(string $timestamp, int $bookingId)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentText('{#change_time#}', array(), array(
                    'color' => '#ffffff',
                    'text-align' => 'center',
                    "padding" => "20 20 20 20",
                ))
            ), array(), array(
                'width' => '100%',
                'background-color' => '#1b1b1b',
                'text-align' => 'center'
            )),
        $this->getComponentColumn(array(
            $this->getDatePicker($timestamp),
            $this->getTimePicker($timestamp),
            $this->getComponentSpacer(30),
        ), array(
//            'style' => 'booking_note_div'
        ), array(
            'background-color' => '#ffffff',
            'padding' => '20 15 20 15',
            'color' => '#000000',
            'font-size' => '18',
            'border-radius' => '3',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
        )),
        $this->getSubmitButton($bookingId)
        ), array(), array());
    }

    protected function getDatePicker($timestamp)
    {
        return $this->uiKitHintedCalendar('Date', 'date', $timestamp, array(
            'active_icon' => 'calendar_black.png',
            'inactive_icon' => 'calendar_black.png'
        ), array(
            'margin' => '0 0 10 0'
        ));
    }

    protected function getTimePicker($timestamp)
    {
        return $this->uiKitHintedTime(date('H', $timestamp), date('i', $timestamp));
    }

    protected function getSubmitButton(int $bookingId)
    {
        $onclick = [];
        $onclick[] = $this->getOnclickSubmit('Controller/change/' . $bookingId);
        $onclick[] = $this->getOnclickHideDiv('change-booking-time-' . $bookingId);
        $onclick[] = $this->getOnclickOpenAction('singlebook', false, array(
            'id' => $bookingId,
            'sync_open' => 1
        ));

        return $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#change_time#}'), array(
                'onclick' => $onclick,
//                    array(
//                    $this->getOnclickSubmit('Controller/change/' . $bookingId),
//                    $this->getOnclickHideDiv('change-booking-time-' . $bookingId)
//                ),
                'style' => 'booking_button'
            ), array(
                "background-color" => "#ea8041",
                "text-align" => "center",
                "color" => "#ffffff",
                "padding" => "20 0 20 0",
                "font-weight" => "bold",
//                "marign" => "10 10 10 10",
                "border-radius" => "3",
                "width" => "100%"
            ))
        ), array(), array(
            'width' => '100%',
            'text-align' => 'center'
        ));
    }
}