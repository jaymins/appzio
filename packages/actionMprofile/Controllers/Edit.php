<?php

namespace packages\actionMprofile\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMprofile\Views\Edit as ArticleView;
use packages\actionMprofile\Models\Model as ArticleModel;

class Edit extends BootstrapController
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $this->model->rewriteActionConfigField('background_color', '#f6f6f6');

        $data = array();

        if ($this->getMenuId() == 'save-profile') {
            $this->model->saveProfile();
            $data['saved'] = true;
        }

        return ['Edit', $data];
    }

}
