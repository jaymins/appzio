<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;

trait ValidationErrorText
{
    public function validationErrorText($text)
    {
        /** @var BootstrapComponent $this */

        return $this->getComponentText($text, array(), array(
            'color' => '#ff0000',
            'text-align' => 'center'
        ));
    }
}