<?php

namespace packages\actionMsubscription\themes\pttrader\Views;

use packages\actionMsubscription\themes\pttrader\Components\Components;
use packages\actionMsubscription\Views\Forward as BootstrapView;

class Forward extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->onload[] = $this->getOnclickOpenAction('home');
        return $this->layout;
    }

}