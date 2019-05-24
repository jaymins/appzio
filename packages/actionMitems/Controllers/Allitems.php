<?php

namespace packages\actionMitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

class Allitems extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point.
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();

        $items = $this->model->getUserItems();

        return ['Allitems', array(
            'items' => $items
        )];
    }
}