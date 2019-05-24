<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Name extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#whats_your_name#}?',array('style' => 'steps_bigtitle'));

        $this->layout->scroll[] = $this->components->getHintedField(
            '{#first_name#}',
            'firstname',
            'text'
            //,array('activation' => 'initially')
        );

        $this->layout->scroll[] = $this->components->getHintedField(
            '{#last_name#}',
            'lastname',
            'text',
            array(
                'submit_menu_id' => 'step1'
            )
        );


        $text = strtoupper($this->model->localize('{#sign_up#}'));
        $onclick = $this->getOnclickSubmit('name');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }