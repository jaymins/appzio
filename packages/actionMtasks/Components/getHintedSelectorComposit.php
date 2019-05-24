<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getHintedSelectorComposit {

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

    public function getHintedSelectorComposit($hint,$variablenames,$list, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        foreach ($variablenames as $variablename) {
            $parameters[$variablename]['variable'] = $variablename;
        }

        /* error handling */
        if($this->model->getValidationError($variablenames[0])) {
            $error[] = $this->getComponentText($hint .' ', array('style' => 'steps_hint','uppercase' => true));
            foreach ($variablenames as $variablename) {
                $error[] = $this->getComponentText($this->model->getValidationError($variablename), array('style' => 'steps_error'));
            }
            $out[] = $this->getComponentRow($error,array(),array('width' => '100%'));
        } else {
            $out[] = $this->getComponentText($hint, array('style' => 'steps_hint','uppercase' => true));
        }

        foreach ($variablenames as $variablename) {
            /* hinter for the field */

            if (isset($parameters[$variablename]['value'])) {
                $value = $parameters[$variablename]['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
            }

            $row[] = $this->getComponentText($value,array('style' => 'mtask_popup_chooser','variable' => $variablename,'value' => $value));
            $row[] = $this->getComponentText(' ');
        }

            $row[] = $this->getComponentImage('form-arrow-down.png',array('style' => 'mtasks_select_icon'));

            $onclick[] = $this->getOnclickHideElement('header');
            $onclick[] = $this->getOnclickHideElement($variablenames[0].'hinter');
            $onclick[] = $this->getOnclickShowElement($variablenames[0].'selector');

        $out[] = $this->getComponentRow($row,array('onclick'=>$onclick,'id' => $variablenames[0].'hinter'),array('margin' => '8 20 8 40'));

            /* data that's shown after click, hidden by default */
            $closeclick[] = $this->getOnclickHideElement($variablenames[0].'selector');
            $closeclick[] = $this->getOnclickShowElement($variablenames[0].'hinter');

            $closeimg[] = $this->getComponentImage('form-arrow-up.png',array('onclick' => $closeclick,'style' => 'mtasks_select_icon'),array());
            //$openstate[] = $this->getComponentRow($closeimg,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

        foreach ($variablenames as $variablename) {
            if (isset($parameters[$variablename]['value'])) {
                $value = $parameters[$variablename]['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
            }

            $openstateCol[] = $this->getComponentFormFieldList($list[$variablename], array(
                'update_on_entry' => 1, 'variable' => $variablename,'value' => $value
            ), array('text-align' => 'center', 'width' => '50%'));
        }

            $openstateRow[] = $this->getComponentRow($openstateCol);
            $openstateRow[] = $this->getComponentText('{#choose#}',array('onclick' => $closeclick,'style' => 'mtasks_small_btn'));

        $out[] = $this->getComponentColumn($openstateRow,array('id' => $variablenames[0].'selector','height' => '300','visibility' => 'hidden'),array());

        /* error handling */
        if($this->model->getValidationError($variablenames[0])){
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider_error'));
        } else {
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider'));
        }


        return $this->getComponentColumn($out);
	}

}