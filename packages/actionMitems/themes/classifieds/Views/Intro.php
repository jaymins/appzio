<?php

namespace packages\actionMitems\themes\classifieds\Views;
use packages\actionMitems\Views\Create as BootstrapView;
use packages\actionMitems\themes\classifieds\Components\Components;

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
