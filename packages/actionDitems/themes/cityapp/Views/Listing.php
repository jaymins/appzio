<?php

namespace packages\actionDitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\cityapp\Components\Components as Components;

class Listing extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $images = [];

        for ($i = 1; $i < 6; $i++) {
            $images[] = $this->components->getImageCard('Some text', 'img' . $i . '.jpg');
        }

        $lat = $this->getData('lat', 'float');
        $lon = $this->getData('lon', 'float');

        $position = $lat . ',' . $lon;

        $this->layout->scroll[] = $this->getComponentMap(array(
            'position' => $position,
            'zoom' => 12,
            'map_type' => 'normal',
            'markers' => [
                [
                    'position' => $position
                ],
                [
                    'position' => '42.7,23.33333'
                ],
                [
                    'position' => '42.7,22.33333'
                ]
            ]
        ), array(
            'height' => $this->screen_height
        ));

        /*$this->layout->scroll[] = $this->getComponentColumn([], [], [
            'height' => $this->screen_height - 50,
            'background-image' => $this->getImageFileName('maps.png'),
            'background-size' => 'cover',
            'margin' => '0 0 0 0',
            'padding' => '0 0 0 0',
        ]);*/

        $layout = new \stdClass();
        $layout->width = $this->screen_width;
        $layout->bottom = '25';
        $layout->left = '0';
        // $layout->right = '15';

        $this->layout->overlay[] = $this->getComponentColumn([
            $this->getComponentRow([
                $this->getComponentText('Търси по категории', [], [
                    'font-size' => '18',
                ]),
                $this->getComponentImage('cityapp-arrow-down.png', [
                    'priority' => 1
                ], [
                    'width' => '20',
                    'floating' => '1',
                    'float' => 'right',
                ])
            ], [
                'onclick' => $this->getOnclickOpenAction('search', false, [
                    'back_button' => 1
                ])
            ], [
                'padding' => '15 15 15 15',
                'vertical-align' => 'middle'
            ]),
            $this->getComponentRow($images, [
                'scrollable' => 1,
                'animate' => 'nudge',
                'hide_scrollbar' => 1,
            ])
        ], [
            'layout' => $layout
        ]);

        return $this->layout;
    }

}