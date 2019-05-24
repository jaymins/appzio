<?php

namespace packages\actionMshopping\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMshopping\Components\Components;
use packages\actionMshopping\Models\Model as ArticleModel;

class Events extends BootstrapView
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