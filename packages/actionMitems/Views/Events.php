<?php

namespace packages\actionMitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components;
use packages\actionMitems\Models\Model as ArticleModel;

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