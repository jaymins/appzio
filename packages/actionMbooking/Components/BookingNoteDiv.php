<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;

trait BookingNoteDiv
{
    public function bookingNoteDiv(string $notes)
    {
        /** @var BootstrapComponent $this */
        return $this->getComponentText($notes, array(
            'style' => 'booking_note_div'
        ));
    }
}