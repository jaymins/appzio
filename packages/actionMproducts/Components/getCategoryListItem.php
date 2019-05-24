<?php

namespace packages\actionMproducts\Components;
use Bootstrap\Components\BootstrapComponent;

trait getCategoryListItem {

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

    public function getCategoryListItem($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $icon = $content->icon ? $content->icon : 'default-category-icon.png';

        $row[] = $this->getComponentImage($icon,array('style' => 'mproduct_category_icon','imgwidth' => '100','imgheight' => '100'));
        $col[] = $this->getComponentText($content->title,array('style' => 'mproduct_category_text'));
        $col[] = $this->getComponentText($content->headertext,array('style' => 'mproduct_category_header'));
        $row[] = $this->getComponentColumn($col);
        $out[] = $this->getComponentDivider();
        $out[] = $this->getComponentRow($row,array('style' => 'mproduct_category_row'));

        $onclick = $this->getOnclickOpenAction('productlist',false,
            array(
                'id' => 'productlist/default/' .$content->id,
                //'context' => 'productlist'.$content->id,
                'sync_open' => 1,
                'back_button' => 1
            ));

        return $this->getComponentColumn($out,array('onclick' => $onclick));

	}

}
