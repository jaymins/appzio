<?php

namespace packages\actionMarticles\themes\cityapp\Controllers;

use packages\actionMarticles\themes\cityapp\Views\Listing as ArticleView;
use packages\actionMarticles\themes\cityapp\Models\Model as ArticleModel;

class Listing extends \packages\actionMarticles\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

	/**
	 * This is the default action inside the controller. This function gets called, if
	 * nothing else is defined for the route
	 * @return array
	 */
	public function actionDefault(){
		$data['articles'] = $this->model->getArticles('id', false);
		return ['Listing', $data];
	}

}