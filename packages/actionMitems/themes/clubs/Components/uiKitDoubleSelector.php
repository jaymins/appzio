<?php

namespace packages\actionMitems\themes\clubs\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitDoubleSelector
{

    public function uiKitDoubleSelector($hint, $variablenames, $list, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapComponent $this */

        foreach ($variablenames as $variablename) {
            $parameters[$variablename]['variable'] = $variablename;
        }

        $activeIcon = isset($parameters['active_icon']) ? $parameters['active_icon'] : 'form-arrow-up.png';
        $inactiveIcon = isset($parameters['inactive_icon']) ? $parameters['inactive_icon'] : 'arrow-down.png';

        /* error handling */
        if ($this->model->getValidationError($variablenames[0])) {
            $error[] = $this->getComponentText($hint . ' ', array('style' => 'akit_double_selector_hint', 'uppercase' => true));
            foreach ($variablenames as $variablename) {
                $error[] = $this->getComponentText($this->model->getValidationError($variablename), array('style' => 'akit_double_selector_error'));
            }
            $out[] = $this->getComponentRow($error, array(), array('width' => '100%'));
        } else {
            $out[] = $this->getComponentText($hint, array('style' => 'akit_double_selector_hint', 'uppercase' => true));
        }

        $row[] = $this->getComponentImage($activeIcon, array('style' => 'akit_double_selector_icon'));

        foreach ($variablenames as $variablename) {
            /* hinter for the field */

            if (isset($parameters[$variablename]['value'])) {
                $value = $parameters[$variablename]['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
            }

            $row[] = $this->getComponentText($value, array('style' => 'akit_double_selector_field', 'variable' => $variablename, 'value' => $value));
            $row[] = $this->getComponentText(' ');
        }

        if (isset($parameters['hide'])) {
            $onclick[] = $this->getOnclickHideElement($parameters['hide']);
        }

        $onclick[] = $this->getOnclickHideElement($variablenames[0] . 'hinter');
        $onclick[] = $this->getOnclickShowElement($variablenames[0] . 'selector');

        // hiding elements that break the UI
        $onclick[] = $this->getOnclickHideElement('switch_1');
        $onclick[] = $this->getOnclickHideElement('switch_2');
        $onclick[] = $this->getOnclickHideElement('spacer_1');
        $onclick[] = $this->getOnclickHideElement('spacer_2');
        $onclick[] = $this->getOnclickHideElement('title');
        $onclick[] = $this->getOnclickHideElement('description');
        $onclick[] = $this->getOnclickHideElement('padded-calendar');

        $out[] = $this->getComponentRow($row, array('onclick' => $onclick, 'id' => $variablenames[0] . 'hinter'), array('margin' => '0 20 8 20'));

        /* data that's shown after click, hidden by default */
        $closeclick[] = $this->getOnclickHideElement($variablenames[0] . 'selector');
        $closeclick[] = $this->getOnclickShowElement($variablenames[0] . 'hinter');

        // show hidden elements on the div
        $closeclick[] = $this->getOnclickShowElement('switch_1');
        $closeclick[] = $this->getOnclickShowElement('switch_2');
        $closeclick[] = $this->getOnclickShowElement('spacer_1');
        $closeclick[] = $this->getOnclickShowElement('spacer_2');
        $closeclick[] = $this->getOnclickShowElement('title');
        $closeclick[] = $this->getOnclickShowElement('description');
        $closeclick[] = $this->getOnclickShowElement('padded-calendar');

        $closeimg[] = $this->getComponentImage($inactiveIcon, array('onclick' => $closeclick, 'style' => 'akit_double_selector_icon'), array());
        $openstate[] = $this->getComponentRow($closeimg,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

        foreach ($variablenames as $variablename) {
            if (isset($parameters[$variablename]['value'])) {
                $value = $parameters[$variablename]['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
            }

            $openstateCol[] = $this->getComponentFormFieldList($list[$variablename], array(
                'update_on_entry' => 1,
                'variable' => $variablename,
                'value' => $value,
                'style' => 'akit_double_selector_field_list'
            ));
        }

        $openstateRow[] = $this->getComponentRow($openstateCol);
        $openstateRow[] = $this->getComponentText('{#choose#}', array('onclick' => $closeclick, 'style' => 'akit_double_selector_select_button'));

        $out[] = $this->getComponentColumn($openstateRow, array('id' => $variablenames[0] . 'selector', 'height' => '200', 'visibility' => 'hidden'), array());

//        /* error handling */
//        if ($this->model->getValidationError($variablenames[0])) {
//            $out[] = $this->getComponentText('', array('style' => 'akit_double_selector_divider_error'));
//        } else {
//            $out[] = $this->getComponentText('', array('style' => 'akit_double_selector_divider'));
//        }


        return $this->getComponentColumn($out);
    }

}