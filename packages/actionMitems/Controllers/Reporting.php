<?php

namespace packages\actionMitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

class Reporting extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $this->model->reportItem();
        $this->no_output = true;
    }
}