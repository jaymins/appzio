<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getFauxTopbar {

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

    public function getFauxTopbar($parameters=array()){
        /** @var BootstrapComponent $this */

        $div = isset($parameters['div_name']) ? $parameters['div_name'] : 'add_adult';
        $title = isset($parameters['title']) ? $parameters['title'] : '{#add_adult#}';
        $btn_title = isset($parameters['btn_title']) ? $parameters['btn_title'] : false;
        $action = isset($parameters['btn_onclick']) ? $parameters['btn_onclick'] : $this->getOnclickSubmit('Controller/addadult/new');

        if(isset($parameters['route_back'])){
            $close = $this->getOnclickRoute($parameters['route_back']);
            $top[] = $this->getComponentImage('div-back-icon.png',array('onclick' => $close,'style' => 'fauxheader_back'));
        }elseif(isset($parameters['mode']) AND $parameters['mode'] == 'justclose'){
            $top[] = $this->getComponentText('',array('style' => 'fauxheader_back'));
        }elseif(isset($parameters['mode']) AND $parameters['mode'] == 'gohome'){
            $close = $this->getOnclickRoute('Controller/flushroutes/flush');
            $close[] = $this->getOnclickGoHome();
            $top[] = $this->getComponentImage('div-back-icon.png',array('onclick' => $close,'style' => 'fauxheader_back'));
        }elseif(isset($parameters['mode']) AND $parameters['mode'] == 'gohome-simple'){
            $close[] = $this->getOnclickGoHome();
            $top[] = $this->getComponentImage('div-back-icon.png',array('onclick' => $close,'style' => 'fauxheader_back'));
        }elseif(isset($parameters['mode']) AND $parameters['mode'] == 'close'){
            $close = $this->getOnclickClosePopup();
            $top[] = $this->getComponentImage('close-icon-div.png',array('onclick' => $close,'style' => 'fauxheader_close'));
        }elseif(isset($parameters['mode']) AND $parameters['mode'] == 'close-go-home'){
            $close[] = $this->getOnclickGoHome();
            $top[] = $this->getComponentImage('close-icon-div.png',array('onclick' => $close,'style' => 'fauxheader_close'));
        } else {
            $close = $this->getOnclickTab(1);
            $top[] = $this->getComponentImage('div-close-icon.png',array('onclick' => $close,'style' => 'fauxheader_close'));
        }

        $spacer_width = $this->screen_width - ($this->screen_width*0.8);
        $spacer_width = $spacer_width - 40;

        $titleStyle = array();
        if (isset($parameters['popup']) && $parameters['popup']) {
            $spacer_width = $spacer_width / 2;

            $titleStyle = array("width" => "20%",
                                "color" => "#64606C",
                                "font-size"=> "18",
                                "text-align"=> "center");
        }

        $top[] = $this->getComponentVerticalSpacer($spacer_width);
        $top[] = $this->getComponentText($title,array('uppercase' => true,'style' => 'fauxheader_title'));

        if($btn_title){
            if($btn_title == 'X'){
                $top[] = $this->getComponentVerticalSpacer($spacer_width);
                $top[] = $this->getComponentImage('div-close-icon.png',array('onclick' => $action,'style' => 'fauxheader_close'));
            } else {
                $top[] = $this->getComponentText($btn_title,array('onclick' => $action,'style' => 'fauxheader_add'), $titleStyle);
            }
        } else {
            $top[] = $this->getComponentText('',array('style' => 'fauxheader_add'));
        }

        return $this->getComponentRow($top,array(),array('background-color' => $this->color_top_bar_color,
            'height' => '45','width' => $this->screen_width,'vertical-align' => 'middle','padding' => '0 10 0 10'));

    }

}
