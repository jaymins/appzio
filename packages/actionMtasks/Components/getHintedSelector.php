<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getHintedSelector {

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

    public function getHintedSelector($hint,$variablename,$list, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $parameters['variable'] = $variablename;

        /* error handling */
        if($this->model->getValidationError($variablename)) {
            $error[] = $this->getComponentText($hint .' ', array('style' => 'steps_hint','uppercase' => true));
            $error[] = $this->getComponentText($this->model->getValidationError($variablename),array('style' => 'steps_error'));
            $out[] = $this->getComponentRow($error,array(),array('width' => '100%'));
        } else {
            $out[] = $this->getComponentText($hint, array('style' => 'steps_hint','uppercase' => true));
        }

            /* hinter for the field */
            if(isset($parameters['value'])){
                $value = $parameters['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
                $value = strtolower($value);
                $value = str_replace('#', '', $value);
                $value = str_replace('{', '', $value);
                $value = str_replace('}', '', $value);
                $value = str_replace(' ', '_', $value);
            }


            $row[] = $this->getComponentText($value,array('style' => 'mtask_popup_chooser','variable' => $variablename));
            $row[] = $this->getComponentImage('form-arrow-down.png',array('style' => 'mtasks_select_icon'));

            $onclick[] = $this->getOnclickHideElement('header');
            $onclick[] = $this->getOnclickHideElement($variablename.'hinter');
            $onclick[] = $this->getOnclickShowElement($variablename.'selector');

        $out[] = $this->getComponentRow($row,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

            /* data that's shown after click, hidden by default */
            $closeclick[] = $this->getOnclickHideElement($variablename.'selector');
            $closeclick[] = $this->getOnclickShowElement($variablename.'hinter');

            $closeimg[] = $this->getComponentImage('form-arrow-up.png',array('onclick' => $closeclick,'style' => 'mtasks_select_icon'),array());
            //$openstate[] = $this->getComponentRow($closeimg,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

            $openstate[] = $this->getComponentFormFieldList($list,array(
                'update_on_entry' => 1, 'variable' => $variablename, 'value' => $value
            ),array('text-align' => 'center','margin' => '0 40 0 40' ));
            $openstate[] = $this->getComponentText('{#choose#}',array('onclick' => $closeclick,'style' => 'mtasks_small_btn'));

        $out[] = $this->getComponentColumn($openstate,array('id' => $variablename.'selector','height' => '300','visibility' => 'hidden'),array());

        /* error handling */
        if($this->model->getValidationError($variablename)){
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider_error'));
        } else {
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider'));
        }


        return $this->getComponentColumn($out);
	}

}