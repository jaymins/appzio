<?php

namespace packages\actionMregister\themes\stepbystep\Views;
use packages\actionMregister\Views\View as BootstrapView;
use packages\actionMregister\themes\stepbystep\Components\Components;
use function strtoupper;

class Photo extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->getComponentText('{#please_add_a_profile_photo#}',array('style' => 'steps_bigtitle'));
        $this->layout->scroll[] = $this->components->getPhotoField('mreg_collect_photo');

        $text = strtoupper($this->model->localize('{#continue#}'));
        $onclick =  $this->getOnclickSubmit('photo');
        $this->layout->scroll[] = $this->components->getComponentText($text,array('style' => 'steps_btn','onclick' => $onclick));

        return $this->layout;

    }



    }