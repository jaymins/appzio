<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusBookingHeader {


    public function getNexudusBookingHeader(){
        /** @var BootstrapComponent $this */
        return $this->getComponentText('Booking header');
    }

}
