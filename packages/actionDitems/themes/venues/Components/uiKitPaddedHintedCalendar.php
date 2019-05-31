<?php

namespace packages\actionDitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitPaddedHintedCalendar
{

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

    public function uiKitPaddedHintedCalendar($hint, $variablename, $time, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapComponent $this */

        $parameters['variable'] = $variablename;
        $activeIcon = isset($parameters['active_icon']) ? $parameters['active_icon'] : 'form-arrow-up.png';
        $inactiveIcon = isset($parameters['inactive_icon']) ? $parameters['inactive_icon'] : 'arrow-down.png';
        $onSelect = isset($parameters['onselect']) ? $parameters['onselect'] : new \stdClass();

        /* error handling */

        if ($this->model->getValidationError($variablename)) {
//            $error[] = $this->getComponentText($hint . ' ', array('style' => 'akit_calendar_hint', 'uppercase' => true));
            $error[] = $this->getComponentText($this->model->getValidationError($variablename), array('style' => 'akit_calendar_error'));
            $out[] = $this->getComponentRow($error, array(), array('width' => '100%'));
        } else {
//            $out[] = $this->getComponentText($hint, array('style' => 'akit_calendar_hint', 'uppercase' => true));
        }

        /* hinter for the field */

        $row[] = $this->getComponentImage($activeIcon, array('style' => 'akit_calendar_icon'));
        $row[] = $this->getComponentText(date('m / d / Y', $time), array('style' => 'akit_calendar_field', 'variable' => $variablename));

        if (isset($parameters['hide'])) {
            $onclick[] = $this->getOnclickHideElement($parameters['hide']);
        }

        $onclick[] = $this->getOnclickHideElement($variablename . 'hinter');
        $onclick[] = $this->getOnclickShowElement($variablename . 'selector');

        $out[] = $this->getComponentRow($row, array(
            'onclick' => $onclick,
            'id' => $variablename . 'hinter',
            'style' => 'akit_calendar_field_wrapper'
        ));

        /* data that's shown after click, hidden by default */
        $closeclick[] = $this->getOnclickHideElement($variablename . 'selector');
        $closeclick[] = $this->getOnclickShowElement($variablename . 'hinter');

//        $closeimg[] = $this->getComponentImage($inactiveIcon, array('onclick' => $closeclick, 'style' => 'akit_calendar_icon'), array());
        $openstate[] = $this->getComponentRow(array(), array('onclick' => $onclick, 'id' => $variablename . 'openhinter'), array('margin' => '8 20 8 40'));


        if (isset($parameters['width'])) {
            $width = $parameters['width'];
        } else {
            $width = $this->screen_width - 80;
        }

        if ($this->model->getSavedVariable('system_source') == 'client_iphone') {
            $styles = array(
                'height' => '250',
                'width' => $width,
                'margin' => '0 40 0 40',
                'color' => '#323232',
//                'padding' => '200 0 0 0'
            );
        } else {
            $styles = array(
                'height' => '400',
                'width' => 'auto',
                'margin' => '0 20 0 20',
                'color' => '#323232'
            );
        }

        $openstate[] = $this->getComponentCalendar(array('date' => $time,
            'update_on_entry' => 1,
            'variable' => $variablename,
            'date_format' => 'MM / dd / yyyy',
            'onselect' => array(
                $this->getOnclickHideElement('dateselector'),
                $this->getOnclickShowElement('datehinter'),
            ),
            'selection_style' => array(
                'color' => '#ffffff',
                'background-color' => '#FFCC00'
            ),
            'disable_swipe' => 1
        ), $styles);

        $out[] = $this->getComponentColumn($openstate, array('id' => $variablename . 'selector', 'visibility' => 'hidden'), array(
            'text-align' => 'center',
        ));

        /* error handling */
        if ($this->model->getValidationError($variablename)) {
            $out[] = $this->getComponentText('', array('style' => 'akit_calendar_divider_error'));
        }


        return $this->getComponentColumn($out, array(
            'id' => 'padded-calendar'
        ));
    }

}