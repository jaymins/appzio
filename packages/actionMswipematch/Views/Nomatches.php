<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Nomatches extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $checked_in = $this->getData('checked_in', 'mixed');

        if($checked_in){
            $this->layout->header[] = $this->getComponentText('{#checked_in#} '.$checked_in);
        }

        $this->layout->scroll[] = $this->getComponentText('This view should be shown if there aren\'t any new matches 111', array(), array(
            'padding' => '10 10 10 10',
            'text-align' => 'center',
        ));

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }

}