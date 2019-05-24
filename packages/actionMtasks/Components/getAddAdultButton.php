<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getAddAdultButton {

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

    public function getAddAdultButton($onclick){
        /** @var BootstrapComponent $this */

        $col[] = $this->getComponentDivider();

        $row[] = $this->getComponentText('{#add_adult#}',array('style' => ''),array('vertical-align' => 'middle','margin' => '0 0 0 20'));
        $row[] = $this->getComponentText('+',array(),array('floating' => 1,'float'=>'right','margin' => '0 20 0 0','vertical-align' => 'middle',
            'font-size' => '22', 'color' => '#D00062'));

        $col[] = $this->getComponentRow($row,array(),array('vertical-align' => 'middle','height' => '50'));
        $col[] = $this->getComponentDivider();

        return $this->getComponentColumn($col,array('onclick'=>$onclick,'style' => 'mtasks_add_adult_button'));
    }

}
