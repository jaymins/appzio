<?php

namespace packages\actionDitems\themes\fanshop\Views;
use packages\actionDitems\Views\Create as BootstrapView;
use packages\actionDitems\themes\fanshop\Components\Components;

class Intro extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;


    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getIntroScreen();
        $this->layout->overlay[] = $this->components->getIntroScreenOverlay();
        return $this->layout;


    }



}
