<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Role extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#please_choose_whether_you_are_registering_as_a_parent_or_a_child#}?',array('style' => 'steps_bigtitle'));

        $onclick_parent =  $this->getOnclickSubmit('parent');
        $onclick_child =  $this->getOnclickSubmit('child');

        $this->layout->scroll[] = $this->components->getComponentText('{#i_am_a_parent#}',array('style' => 'role_btn','onclick' => $onclick_parent,'uppercase' => true));
        $this->layout->scroll[] = $this->components->getComponentText('{#i_am_a_child#}',array('style' => 'role_btn','onclick' => $onclick_child,'uppercase' => true));

        return $this->layout;

    }



    }