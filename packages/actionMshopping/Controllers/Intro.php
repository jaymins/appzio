<?php

namespace packages\actionMshopping\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMshopping\Models\Model as ArticleModel;
use packages\actionMshopping\Models\ItemCategoryModel;

class Intro extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        if ($this->model->getSavedVariable('logged_in')) {
            $view = 'Intro';
        } else {
            $view = 'AppIntro';
        }

        return [$view, []];
    }

}