<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getAddToCartButton {

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

    public function getAddToCartButton($id,array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */
        $onclick = $this->getOnclickOpenAction(
            'cartpopup',
            false,
            array('sync_open' => 1,'open_popup' => 1),
            'Cart/addtocart/'.$id,
            true,
            array('productid' => $id));

        $text = strtoupper($this->model->localize('{#earn_it#}'));
        if(isset($parameters['vertical-align'])){
            return $this->getComponentText($text,array('style' => 'earnster_bottom_button_bottom','onclick' => $onclick));
        } else {
            return $this->getComponentText($text,array('style' => 'earnster_bottom_button','onclick' => $onclick));
        }


    }

}
