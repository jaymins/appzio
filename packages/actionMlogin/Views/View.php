<?php
namespace packages\actionMlogin\Views;

use Bootstrap\Views\BootstrapView;

class View extends BootstrapView
{
    public $model;
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText(' view');

        return $this->layout;
    }
}
