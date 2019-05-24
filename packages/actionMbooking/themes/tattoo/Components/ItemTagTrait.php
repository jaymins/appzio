<?php
namespace packages\actionMbooking\themes\tattoo\Components;

use Bootstrap\Components\BootstrapComponent;

trait ItemTagTrait{

    public function getBookTattooItemTag($text)
    {
        return $this->getComponentText($text, array(), array(
            'color' => '#000000',
            'margin' => '0 0 0 5',
        ));
    }

    public function getBookTattooItemCategory($text)
    {
        return $this->getComponentText($text, array(), array(
            'color' => '#000000',
            'padding' => '5 10 5 10',
            'background-color' => '#f6bb33',
            'border-radius' => '5',
            'margin' => '5 5 0 0',
            'font-size' => '12'
        ));
    }

}