<?php

namespace packages\actionMproducts\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMproducts\Controllers\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Searchresults extends View {

    /* @var \packages\actionMproducts\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $products = $this->getData('products', 'array');

        $this->layout->header[] = $this->components->getSearchBox('term','categories');
        $string = $this->model->localize('{#search_results#} {#for#} ');
        $this->layout->scroll[] = $this->getComponentText($string .$this->getData('searchterm', 'string'),array('style' => 'mproducts_list_title','uppercase' => true));

        if(!$products){
            $this->layout->scroll[] = $this->getComponentText(
                '{#no_results#}',
                array('style' => 'mproducts_list_title'));
        }

        foreach($products as $product){
            $this->layout->scroll[] = $this->components->getProductListItem($product);
        }

        return $this->layout;
    }
}
