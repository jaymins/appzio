<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\cityapp\Controllers;

use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\cityapp\Models\Model as ArticleModel;

class Category extends \packages\actionMitems\Controllers\Controller {

    /* @var ArticleView */
    public $view;

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

        $category_id = $this->getMenuId();

        if ( empty($category_id) )
            $category_id = 14;

        $category_data = $this->model->getItemsByCategory( $category_id );

        $data['category_data'] = $category_data;

        return ['Category', $data];
    }

}