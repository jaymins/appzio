<?php

namespace packages\actionMproducts\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMproducts\Controllers\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Categorylist extends View {

    /* @var \packages\actionMproducts\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $categories = $this->getData('categories', 'array');
        $featured = $this->getData('featured', 'array');

        $this->layout->scroll[] = $this->getComponentText('{#showroom#}',array('style' => 'mproducts_list_title','uppercase' => true));

        $this->layout->header[] = $this->components->getSearchBox('term');
        $this->layout->scroll[] = $this->components->getFeaturedProductsSwiper($featured);

        $this->layout->scroll[] = $this->getComponentText('{#categories#}',array('style' => 'mproducts_list_title','uppercase' => true));

        foreach($categories as $category){
            $this->layout->scroll[] = $this->components->getCategoryListItem($category);
        }

        return $this->layout;
    }
}
