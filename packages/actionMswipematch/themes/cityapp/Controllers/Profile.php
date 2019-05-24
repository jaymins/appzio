<?php

namespace packages\actionMswipematch\themes\cityapp\Controllers;

use packages\actionMswipematch\themes\cityapp\Models\Model as ArticleModel;

class Profile extends \packages\actionMswipematch\Controllers\Controller {

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $data = [];

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $profile_id = 639;

        // To do: perhaps we would need a limit?
        $data['art_items'] = $this->model->getItemsByAuthor( $profile_id );

        return ['Profile', $data];
    }

}