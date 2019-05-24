<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Complete extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentSpacer(80);
        $this->layout->scroll[] = $this->getComponentFullPageLoaderAnimated(['color' => '#ffffff','text' => '{#hold_tight#}, {#logging_you_in#}...']);
        $this->layout->onload[] = $this->getOnclickCompleteAction();
        return $this->layout;
    }

}
