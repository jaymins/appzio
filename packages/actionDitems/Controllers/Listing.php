<?php

namespace packages\actionDitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Listing extends BootstrapController
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

        return ['Listing', array(
            'items' => $items
        )];
    }
}