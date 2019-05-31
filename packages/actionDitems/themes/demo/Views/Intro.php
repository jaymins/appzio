<?php

namespace packages\actionDitems\themes\demo\Views;
use packages\actionDitems\Views\Create as BootstrapView;
use packages\actionDitems\themes\demo\Components\Components;

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
        $message = $this->getData('message', 'string');

        //if(!empty($message)){
            //$this->layout->header[] = $this->getComponentText('Hello');
       // }

        $this->layout->scroll[] = $this->components->getIntroScreen();
        $this->layout->overlay[] = $this->components->getIntroScreenOverlay();
        return $this->layout;


    }



}
