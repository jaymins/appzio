<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\Model as ArticleModel;

class Imagepreview extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $data = [];

        $this->model->rewriteActionConfigField('background_color', '#000000');

        $image = $this->getMenuId();

        if ( !empty($image) ) {
            $this->model->sessionSet('current_image_preview', $image);
        } else {
            $image = $this->model->sessionGet('current_image_preview');
        }

        $data['image'] = $image;

        return ['Imagepreview', $data];
    }

}