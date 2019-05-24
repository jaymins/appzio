<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getCartHeader {

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

    public function getCartHeader($data, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $this->data = $data;

        $cart_items = $this->getData('cart_items', 'num');
        $cart_price = $this->getData('cart_total', 'float');
        $isPopup = $this->getData('isPopup', 'int');
        if($cart_items == 0){
            $cart_items = '{#no#} {#items#}';
        }elseif($cart_items > 1){
            $cart_items = $cart_items.' {#items#}';
        } else {
            $cart_items = $cart_items.' {#item#}';
        }

        $col[] = $this->getComponentText($cart_items,array('style' => 'mproducts_list_title','uppercase' => true));
        $col[] = $this->getComponentText('{#total#}: $'.$cart_price,array('style' => 'mproducts_list_title_price','uppercase' => true));
        if ($isPopup) {
            $action = $this->getOnclickClosePopup();
            $colTop[] = $this->getComponentImage(
                'remove-from-cart.png',
                array(
                    'style' => 'mproducts_popup_close',
                    'onclick' => $action
                    )
            );
            $row[] = $this->getComponentRow($colTop);
        }

        $row[] = $this->getComponentRow($col);
        return $this->getComponentColumn($row);

	}

}
