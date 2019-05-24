<?php

namespace packages\actionMusersettings\themes\uikit\Components;
use Bootstrap\Components\BootstrapComponent;

trait getInputGroupField {

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

    public function getInputGroupField(string $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */


        return $this->getComponentColumn([
            $this->getComponentText('{#first_name#}', [
                'style' => 'mreg_input_description'
            ], [
            ]),
            $this->getComponentFormFieldText('',[
                'variable' => 'firstname',
                'hint' => '',
                'style' => 'mreg_input'
            ])
        ],[
            'style' => 'mreg_inputgroup'
        ],[]);

    }

}
