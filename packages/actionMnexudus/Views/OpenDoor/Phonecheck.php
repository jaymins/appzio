<?php

namespace packages\actionMnexudus\Views\OpenDoor;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Phonecheck extends BootstrapView
{

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $phone = $this->model->getSavedVariable('opendoor_phone');

        $this->layout->scroll[] = $this->getComponentFullPageLoaderAnimated(['color' => '#ffffff', 'text' => '{#verifying_your_phone_number#}']);

        $this->layout->onload[] = $this->getOnclickDoorflowAuthenticate([
            'phone' => $phone,
            'id' => 'verification/verificate/',
            'variable_error' => 'authenticate_error',
            'variable_status' => 'authenticate_status',
            'variable_description' => 'authenticate_description'
        ]);

        return $this->layout;

    }


}
