<?php

namespace packages\actionMregister\themes\stepbystep\Views;
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
        $text1 = $this->getData('text1', 'string');
        $text2 = $this->getData('text2', 'string');
        $this->layout->scroll[] = $this->components->getComponentText($text1,array('style' => 'steps_bigtitle'));
        $this->layout->scroll[] = $this->components->getComponentText($text2,array('style' => 'steps_smalltitle'));
        $this->layout->onload[] = $this->getOnclickCompleteAction();
        return $this->layout;
    }

}