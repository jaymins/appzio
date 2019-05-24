<?php

namespace packages\actionMtasks\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMtasks\Controllers\Components;
use stdClass;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Tasklist extends BootstrapView {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        return $this->phase1();
    }

    public function phase1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getTaskListChild($this->data);
        return $this->layout;
    }

    public function tab2(){
        $layout = new stdClass();

        if($this->model->getMenuId() != 'new'){
            return $this->tab1();
        }

        $layout->header[] = $this->components->getFauxTopbar();
        $page[] = $this->components->getAddAdult();

        $layout->scroll[] = $this->getComponentColumn($page,array(),array('width' => $this->screen_width,
            'background-color' => '#EFEFEF'));

        return $layout;
    }

    public function addForm(){

    }


    /* if view has getDivs defined, it will include all the needed divs for the view */


}
