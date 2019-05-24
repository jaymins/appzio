<?php

namespace packages\actionMitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\Models\ItemCategoryModel;

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