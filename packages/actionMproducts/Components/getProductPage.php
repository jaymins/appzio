<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getProductPage {

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

    public function getProductPage($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        /** @var ProductitemsModel $content */

        if(!isset($content->photo)){
            return $this->getComponentText('No product found');
        }

        $mainphoto = $content->photo ? $content->photo : 'default-product-icon.png';

        if(!isset($parameters['hide_extra_photos'])) {

            $photos = $content->photos;
            $current = 1;

            if (is_array($photos)) {
                $col[] = $this->getImg($mainphoto);
                $swiper[] = $this->getComponentColumn($col);

                foreach ($photos as $photo) {
                    unset($col);
                    $current++;
                    $col[] = $this->getImg($photo->photo);
                    $swiper[] = $this->getComponentColumn($col);
                }

                $out[] = $this->getComponentSwipe($swiper,array('hide_scrollbar' => true));
            }
        } else {
            $onclick = $this->getOnclickOpenAction('product', false,
                array(
                    'id' => 'product/default/' . $content->id,
                    //'context' => 'product' . $content->id,
                    'sync_open' => 1,
                    'back_button' => 1
                ));

            $out[] = $this->getComponentImage($mainphoto,array('parent_style' => 'mproduct_mainp_img',
                'imgwidth' => '600','imgheight' => '600','quality' => '60','lazy' => 1,
                'onclick' => $onclick),
                array('width' => $this->screen_width,'height' => $this->screen_width,'crop' => 'yes'));
        }

        unset($col);

        if(!isset($parameters['hide_description'])) {
            $out[] = $this->getComponentSwipeAreaNavigation('#000000', '#B2B3B3', array(), array('width' => '100%', 'text-align' => 'center'));
        }

        $col[] = $this->getComponentText($content->title,array('style' => 'mproduct_product_title'));
        $col[] = $this->getComponentText('$' .$content->price .' • Amazon',array('style' => 'mproduct_product_price'));
        $row[] = $this->getComponentColumn($col);

        $out[] = $this->getComponentDivider();
        $out[] = $this->getComponentRow($row,array('style' => 'mproduct_category_row'));

        if(!isset($parameters['hide_description'])){
            $out[] = $this->getComponentDivider();
            $txt[] = $this->getComponentText($content->description,array('style' => 'mproduct_product_price'));
            $out[] = $this->getComponentRow($txt,array('style' => 'mproduct_category_row'));
        }

        return $this->getComponentColumn($out);

	}

	private function getImg($img){
        return $this->getComponentImage($img,array(
            'parent_style' => 'mproduct_mainp_img',
            'imgwidth' => '600',
            'imgheight' => '600',
            'lazy' => 1,
            'quality' => '60'),
            array('width' => $this->screen_width,'height' => $this->screen_width,'crop' => 'yes'));

    }

}
