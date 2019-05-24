<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Createnew extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#create_a_new_account#}?',array('style' => 'steps_bigtitle'));

        $text = strtoupper($this->model->localize('{#create_new_account#}'));
        $onclick =  $this->getOnclickSubmit('createnew');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }