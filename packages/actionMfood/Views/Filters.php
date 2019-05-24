<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfood\Views;

use Bootstrap\Views\BootstrapView;


class Filters extends BootstrapView {

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfood\Components\Components
     */
    public $components;
    public $theme;

    public function tab1(){
        $this->layout = new \stdClass();
        return $this->layout;
    }
}