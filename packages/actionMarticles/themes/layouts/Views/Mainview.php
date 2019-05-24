<?php

namespace packages\actionMarticles\themes\layouts\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\layouts\Components\Components;
use packages\actionMarticles\Models\Model as ArticleModel;

class Mainview extends BootstrapView {

	/* @var ArticleModel */
	public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

	public function tab1() {
		$this->layout = new \stdClass();

		$this->model->flushActionRoutes();

		$list_items = [
		    'Controller/SimpleGrid' => 'Simple Grid',
		    'Controller/ExtendedGrid' => 'Extended Grid',
		    'Controller/Checkboxes' => 'Checkboxes & Radio Buttons',
		    'Controller/Overlay' => 'Overlay Images',
		    'Controller/Swipers' => 'Swipers',
        ];

        foreach ($list_items as $route => $list_item) {
            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText($list_item, [], [
                    'padding' => '10 15 10 15',
                    'width' => 'auto',
                    'text-align' => 'center',
                    'border-width' => '1',
                    'border-color' => '#cccccc',
                    'border-radius' => '5',
                    'background-color' => '#eaeaea',
                    'color' => '#333333',
                ])
            ], [
                'onclick' => $this->getOnclickRoute($route, true)
            ], [
                'margin' => '10 15 0 15',
            ]);
		}

		return $this->layout;
	}

    public function getElements( $count = 4 ) {

	    $elements = [
	        [
	            'title' => 'Element 1',
	            'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'image-1.jpg'
            ],
            [
                'title' => 'Element 2',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'image-2.jpg'
            ],
            [
                'title' => 'Element 3',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'image-3.jpg'
            ],
            [
                'title' => 'Element 4',
                'description' => 'Lorem Ipsum is simply dummy text',
                'image' => 'image-4.jpg'
            ],
        ];

	    return array_slice($elements, 0, $count);
    }

}