<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Password extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#choose_a_password#}',array('style' => 'steps_bigtitle'));

        $this->layout->scroll[] = $this->components->getHintedField(
            '{#password#}',
            'pass1',
            'password'
            ,array('activation' => 'initially')
        );

        $this->layout->scroll[] = $this->components->getHintedField(
            '{#password_again#}',
            'pass2',
            'password'
        );

        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('password');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }