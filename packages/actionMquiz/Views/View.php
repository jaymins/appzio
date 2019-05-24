<?php

namespace packages\actionMquiz\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMquiz\Components\Components;
use packages\actionMquiz\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentText(__FILE__);
        return $this->layout;
    }

}
