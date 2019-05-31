<?php

namespace packages\actionDitems\themes\demo\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Liked extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();
        $items = $this->model->getAllItems(1,false,true);
        $bookmarks = $this->model->getMyLikedItemsArray();

        return ['Listing', array(
            'items' => $items,
            'bookmarks' => $bookmarks
        )];
    }
}