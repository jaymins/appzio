<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Phone extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#what_is_your_phone_number#}?',array('style' => 'steps_bigtitle'));

        $this->layout->scroll[] = $this->components->getPhoneNumberFieldStep($this->getData('country_code', 'string'));
        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('phone');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));
        return $this->layout;
    }



    }