<?php

namespace packages\actionDitems\themes\fans\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitHintedTime
{
    public function uiKitHintedTime($hourValue = 0, $minutesValue = 0, $icon = 'clock-outline.png')
    {
        /** @var BootstrapComponent $this */

        $hours = ';';
        $minutes = ';';

        for ($i = 0; $i <= 23; $i++) {
            $num = $i < 10 ? '0' . $i : $i;
            $hours .= ";$i;$num";
        }

        for ($i = 0; $i < 60; $i+=15) {
            $num = $i < 10 ? '0' . $i : $i;
            $minutes .= ";$i;$num";
        }

        return $this->uiKitDoubleSelector('', array(
            'hour',
            'minutes'
        ), array(
            'hour' => $hours,
            'minutes' => $minutes
        ), array(
            'active_icon' => $icon,
            'inactive_icon' => $icon,
            'hour' => array(
                'value' => $hourValue
            ),
            'minutes' => array(
                'value' => $minutesValue
            )
        ));
    }
}