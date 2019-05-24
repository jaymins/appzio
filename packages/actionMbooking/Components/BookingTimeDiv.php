<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;

trait BookingTimeDiv
{
    public function bookingTimeDiv(string $timestamp, int $bookingId)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentColumn(array(
            $this->getDatePicker($timestamp),
            $this->getTimePicker($timestamp),
            $this->getComponentSpacer(30),
            $this->getSubmitButton($bookingId)
        ), array(
            'style' => 'booking_note_div'
        ));
    }

    protected function getDatePicker($timestamp)
    {
        /** @var BootstrapComponent $this */
        return $this->uiKitHintedCalendar('Date', 'date', $timestamp, array(
            'active_icon' => 'calendar-icon.png',
            'inactive_icon' => 'calendar-icon.png'
        ), array());
    }

    protected function getTimePicker($timestamp)
    {
        return $this->uiKitHintedTime(date('H', $timestamp), date('i', $timestamp));
    }

    protected function getSubmitButton(int $bookingId)
    {
        return $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#change_time#}'), array(
                'onclick' => array(
                    $this->getOnclickSubmit('Controller/change/' . $bookingId),
                    $this->getOnclickHideDiv('change-booking-time-' . $bookingId)
                ),
                'style' => 'booking_button'
            ))
        ), array(), array(
            'width' => '100%',
            'text-align' => 'center'
        ));
    }
}