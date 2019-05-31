<?php

namespace packages\actionDitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Components\Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Addevent extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1() {
        $this->layout = new \stdClass();

        return $this->layout;
    }

}