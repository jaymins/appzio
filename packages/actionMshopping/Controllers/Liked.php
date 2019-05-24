<?php

namespace packages\actionMshopping\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMshopping\Models\Model as ArticleModel;

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
        $liked = $this->model->getLikedItems();

        return ['Liked', array(
            'liked' => $liked
        )];
    }
}