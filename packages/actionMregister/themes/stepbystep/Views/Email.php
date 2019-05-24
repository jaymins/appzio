<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Email extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#what_is_your_email#}?',array('style' => 'steps_bigtitle'));
        $this->layout->scroll[] = $this->components->getHintedField(
            '{#email#}',
            'email',
            'text'
            ,array('input_type' => 'email',
                'submit_menu_id' => 'email'
            )
        );

        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('email');

        $this->layout->scroll[] = $this->components->getTerms();
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }