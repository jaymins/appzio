<?php

namespace packages\actionMnexudus\Components;

use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getBigNexudusHeader
{

    public function getBigNexudusHeader($title, $parameters = array())
    {
        /** @var BootstrapComponent $this */

        $onclick = $this->addParam('onclick', $parameters, $this->getOnclickGoHome());
        $icon = $this->addParam('icon', $parameters, 'icon-nexus-back.png');
        $width = ($this->screen_width / 2) - 70;

        $row[] = $this->getComponentImage($icon, [
            'onclick' => $onclick
        ], ['width' => '30',
            'margin' => '0 0 0 0',
        ]);

        // bump
        $row[] = $this->getComponentText($title, [
            'uppercase' => true
        ], [
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'center',
            'font-size' => '18',
            'padding' => '0 0 0 15',
            'width' => $this->screen_width-100,
        ]);

        $row[] = $this->getComponentImage(
            'nexudus-logo-2.png', [], [
            'width' => '30',
            'floating' => '1',
            'margin' => '10 0 0 0',
            'float' => 'right']);

        if ($this->notch) {
            $margin = '40 15 0 15';
        } else {
            $margin = '24 15 0 15';
        }

        return $this->getComponentRow($row, [
        ], [
            'vertical-align' => 'middle',
            'background-color' => $this->color_top_bar_color,
            'padding' => $margin,
            'width' => $this->screen_width
        ]);

        $col[] = $this->getComponentText($title, [], [
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'center',
            'font-size' => '24',
            'margin' => '0 0 10 0'
        ]);

        return $this->getComponentColumn($col, [

        ], ['background-color' => $this->color_top_bar_color, 'margin' => '0 0 0 0', 'vertical-align' => 'bottom']);
    }

}
