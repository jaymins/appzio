<?php

namespace packages\actionMarticles\themes\uikit\Controllers;

use packages\actionMarticles\themes\uikit\Views\Categorylisting as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;

class Controller extends \packages\actionMarticles\Controllers\Controller {

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

		$this->model->rewriteActionConfigField('background_color', '#ffffff');
		$this->model->rewriteActionConfigField('hide_subject', 1);
		$this->model->rewriteActionConfigField('hide_menubar', 1);

		$data = [];
		$data['parent_category'] = (object) [];
		
		$categories = $this->model->getCategories( false, 0 );
		
		if ( $this->model->getCategoryId() AND $this->model->getCategoryId() != 'list-categories-home' ) {
			$categories = $this->model->getCategories( $this->model->getCategoryId() );
			$data['parent_category'] = $this->model->getCategoryInfo();
		}

		$data['categories'] = $categories;

		return ['Categorylisting', $data];
	}

}