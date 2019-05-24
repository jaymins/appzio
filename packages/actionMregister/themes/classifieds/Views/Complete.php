<?php

namespace packages\actionMregister\themes\classifieds\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Complete extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#registration_complete#}',array('style' => 'steps_bigtitle'));
        $this->layout->scroll[] = $this->components->getComponentText('{#logging_you_in#}',array('style' => 'steps_smalltitle'));
        $this->layout->onload[] = $this->getOnclickCompleteAction();
        return $this->layout;
    }

}