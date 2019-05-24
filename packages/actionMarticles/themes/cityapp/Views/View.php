<?php

namespace packages\actionMarticles\themes\cityapp\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\cityapp\Components\Components;
use packages\actionMarticles\Models\Model as ArticleModel;

class View extends BootstrapView {

	/* @var ArticleModel */
	public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

	public function tab1() {

		$this->layout = new \stdClass();

		$map = $this->getData('article_map', 'array');
		$article = $this->getData('article', 'object');
		$next_article = $this->getData('next_article', 'object');
		$category_data = $this->getData('category_data', 'object');

		if ( !isset($article->content) ) {
			$this->layout->scroll[] = $this->getComponentText('{#missing_article#}', array('style' => 'article-uikit-error'));
			return $this->layout;
		}

		$fields = @json_decode( $article->content, true );

		if ( empty($fields) ) {
			$this->layout->scroll[] = $this->getComponentText('{#missing_fields#}', array('style' => 'article-uikit-error'));
			return $this->layout;
		}

        $show_false_back_arrow = false;
		
		if ( $this->getData('show_false_back_arrow', 'mixed') )
            $show_false_back_arrow = true;

		$this->layout->scroll[] = $this->components->uiKitArticleHeading( $article, $category_data, $show_false_back_arrow );
		
		foreach ( $fields as $field_data ) {

			$field_type = $field_data['type'];
			$field_attributes = ( isset($map[$field_type]) ? $map[$field_type] : false );

			if ( empty($field_attributes) OR !isset($field_attributes['component']) ) {
				continue;
			}

			$params = [];
			foreach ( $field_attributes['attributes'] as $attribute ) {

			    if ( !isset($field_data[$attribute]) )
			        continue;

				$params[$attribute] = $field_data[$attribute];
			}

			// Handle Image IDs
			if ( isset($params['image_id']) ) {
				$params['image_id'] = $this->model->getImageByID( $params['image_id'] );
			}

			// Handle Galleries
			if ( isset($params['ref']) ) {
				$params['images'] = $this->model->getGalleryImages( $params['ref'], $article->id );
			}

			$styles = [];
			if ( isset($field_data['styles']) AND is_array($field_data['styles']) ) {
				$styles = $field_data['styles'];
			}

			$this->layout->scroll[] = $this->{$field_attributes['component']}( $params, $styles );

		}

		if ( is_object($next_article) AND isset($next_article->title) ) {
			$this->layout->scroll[] = $this->uiKitArticleNextentry( $next_article, $category_data );
		}

		return $this->layout;
	}

}