<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Username extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#pick_a_username#}?',array('style' => 'steps_bigtitle'));
        $this->layout->scroll[] = $this->components->getComponentText('{#this_is_how_your_friends_will_find_you#}!',array('style' => 'steps_smalltitle'));
        $this->layout->scroll[] = $this->components->getHintedField(
            '{#username#}',
            'username',
            'text'
            ,array('activation' => 'initially')
        );

        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('username');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }