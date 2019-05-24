<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;
use function strtoupper;

trait getFeaturedProductsSwiperBig {

    public $page = 0;

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

    public function getFeaturedProductsSwiperBig($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */


        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_featured_items_at_the_monent#}',array('style' => 'steps_error2'));
        }

        $itemcount = count($content);


        foreach($content as $items){
            $swiper[] = $this->getSwipeBig($items,$itemcount);
        }

        if(isset($swiper)){
            $out[] = $this->getComponentSwipe($swiper);
            return $this->getComponentColumn($out);
        }

        return $this->getComponentText('{#no_featured_items_at_the_monent#}',array('style' => 'steps_error2'));



    }

	private function getSwipeBig($items,$itemcount){
        $this->page++;

        $height = $this->screen_height - 116;
        $display = [];
        foreach ($items as $item) {

            $page = array();
            $product = $this->getProductPage($item, array('hide_extra_photos' => 1, 'hide_description' => 1));
            $page[] = $product;
            $page[] = $this->getAddToCartButton($item->id,
                array('vertical-align' => 'bottom')
            );

            $display[] = $this->getComponentColumn($page,array(),array('height' => $height, 'margin' => '0 0 5 0'));
        }

        $title = $this->model->localize($item->categories->title);

        return $this->getComponentColumn($display,array('topbar_title' => strtoupper($title)));
    }


}
