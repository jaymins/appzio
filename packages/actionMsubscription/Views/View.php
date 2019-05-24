<?php

namespace packages\actionMsubscription\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMsubscription\Controllers\Components;
use packages\actionMsubscription\Models\PurchaseProductsModel;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView {

    /* @var \packages\actionMsubscription\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->setHeader();
        $this->setDbProducts();
        $this->setFooter();
        return $this->layout;
    }

    public function setDbProducts(){
        $products = $this->getData('products', 'array');

        if($products){
            foreach($products as $product){
                $this->renderProduct($product);
            }
        }
    }

    public function renderProduct(PurchaseProductsModel $product){
        $this->layout->scroll[] = $this->getComponentText($product->name,[],[
            'margin' => '15 15 0 15',
            'font-size' => '22',
            'font-weight' => 'bold',
            'color' => $this->color_text_color
        ]);
        $this->layout->scroll[] = $this->getComponentText($product->description,[],[
            'margin' => '15 15 15 15',
            'font-size' => '14',
            'color' => $this->color_text_color
        ]);

        $text = '› '.$product->name .' ('.$product->price.$product->currency.')';

        $onclick[] = $this->getOnclickPurchase($product->code_ios,$product->code_android,true);
        $onclick[] = $this->getOnclickSubmit('Controller/default/done');

        $this->setPurchaseButton($text,$onclick);

    }

    public function setPurchaseButton($text,$onclick){

        $this->layout->scroll[] = $this->getComponentText($text,[
            'onclick' => $onclick
        ],[
            'margin' => '0 15 15 15',
            'padding' => '8 15 8 15',
            'border-radius' => '8',
            'font-size' => '14',
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color
        ]);
    }


    public function setHeader($tab=1){
        $this->layout->header[] = $this->uiKitFauxTopBar(
            [
                'title' => '{#subscriptions#}',
                'mode' => 'gohome'
            ]
        );

        if($header = $this->getData('header_text', 'mixed')){
            $this->layout->header[] = $this->getComponentHtml($header, [], [
                'font-size' => '14',
                'font-ios' => 'OpenSans',
                'margin' => '15 15 0 15',
                'color' => $this->color_text_color,'opacity' => '0.8'
            ]);
        }
    }

    public function setFooter(){

        if($header = $this->getData('footer_text', 'mixed')){
            $this->layout->footer[] = $this->getComponentHtml($header, [], [
                'font-size' => '14',
                'font-ios' => 'OpenSans',
                'margin' => '15 15 0 15',
                'color' => $this->color_text_color,'opacity' => '0.8'
            ]);
        }

        $footer[] = $this->getComponentText('{#full_terms_and_conditions#}',
            [
                'onclick' => $this->getOnclickOpenAction('terms', false, ['open_popup' => 1, 'id' => 'popup', 'sync_open' => 1])
            ],
            [
                'color' => $this->color_text_color,
                'parent_style' => 'uikit_search_noresults'
            ]);

        $this->layout->footer[] = $this->getComponentColumn($footer, [], ['padding' => '0 0 10 0']);


    }



}
