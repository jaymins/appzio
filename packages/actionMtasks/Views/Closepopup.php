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

class Closepopup extends BootstrapView {

    /* @var \packages\actionMtasks\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getIntroText('{#saving#}', '{#saving_the_form_content#}');
        $this->layout->onload[] = $this->getOnclickClosePopup();
        return $this->layout;

    }




    /* if view has getDivs defined, it will include all the needed divs for the view */


}
