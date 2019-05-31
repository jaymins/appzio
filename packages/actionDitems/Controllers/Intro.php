<?php

namespace packages\actionDitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;
use packages\actionDitems\Models\ItemCategoryModel;

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