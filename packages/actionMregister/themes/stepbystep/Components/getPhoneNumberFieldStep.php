<?php

namespace packages\actionMregister\themes\stepbystep\Components;
use function array_flip;
use Bootstrap\Components\BootstrapComponent;
use function strtolower;

trait getPhoneNumberFieldStep {

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

    public function getPhoneNumberFieldStep($country){
        /** @var BootstrapComponent $this */

        //$this->addDivs(array('countries' => 'getDivPhoneNumbers'));

        $params['variable'] = 'phonenumber';
        $params['hint'] = '123 456 78';
        $params['style'] = 'steps_phonefield';
        $params['input_type'] = 'phone';
        $params['activation'] = 'initially';

        $col[] = $this->getComponentText('(',array('style' => 'steps_fieldinclusion'));
        $col[] = $this->getComponentFormFieldText($country,array('style' => 'steps_countrycode','variable' => 'countrycode',
            'input_type' => 'phone'));
        $col[] = $this->getComponentText(') ',array('style' => 'steps_fieldinclusion'));
        $col[] = $this->getComponentFormFieldText($this->model->getSubmittedVariableByName('number'),$params);

        $out[] = $this->getComponentRow($col,array('style' => 'steps_phonefield_row'));

        if($this->model->getValidationError('phonenumber')){
            $out[] = $this->getComponentText($this->model->getValidationError('phonenumber'),array('style' => 'steps_error2'));
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider_error'));
        } else {
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider'));
        }
        
        return $this->getComponentColumn($out);


    }

}