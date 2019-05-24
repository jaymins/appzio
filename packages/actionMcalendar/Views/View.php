<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMcalendar\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMcalendar\Models\Model;


class View extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMcalendar\Components\Components
     */
    public $components;
    public $theme;

    /* @var Model */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        return $this->layout;
    }



}