<?php

namespace packages\actionMarticles\themes\uikit\Controllers;

use packages\actionMarticles\themes\uikit\Views\View as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;

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

		$data = [];

		$article = $this->model->getArticle();
		$next_article = (object) [];
		$category_data = (object) [];

		if ( !empty($article) ) {
			$this->model->category_id = $article->category_id;

			$category_data = $this->model->getCategoryInfo();
			$next_article = $this->model->getNextArticle( $article->id );
		}
		
		$data['article_map'] = $this->getArticleMap();
		$data['article'] = $article;
		$data['next_article'] = $next_article;
		$data['category_data'] = $category_data;

		return ['View', $data];
	}

}