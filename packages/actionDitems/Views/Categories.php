<?php

namespace packages\actionDitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Categories extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public $theme;
    public $margin;
    public $grid;
    public $deleting;
    public $presetData;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Default action entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $categories = $this->getData('categories', 'array');
        $onclick = 'Categories/savecategory/';
        $this->layout->scroll[] = $this->uiKitHierarchicalCategories($categories,$onclick);
        return $this->layout;
    }


}
