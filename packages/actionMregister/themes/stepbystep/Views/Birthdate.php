<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Birthdate extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#when_is_your_birthday#}?',array('style' => 'steps_bigtitle'));

        $this->layout->scroll[] = $this->components->getComponentFormFieldBirthday(array('format' => 'us'));

        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('birthdate');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }