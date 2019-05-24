<?php

namespace packages\actionMarticles\themes\uikit\Controllers;

use packages\actionMarticles\themes\uikit\Views\Listing as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;

class Listing extends \packages\actionMarticles\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

	public $category_id;
	public $category_data;

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

		$this->category_id = $this->model->getCategoryId();		
		$this->category_data = $this->model->getCategoryInfo();
		
		if ( count($this->model->getArticles()) == 1 ) {
			$article = $this->model->getArticles()[0];
			$next_article = $this->model->getNextArticle( $article->id );

			$data['article_map'] = $this->getArticleMap();
			$data['article'] = $article;
			$data['next_article'] = $next_article;
			$data['category_data'] = $this->category_data;

			return ['View', $data];
		}
		
		$data['articles'] = $this->model->getArticles();
		$data['category_data'] = $this->category_data;

		return ['Listing', $data];
	}

}