<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;

class View extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfitness\Components\Components
     */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }


    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentText('fitness view');
        return $this->layout;
    }

    public function getDivs()
    {
        $divs = new \stdClass();

        return $divs;
    }

}