<?php

namespace packages\actionMarticles\themes\uikit\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\uikit\Components\Components;
use packages\actionMarticles\Models\Model as ArticleModel;

class Listing extends BootstrapView {

	/* @var ArticleModel */
	public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public $video_divs = array();

	public function tab1() {
		$this->layout = new \stdClass();

		$articles = $this->getData('articles', 'array');
		$category_data = $this->getData('category_data', 'object');

		$title = '';
		if ( $category_data AND isset($category_data->title) ) {
			$title = strtoupper( $category_data->title );
		}

		$this->layout->header[] = $this->components->uiKitTopbar('arrow-back-white-v2.png', $title, false, array(
			'background-color' => $this->color_top_bar_color
		));

		if ( isset($category_data->description) ) {
			$this->layout->scroll[] = $this->getComponentText($category_data->description, array(), array(
				'font-size' => '18',
				'padding' => '10 15 10 15',
			));
		}

		if ( empty($articles) ) {
			$this->layout->scroll[] = $this->getComponentText('{#no_articles_yet#}', array('style' => 'article-uikit-error'));
			return $this->layout;
		}

		foreach ( $articles as $article ) {

			if ( $article->link ) {

			    $div_id = 'video-article-' . $article->id;

				$this->video_divs[$div_id] = $this->components->uiKitVideoDiv(array(
					'video_url' => $article->link,
					'title' => $article->title,
					'div_id' => $div_id,
				));
			}

			$this->layout->scroll[] = $this->components->uiKitArticleItem( $article, $category_data );
			$this->layout->scroll[] = $this->getComponentDivider(array(
				'style' => 'article-uikit-divider'
			));
		}

		return $this->layout;
	}

	public function getDivs() {
		return $this->video_divs;
	}

}