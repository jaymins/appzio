<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMswipematch\Controllers\Components;
use stdClass;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView {

    /* @var \packages\actionMregister\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        return $this->layout;
    }


}