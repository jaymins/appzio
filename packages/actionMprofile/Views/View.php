<?php

namespace packages\actionMprofile\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMprofile\Components\Components;

class View extends BootstrapView
{
    /* @var \packages\actionMprofile\Components\Components */
    public $components;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getProfileImages();

        $this->layout->scroll[] = $this->getProfileBody();

        return $this->layout;
    }

    protected function getProfileImages()
    {
        return $this->getComponentImage('anonymous.png');
    }

    protected function getProfileBody()
    {
        // To be implemented in theme classes
    }
}