<?php

namespace packages\actionDitems\Controllers;

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
        $liked = $this->model->getLikedItems();

        return ['Liked', array(
            'liked' => $liked
        )];
    }
}