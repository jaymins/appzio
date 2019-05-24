<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Matching extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $users = $this->getData('users', 'mixed');
        $this->layout->scroll[] = $this->components->uiKitUserSwiper($users);
        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();
        return $divs;
    }

}