<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;

trait BookingCardInformation
{
    public function bookingCardInformation(string $bookingDate, $itemPrice)
    {
        /** @var BootstrapComponent $this */

        return $this->getComponentRow(array(
            $this->getComponentText(date('d M @ H:i', $bookingDate), array(
                'style' => 'booking_card_date'
            )),
        ), array(
            'style' => 'booking_card_row_wrapper'
        ));
    }
}