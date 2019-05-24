<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMmessaging\Views;

use Bootstrap\Views\BootstrapView;

class Listing extends BootstrapView {

    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('This is a chat listing');

        return $this->layout;
    }

}