<?php

namespace packages\actionDitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait CategoryDescription
{
    protected function getCategoryDescription(string $description = '', string $style)
    {
        /** @var BootstrapComponent $this */

        return array_map(function($statement) use ($style) {
            return $this->getComponentText($statement, array('style' => $style));
        }, explode('|', $description));
    }
}
