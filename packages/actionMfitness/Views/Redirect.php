<?php

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;

class Redirect extends BootstrapView
{

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $redirect = $this->getData('redirect', 'string');
        $this->layout->onload[] = $this->getOnclickOpenAction($redirect);
        return $this->layout;
    }

}