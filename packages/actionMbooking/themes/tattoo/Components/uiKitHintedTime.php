<?php

namespace packages\actionMbooking\themes\tattoo\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitHintedTime
{
    public function uiKitHintedTime($hourValue, $minutesValue = 0, $params = [], $styles = [])
    {
        /** @var BootstrapComponent $this */

        $hours = ';';
        $minutes = ';';

        for ($i = 1; $i <= 24; $i++) {
            $num = $i < 10 ? '0' . $i : $i;
            $hours .= ";$i;$num";
        }

        for ($i = 0; $i < 60; $i+=15) {
            $num = $i < 10 ? '0' . $i : $i;
            $minutes .= ";$i;$num";
        }

        $parameters = array_merge(array(
            'active_icon' => 'clock_black.png',
            'inactive_icon' => 'clock_black.png',
            'hour' => array(
                'value' => $hourValue
            ),
            'minutes' => array(
                'value' => $minutesValue
            )
        ), $params);

        return $this->uiKitDoubleSelector('Time', array(
            'hour',
            'minutes'
        ), array(
            'hour' => $hours,
            'minutes' => $minutes
        ),
            $parameters,
            $styles
        );

    }
}