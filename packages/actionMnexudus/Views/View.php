<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class View extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentText('Default View');
        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
