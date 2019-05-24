<?php

namespace packages\actionMsubscription\Components;
use Bootstrap\Components\BootstrapComponent;

trait getButtonField {

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
    public function getButtonField($field,$title,$buttontitle){
        /** @var BootstrapComponent $this */

        $params['variable'] = $field;
        $params['hint'] = $title;
        $params['style'] = 'subscription_textfield';

        $col[] = $this->getComponentFormFieldText($this->model->getSubmittedVariableByName($field),$params);
        $onclick = $this->getOnclickSubmit('addcode');
        $col[] = $this->getComponentText($buttontitle,array('style' => 'subscription_btn','onclick' => $onclick));

        if(isset($this->model->validation_errors[$field])){
            $row[] = $this->getComponentText($this->model->validation_errors[$field],array('style' => 'mreg_error'));
            $row[] = $this->getComponentRow($col,array(),array('vertical-align' => 'middle'));
            return $this->getComponentColumn($row);
        }

        return $this->getComponentRow($col,array(),array('vertical-align' => 'middle'));
    }

}
