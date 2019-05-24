<?php

namespace packages\actionMnexudus\Components;

use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusTimer
{

    public function getNexudusTimer($start_time, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapComponent $this */

        $id = $this->addParam('id', $parameters, false);

        $options['mode'] = 'countdown';
        $options['style'] = 'uikit_timer';
        $options['timer_id'] = 'booth-timer-' . $id;
        $options['submit_menu_id'] = 'timerexpired';
        $seconds_left = $start_time - time();

        if ($seconds_left > 86400) {
            $days = floor($seconds_left / 86400);
            $seconds_left = $seconds_left - ($days * 86400);
            $col[] = $this->getComponentText($days . ' {#days#} ', ['style' => 'uikit_timer']);
        }

        $timer_output[] = $this->getTimer($seconds_left, $options);
        $timer_output[] = $this->getComponentText(' ', [], [
            'font-size' => '20',
            'color' => '#ffffff',
            'margin' => '0 0 0 0',
        ]);

        $col[] = $this->getComponentRow($timer_output, [], [
            'vertical-align' => 'middle',
            'width' => 'auto',
            'text-align' => 'center',
        ]);

        return $this->getComponentColumn($col, [], [
            'width' => 'auto',
            'vertical-align' => 'middle',
            'text-align' => 'center',
        ]);
    }

}
