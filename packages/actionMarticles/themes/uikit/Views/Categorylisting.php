<?php

namespace packages\actionMarticles\themes\uikit\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\uikit\Components\Components;
use packages\actionMarticles\Models\Model as ArticleModel;

class Categorylisting extends BootstrapView {

	/* @var ArticleModel */
	public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$categories = $this->getData('categories', 'array');
		$parent_category = $this->getData('parent_category', 'object');
		
		$title = strtoupper( '{#knowledge_base#}' );
		$onclick = $this->getOnclickOpenAction('home', false, array(
		    'transition' => 'back'
        ));

		if ( $parent_category AND isset($parent_category->title) ) {
			$title = strtoupper( $parent_category->title );

			$onclick = $this->getOnclickOpenAction('materialscategorylisting',false,
				array(
					'id' => 'list-categories-home',
					'sync_open' => 1,
					'sync_close' => 1,
					'back_button' => 1,
                    'transition' => 'back',
				));
		}

		$this->layout->header[] = $this->components->uiKitTopbar('arrow-back-white-v2.png', $title, $onclick, array(
			'background-color' => $this->color_top_bar_color
		));

		if ( empty($categories) ) {
			$this->layout->scroll[] = $this->getComponentText('{#no_categories_yet#}', array('style' => 'article-uikit-error'));
			return $this->layout;
		}

		$params = [];
		
		if ( count($categories) < 3 ) {
			$params['box_ratio'] = 2;
		}

		foreach ( $categories as $category ) {
			$this->layout->scroll[] = $this->components->uiKitArticleCategoryItem($category, $params);
		}

		return $this->layout;
	}

}