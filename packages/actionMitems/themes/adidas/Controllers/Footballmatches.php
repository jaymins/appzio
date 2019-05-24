<?php

namespace packages\actionMitems\themes\adidas\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\adidas\Models\Model as ArticleModel;

class Footballmatches extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $data = [];

        $data['football_matches'] = $this->model->getFootballMatches();

        return ['Footballmatches', $data];
    }

}