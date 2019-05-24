<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getProductListItem {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getProductListItem($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        if(isset($content->product->photo)){
            $cart = $content;
            $content = $content->product;
        }

        if(!isset($parameters['no_controls'])) {
            $onclick = $this->getOnclickOpenAction('product', false,
                array(
                    'id' => 'product/default/' . $content->id,
                    //'context' => 'product' . $content->id,
                    'sync_open' => 1,
                    'back_button' => 1
                ));
        } else {
            $onclick = array();
        }

        $icon = $content->photo ? $content->photo : 'icon_camera-grey.png';

        if($this->model->getCurrentActionPermaname() == 'cartpopup'){
            $width = $this->screen_width - 270;
        } else {
            $width = $this->screen_width - 240;
        }

        $row[] = $this->getComponentImage($icon,array('style' => 'mproduct_product_icon','imgwidth' => '300','imgheight' => '300'));
        $col[] = $this->getComponentText($content->title,array('parent_style' => 'mproduct_productlist_text','onclick' => $onclick),
            array('parent_style' => 'mproduct_productlist_text','width' => $width));

        if(isset($cart->quantity)) {
            $price = $content->price * $cart->quantity;
        } else {
            $price = $content->price;
        }

        // determines whether this a cart view
        if(isset($parameters['no_controls'])) {
            $col[] = $this->getComponentText('{#sold_by#}: {#amazon#}',
                array('parent_style' => 'mproduct_productlist_text_header', 'onclick' => $onclick),
                array('parent_style' => 'mproduct_productlist_text_header', 'width' => $width));
            $row[] = $this->getComponentColumn($col);
            $row[] = $this->getComponentText('$ ' . number_format($price, 2));
        } else {

            $col[] = $this->getComponentText('{#price#}: $' .$price,
                array('parent_style' => 'mproduct_productlist_text_header','onclick' => $onclick),
                array('parent_style' => 'mproduct_productlist_text_header','width' => $width));
            $row[] = $this->getComponentColumn($col);

        }

        if(isset($cart->quantity)){
            if(!isset($parameters['no_controls'])) {
                $minus = $this->getOnclickSubmit('removefromcart-' . $cart->id);
                $plus = $this->getOnclickSubmit('addtocart-' . $cart->id);
                $row[] = $this->getComponentImage('cart-minus.png', array('onclick' => $minus, 'style' => 'mproduct_del_cart_item'));
                $row[] = $this->getComponentText($cart->quantity, array('style' => 'mproduct_cart_quantity'));
                $row[] = $this->getComponentImage('cart-plus.png', array('onclick' => $plus, 'style' => 'mproduct_del_cart_item'));
            }
            $click = array();
        } else {
            // only in case of product list
            $click = array('onclick' => $onclick);
        }

        $out[] = $this->getComponentDivider();
        $out[] = $this->getComponentRow($row,array('style' => 'mproduct_category_row'));
        return $this->getComponentColumn($out,$click);

	}

}
