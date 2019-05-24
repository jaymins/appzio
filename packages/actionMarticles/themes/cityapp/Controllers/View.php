<?php

namespace packages\actionMarticles\themes\cityapp\Controllers;

use packages\actionMarticles\themes\cityapp\Views\View as ArticleView;
use packages\actionMarticles\themes\cityapp\Models\Model as ArticleModel;

class View extends \packages\actionMarticles\Controllers\Controller {

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

		$article = $this->model->getArticle();
		
		$next_article = (object) [];
		$category_data = (object) [];
		
		$data['article_map'] = $this->getArticleMap();
		$data['article'] = $article;
		$data['next_article'] = $next_article;
		$data['category_data'] = $category_data;

		return ['View', $data];
	}

}