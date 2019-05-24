<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getFeaturedProductsSwiper {

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

    public function getFeaturedProductsSwiper($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */


        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_featured_items_at_the_monent#}',array('style' => 'steps_error2'));
        }

        $count = 0;
        $itemcount = count($content);

        foreach($content as $item){
            $products[] = $this->getFeaturedProduct($item);
            $count++;
            if($count == 2){
                $swiper[] = $this->getSwipe($products,$itemcount);
                unset($products);
                $count = 0;
            }
        }


        if(isset($products)){
            $swiper[] = $this->getSwipe($products,$itemcount);
        }

        if(isset($swiper)){
            $out[] = $this->getComponentSwipe($swiper,array('hide_scrollbar' => true,'animate' => 'nudge'));
            //$out[] = $this->getComponentSwipeAreaNavigation('#000000','#B2B3B3',array(),array('width' => '100%','text-align' => 'center'));
            return $this->getComponentColumn($out);
        }

        return $this->getComponentText('{#no_featured_items_at_the_monent#}',array('style' => 'steps_error2'));

    }

	private function getSwipe($item,$itemcount){
        $this->page++;
        $page[] = $this->getComponentRow($item,array(),array('text-align'=>'center'));
        return $this->getComponentColumn($page);
    }

	private function getFeaturedProduct($content){
        $icon = $content->photo ? $content->photo : 'default-product-icon.png';

        $col[] = $this->getComponentImage($icon,array(
            'style' => 'mproduct_featured_image','imgwidth' => '150','imgheight' => '150','lazy' => 1));
        
        if(strlen($content->title) > 60){
            $title = substr($content->title, 0, strrpos(substr($content->title, 0, 60), ' ')).'...';
        } else {
            $title = $content->title;
        }
        
        $col[] = $this->getComponentText($title,array('parent_style' => 'mproduct_productlist_text'),
            array('parent_style' => 'mproduct_featured_title'));

        $click = $this->getOnclickOpenAction('product',false,array('id' => $content->id,'sync_open' => 1));

        return $this->getComponentColumn($col,array('onclick' => $click,'style' => 'mproduct_featured_row'));
    }

}
