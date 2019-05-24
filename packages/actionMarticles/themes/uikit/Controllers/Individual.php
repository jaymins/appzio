<?php

namespace packages\actionMarticles\themes\uikit\Controllers;

use packages\actionMarticles\themes\uikit\Views\View as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;

class Individual extends \packages\actionMarticles\Controllers\Controller {

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

		$data = [];
        $article = (object) [];
        $category_data = (object) [];

		$article_id = $this->model->getConfigParam('article_id');

		if ( $article_id ) {
            $article = $this->model->getArticle($article_id);

            if ( !empty($article) ) {
                $this->model->category_id = $article->category_id;
                $category_data = $this->model->getCategoryInfo();
            }
        }

        if ( $this->model->getConfigParam('hide_menubar') == 1 ) {
            $data['show_false_back_arrow'] = true;
        }

        $data['article_map'] = $this->getArticleMap();
		$data['article'] = $article;
		$data['next_article'] = (object) [];
		$data['category_data'] = $category_data;

		return ['View', $data];
	}

}