<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMbooking\Models\BookingModel;

trait BookingCardImage
{
    public function bookingCardImage(string $image)
    {
        /** @var BootstrapComponent $this */

        return $this->getComponentImage($image, array(
            'style' => 'booking_card_image'
        ));
    }
}