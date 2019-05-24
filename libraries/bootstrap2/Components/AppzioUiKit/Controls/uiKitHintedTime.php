<?php

namespace Bootstrap\Components\AppzioUiKit\Controls;

use Bootstrap\Components\BootstrapComponent;

trait uiKitHintedTime
{
    public function uiKitHintedTime($hourValue, $minutesValue = 0)
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

        return $this->uiKitDoubleSelector('Time', array(
            'hour',
            'minutes'
        ), array(
            'hour' => $hours,
            'minutes' => $minutes
        ), array(
            'active_icon' => 'clock-icon.png',
            'inactive_icon' => 'clock-icon.png',
            'hour' => array(
                'value' => $hourValue
            ),
            'minutes' => array(
                'value' => $minutesValue
            )
        ));
    }
}