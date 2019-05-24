<?php

namespace packages\actionMMarketplace\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitCalendarCustom
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

    public function uiKitCalendarCustom($hint, $variablename, $time, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapComponent $this */

        $parameters['variable'] = $variablename;
        $activeIcon = isset($parameters['active_icon']) ? $parameters['active_icon'] : 'form-arrow-up.png';
        $inactiveIcon = isset($parameters['inactive_icon']) ? $parameters['inactive_icon'] : 'arrow-down.png';
        $disable_hint = isset($parameters['disable_hint']) ? $parameters['disable_hint'] : false;

        /* error handling */

        if ($this->model->getValidationError($variablename)) {

            if ( !$disable_hint ) {
                $error[] = $this->getComponentText($hint . ' ', array('style' => 'akit_calendar_hint', 'uppercase' => true));
            }

            $error[] = $this->getComponentText($this->model->getValidationError($variablename), array('style' => 'akit_calendar_error'));
            $out[] = $this->getComponentRow($error, array(), array('width' => '100%'));
        } else if ( !$disable_hint ) {
            $out[] = $this->getComponentText($hint, array('style' => 'akit_calendar_hint', 'uppercase' => true));
        }

        if (isset($parameters['hide'])) {
            $onclick[] = $this->getOnclickHideElement($parameters['hide']);
        }

        $onclick[] = $this->getOnclickHideElement($variablename . 'hinter');
        $onclick[] = $this->getOnclickShowElement($variablename . 'selector');

        $out[] = $this->getComponentRow(array(
            $this->getComponentImage($activeIcon, array(), array(
                'width' => '30',
                'height' => '30',
                'margin' => '10 15 0 0'
            )),
            $this->getComponentText(date('m / d / Y', $time), array('variable' => $variablename), array(
                'color' => '#000000',
                'font-weight' => 'bold'
            ))
        ), array(
            'onclick' => $onclick,
            'id' => $variablename . 'hinter',
        ), array(
            'padding' => '5 0 5 0',
            'vertical-align' => 'middle'
        ));

        /* data that's shown after click, hidden by default */
        $closeclick[] = $this->getOnclickHideElement($variablename . 'selector');
        $closeclick[] = $this->getOnclickShowElement($variablename . 'hinter');

        $closeimg[] = $this->getComponentImage($inactiveIcon, array('onclick' => $closeclick, 'style' => 'akit_calendar_icon'), array());
        $openstate[] = $this->getComponentRow($closeimg, array('onclick' => $onclick, 'id' => $variablename . 'openhinter'), array('margin' => '8 20 8 40'));

        if (isset($parameters['width'])) {
            $width = $parameters['width'];
        } else {
            $width = $this->screen_width - 80;
        }

        $openstate[] = $this->getComponentCalendar(array('date' => $time,
            'update_on_entry' => 1,
            'variable' => $variablename,
            'date_format' => 'MM / dd / yyyy',
            'min_date' => '0',
            'selection_style' => array(
                'color' => '#ffffff',
                'background-color' => '#F89063'
            ),
        ), array(
            'height' => '300',
            'width' => $width,
            'margin' => '0 40 0 40',
        ));

        $openstate[] = $this->getComponentText('{#select_date#}', array('onclick' => $closeclick, 'style' => 'akit_calendar_choose_button'));

        $out[] = $this->getComponentColumn($openstate, array('id' => $variablename . 'selector', 'height' => '300', 'visibility' => 'hidden'), array(
            'text-align' => 'center'
        ));

        return $this->getComponentColumn($out, array(), $styles);
    }

}