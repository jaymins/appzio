<?php

namespace packages\actionMproducts\themes\example\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMproducts\Components\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Product extends View {

    /* @var \packages\actionMproducts\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->setTopShadow();
        $products = $this->getData('product', 'array');
        $this->layout->scroll[] = $this->components->getProductPage($this->getData('product_info', 'object'));
        $product = $this->getData('product_info', 'object');

        $text = strtoupper($this->model->localize('{#add_to_cart#}'));

        $onclick = $this->components->getOnclickOpenAction(
            'cart',
            false,
            array('sync_open' => 1,'open_popup' => 1),
            'Cart/addtocart/'.$product->id,
            true,
            array('productid' => $product->id));

        $this->layout->footer[] = $this->components->getComponentText($text,array('style' => 'add_to_cart','onclick' => $onclick));

        return $this->layout;
    }




}
