<?php

namespace packages\actionMproducts\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMproducts\Controllers\Components;
use function stristr;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Cart extends View {

    /* @var \packages\actionMproducts\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        $isParent = $this->getData('isParent', 'int');
        $cart = $this->getData('cart', 'array');

        if(!$cart){
            $this->layout->scroll[] = $this->components->getIntroText(
                '{#empty_cart#}',
                '{#no_products_in_cart_yet#}'
            );
        } else {
            $this->layout->scroll[] = $this->components->getCartHeader($this->data);

        }

        $productParams = array();
        if ($isParent) {
            $productParams['no_controls'] = 1;
        }

        $link = '';
        $count = 1;

        foreach($cart as $product){
            $this->layout->scroll[] = $this->components->getProductListItem($product, $productParams);
/*          $productLinkParts = explode('/', $product->product->link);
            $productRef =  current(array_slice($productLinkParts, -2, 1));*/
            $link .= '&ASIN.' .$count.'='.$product->product->amazon_product_id.'&Quantity.'.$count.'='.$product->quantity;
            $count++;
        }

        $earnterImg[] = $this->getComponentImage('earnster_logo.png',array(),array('vertical-align'=>'bottom','floating' => 1));
        $this->layout->footer[] = $this->components->getComponentRow(
            $earnterImg,
            array('id' => 'bottom_earn_logo'),
            array('text-align' => "center", 'margin' => '20 0 0 0'));

        if($cart){
            $text = strtoupper($this->model->localize('{#earn_it#}'));

            if ($isParent) {
                $text = strtoupper($this->model->localize('{#checkout#}'));
                $url = 'https://www.amazon.com/gp/aws/cart/add.html?AWSAccessKeyId=AKIAJD3OUVUBJLVWSGVA&AssociateTag=arnster2017-20&linkCode=as2&tag=arnster2017-20';

                if($this->model->getSavedVariable('purchase_once') OR $this->model->getSavedVariable('purchase_yearly')){
                    $onclick[] = $this->getOnclickOpenUrl($url.$link);
                    $onclick[] = $this->components->getOnclickShowDiv('cancel_div',
                        array('background' => 'blur','tap_to_close' => true),
                        array('left' => '50','right' => '50','bottom' => $this->screen_height/2 - 200));
                    //$this->model->saveVariable('purchase_once', '');
                } else {
                    $this->model->saveVariable('tempurl', $url.$link);
                    $onclick = $this->getOnclickOpenAction('subscription');
                }

            } else {
                $onclick[] = $this->components->getOnclickOpenAction('addtask');
                $onclick[] = $this->components->getOnclickSubmit('Controller/default/' . $product->id);
            }

            $this->layout->footer[] = $this->components->getComponentText($text,array('style' => 'earnster_bottom_button','onclick' => $onclick));

        }


        return $this->layout;
    }

    /**
     * @return mixed
     */
    public function getDivs(){

        $onclick[] = $this->getOnclickSubmit('cart/confirmbuy');
        $onclick[] = $this->getOnclickHideDiv('cancel_div');
        $onclick[] = $this->getOnclickOpenAction('cart');

        $divs['cancel_div'] = $this->components->getComponentConfirmationDialog(
            $onclick,'cancel_div','{#have_you_finished_the_transaction_for_buying#}?',
            array('title' => '{#finished#}?','title_cancel' => '{#no#}'));
        return $divs;
    }

}
