<?php

namespace packages\actionMitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

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