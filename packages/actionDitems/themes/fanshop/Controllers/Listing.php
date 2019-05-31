<?php

namespace packages\actionDitems\themes\fanshop\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Listing extends \packages\actionDitems\Controllers\Listing
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $this->model->setBackgroundColor();
        $items = $this->model->getAllItems(1,true);
        $bookmarks = $this->model->getMyLikedItemsArray();

        return ['Listing', array(
            'items' => $items,
            'bookmarks' => $bookmarks
        )];
    }

}