<?php

namespace packages\actionMregister\themes\adidas\Components;
use Bootstrap\Components\BootstrapComponent;

trait HeaderWithImageBgr {

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

    public function HeaderWithImageBgr(string $image, array $parameters=array(),array $styles=array()) {

        $header = [];
        if(isset($parameters['icon'])){
            $header[] = $this->getComponentColumn(array(
                $this->getComponentImage($parameters['icon'], array(), array(
                    'height' => 65
                ))
            ), array(), array('text-align' => 'center', 'margin' => '30 0 10 0'));
        }

        if (isset($parameters['title'])) {
            $header[] = $this->getComponentText($parameters['title'], array(), array(
                'font-size' => '35',
                'text-align' => 'center',
                'margin' => '10 10 10 10',
                'color' => '#ffffff',
            ));
        }

        if (isset($parameters['description'])) {
            $header[] = $this->getComponentColumn(array(
                $this->getComponentText('{#and_connect_with_fans_around_the_world#}', array(
                    'style' => 'uikit_intro_header_description'
                )),
            ), array(), array(
                'margin' => '0 0 5 0',
                'vertical-align' => 'bottom',
            ));
        }

        return $this->getComponentColumn($header, array(),
            array(
                'width' => 'auto',
                'height' => '270',
                "background-image" => $this->getImageFileName($image),
                "background-size" => "cover",
            ));
	}

}