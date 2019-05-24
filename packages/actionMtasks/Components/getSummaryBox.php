<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getSummaryBox {

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

    public function getSummaryBox($title,$content=array(),$indent=false,$editonclick=false){
        /** @var BootstrapComponent $this */

        if($editonclick){
            $row[] = $this->getComponentText($title,array('style' => 'mtask_summary_header','uppercase' => true));
            $row[] = $this->getComponentText('{#edit#}',array('style' => 'mtask_summary_header_right_pink','onclick' => $editonclick));
            $out[] = $this->getComponentRow($row);
        } else {
            $out[] = $this->getComponentText($title,array('style' => 'mtask_summary_header','uppercase' => true));
        }

        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_header_spacer'));

        if($indent){
            $out[] = $this->getComponentColumn($content,array(),array('margin' => '0 15 0 15'));
        } else {
            $out[] = $this->getComponentColumn($content);
        }

        $out[] = $this->getComponentSpacer('5');
        $out[] = $this->getComponentText('',array('style' => 'mtask_summary_spacer'));

        return $this->getComponentColumn($out,array('style' => 'mtask_summarybox'));




    }

}
