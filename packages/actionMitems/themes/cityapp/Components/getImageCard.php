<?php

namespace packages\actionMitems\themes\cityapp\Components;

use Bootstrap\Components\BootstrapComponent;

trait getImageCard
{

    public function getImageCard(string $text, string $image, array $params = [], array $styles = [])
    {
        /** @var BootstrapComponent $this */

        return $this->getComponentColumn([
            $this->getComponentColumn([
                $this->getComponentImage('cityapp-placeholder.png', [
                    'priority' => 1
                ], [
                    'width' => '100%',
                ]),
                $this->getComponentText($text, [], [
                    'color' => '#ffffff',
                    'padding' => '0 7 7 7',
                    'font-size' => ( isset($styles['width']) ? '15' : '12' ),
                ]),
            ], [], [
                'background-image' => $this->getImageFileName('cityapp-shadow-image-wide.png', [
                    'width' => '100%',
                    'priority' => 1
                ]),
                'background-size' => 'cover',
                // 'height' => '100',
                'vertical-align' => 'bottom',
            ])
        ], $params, array_merge([
            'width' => '35%',
            'margin' => '0 15 0 15',
            // 'height' => '100',
            'border-radius' => '3',
            'background-image' => $this->getImageFileName($image, [
                'width' => '100%',
                'priority' => 9
            ]),
            'background-size' => 'cover',
            'shadow-color' => '#66000000',
            'shadow-radius' => '4',
            'vertical-align' => 'bottom',
            'shadow-offset' => '0 3',
        ], $styles));
    }

}