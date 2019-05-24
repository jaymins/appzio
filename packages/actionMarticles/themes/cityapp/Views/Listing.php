<?php

namespace packages\actionMarticles\themes\cityapp\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\cityapp\Components\Components;
use packages\actionMarticles\Models\ArticlesModel as Article;
use packages\actionMarticles\Models\Model as ArticleModel;

class Listing extends BootstrapView {

	/* @var ArticleModel */
	public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        $articles = $this->getData('articles', 'mixed');

        if ( empty($articles) ) {
            // To do: render a no articles message
            $this->layout;
        }

        foreach ($articles as $article) {
            $this->layout->scroll[] = $this->getBlogItem($article);
        }

		return $this->layout;
	}

    private function getBlogItem(Article $article)
    {

        $image = $this->getBlogItemPhoto($article->photos);
        $date = date('d.m.Y', strtotime($article->article_date));

        $main_layout = new \stdClass();
        $main_layout->width = $this->screen_width - 30;
        $main_layout->height = 200;
        $main_layout->top = 0;
        $main_layout->center = 0;

        return $this->getComponentColumn([
            $this->getComponentImage($image, [
                'overlay' => [
                    $this->getComponentImage('cityapp-cover.png', [
                        'layout' => $main_layout
                    ], [
                        'width' => $this->screen_width,
                        'height' => 200,
                        'crop' => 'yes',
                    ]),
                    $this->getComponentColumn([
                        $this->getComponentText($article->title, [], [
                            'width' => '70%',
                            'font-size' => '20',
                            'font-weight' => 'bold',
                            'padding' => '20 0 0 20',
                            'color' => '#ffffff',
                        ]),
                        $this->getComponentText($date . ' • ' . $article->header, [], [
                            'width' => '100%',
                            'font-size' => '15',
                            'padding' => '0 20 20 20',
                            'color' => '#ffffff',
                            'text-align' => 'right',
                            'floating' => '1',
                            'vertical-align' => 'bottom',
                        ]),
                    ], [
                        'layout' => $main_layout
                    ], [
                        'width' => $this->screen_width,
                        'height' => 200,
                    ]),
                ]
            ], [
                'width' => $this->screen_width - 30,
                'height' => 200,
                'crop' => 'yes',
            ])
        ], [
            'onclick' => $this->getOnclickOpenAction('blogarticle', false, [
                'id' => $article->id,
                'back_button' => 1,
                'sync_open' => 1
            ])
        ], [
            'width' => 'auto',
            'height' => 200,
            'border-radius' => 5,
            'margin' => '0 15 20 15',
        ]);
    }

    private function getBlogItemPhoto($photos)
    {

        foreach ($photos as $image) {
            if ( $image->position == 'featured' )
                return $image->photo;
        }

        return 'cityapp-article-placeholder.jpg';
    }

}