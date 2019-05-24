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

class Adultspopup extends BootstrapView {

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
        $this->model->rewriteActionConfigField('hide_menubar', 1);

        $parameters['title'] = '{#choose_adult#}';
        $parameters['mode'] = 'close';
        $parameters['btn_title'] = false;

        $this->layout->header[] = $this->components->getFauxTopbar($parameters);

        $adults = $this->getData('adults', 'array');

        if($adults){
            foreach($adults as $adult){
                $this->layout->scroll[] = $this->components->getAdultRow($adult,false,false,false,array('close' => true));
            }
        }

        return $this->layout;
    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
