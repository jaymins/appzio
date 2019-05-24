<?php

namespace packages\actionMbooking\themes\tattoo\Components;

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
        $disable_hint = isset($parameters['disable_hint']) ? $parameters['disable_hint'] : false;

        /* error handling */
        if ($this->model->getValidationError($variablenames[0])) {

            if ( !$disable_hint ) {
                $error[] = $this->getComponentText($hint . ' ', array('uppercase' => true), array(
                    "color" => "#000000",
                    "text-align" => "left",
                    "font-size" => "12",
                    "margin" =>  "15 40 0 10"
                ));
            }

            foreach ($variablenames as $variablename) {
                $error[] = $this->getComponentText($this->model->getValidationError($variablename), array('style' => 'akit_double_selector_error'));
            }

            $out[] = $this->getComponentRow($error, array(), array('width' => '100%'));

        } else if ( !$disable_hint ) {
            $out[] = $this->getComponentText($hint, array('style' => 'akit_double_selector_hint', 'uppercase' => true));
        }

        $row[] = $this->getComponentImage($activeIcon, array(), array(
            'width' => '30',
            'height' => '30',
            'margin' => '10 15 0 0'
        ));

        foreach ($variablenames as $variablename) {
            /* hinter for the field */

            if (isset($parameters[$variablename]['value'])) {
                $value = $parameters[$variablename]['value'];
            } else {
                $value = $this->model->getSubmittedVariableByName($variablename) ? $this->model->getSubmittedVariableByName($variablename) : '{#choose#}';
            }

            $row[] = $this->getComponentText($value, array('variable' => $variablename, 'value' => $value), array(
                'color' => '#000000',
                'font-weight' => 'bold'
            ));

            $row[] = $this->getComponentText(' ');
        }

        if (isset($parameters['hide'])) {
            $onclick[] = $this->getOnclickHideElement($parameters['hide']);
        }

        $onclick[] = $this->getOnclickHideElement($variablenames[0] . 'hinter');
        $onclick[] = $this->getOnclickShowElement($variablenames[0] . 'selector');

        $out[] = $this->getComponentRow($row, array('onclick' => $onclick, 'id' => $variablenames[0] . 'hinter'));

        /* data that's shown after click, hidden by default */
        $closeclick[] = $this->getOnclickHideElement($variablenames[0] . 'selector');
        $closeclick[] = $this->getOnclickShowElement($variablenames[0] . 'hinter');

        $closeimg[] = $this->getComponentImage($inactiveIcon, array('onclick' => $closeclick, 'style' => 'akit_double_selector_icon'), array());
        //$openstate[] = $this->getComponentRow($closeimg,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

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
            ), array(
                "text-align" => "center",
                "width" => "50%",
                'color' => '#000000'
            ));
        }

        $openstateRow[] = $this->getComponentRow($openstateCol);
        $openstateRow[] = $this->getComponentText('{#choose#}', array('onclick' => $closeclick, 'style' => 'akit_double_selector_select_button'));

        $out[] = $this->getComponentColumn($openstateRow, array(
            'id' => $variablenames[0] . 'selector',
            'visibility' => 'hidden'
        ), array(
            'height' => 'auto',
            'vertical-align' => 'middle',
        ));

        return $this->getComponentColumn($out, array(), $styles);
    }

}