<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getProgressHeader {

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

    public function getProgressHeader($phase=1){
        /** @var BootstrapComponent $this */

        $title1 = $this->model->localize('{#adult#}');
        $title2 = $this->model->localize('{#task#}');
        $title3 = $this->model->localize('{#details#}');
        $title4 = $this->model->localize('{#review#}');

        $col[] = $this->getComponentText($title1,array('uppercase' => true),array('parent_style' => 'tsk_header_active','width' => '25%'));

        if($phase > 1) {
            $col[] = $this->getComponentText($title2,array('uppercase' => true),array('parent_style' => 'tsk_header_active','width' => '25%'));
        } else {
            $col[] = $this->getComponentText($title2,array('uppercase' => true),array('parent_style' => 'tsk_header','width' => '25%'));
        }

        if($phase > 2) {
            $col[] = $this->getComponentText($title3,array('uppercase' => true),array('parent_style' => 'tsk_header_active','width' => '25%'));

        } else {
            $col[] = $this->getComponentText($title3,array('uppercase' => true),array('parent_style' => 'tsk_header','width' => '25%'));
        }

        if($phase > 3) {
            $col[] = $this->getComponentText($title4,array('uppercase' => true),array('parent_style' => 'tsk_header_active','width' => '25%'));
        } else {
            $col[] = $this->getComponentText($title4,array('uppercase' => true),array('parent_style' => 'tsk_header','width' => '25%'));
        }

        $row[] = $this->getComponentRow($col,array(),array('vertical-align' => 'middle','margin' => '10 0 0 0','width' => '100%'));

        $width = ($this->screen_width / 8) - 7;

        unset($col);

        $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
        $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball_active'));

        if($phase > 1){
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball_active'));
        } else {
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball'));
        }

        if($phase > 2){
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball_active'));
        } else {
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball'));
        }

        if($phase > 3){
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider_active','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball_active'));
        } else {
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
            $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_ball'));
        }

        $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));
        $col[] = $this->getComponentText('',array(),array('parent_style' => 'tsk_header_divider','width'=>$width));

        $row[] = $this->getComponentRow($col,array(),array('vertical-align' => 'middle','margin' => '10 0 0 0','width' => '100%'));


        return $this->getComponentColumn($row,array(),array());
    }

}
