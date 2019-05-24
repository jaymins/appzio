<?php

namespace packages\actionMitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\cityapp\Components\Components as Components;

class Single extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    private $art_item;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->art_item = $this->getData('art_item', 'object');

        if ( empty($this->art_item->name) ) {
            $this->layout->scroll[] = $this->getComponentText('{#missing_id#}');
            return $this->layout;
        }

        $this->layout->header[] = $this->getComponentText($this->art_item->name, [], [
            'font-size' => 24,
            'font-weight' => 'bold',
            'color' => '#ef4345',
            'padding' => '20 15 20 15',
        ]);

        $this->getArtImages();

        $this->layout->scroll[] = $this->getComponentText('Снимка на: Benjamin Balazs, CCO', [], [
            'font-size' => '15',
            'font-style' => 'italic',
            'color' => '#6c6c6c',
            'margin' => '15 15 15 15',
        ]);

        $this->getArtDescription();
        $this->getArtistBlock();
        $this->getArtMap();

        return $this->layout;
    }

    private function getArtImages()
    {

        $images = $this->art_item->images_data;

        if ( empty($images) ) {
            return false;
        }

        $count = 0;

        foreach ($images as $image) {
            if ( $image->featured )
                continue;

            $count++;

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentImage($image->image, [
                    'priority' => 9,
                    'imgwidth' => '2048',
                    'click_hilite' => 'none',
                    'tap_to_open' => 1,
                    'lazy' => 1,
                ], [
                    'width' => $this->screen_width,
                ])
            ], [], [
                'margin' => ( $count == 1 ? '15 15 15 15' : '0 15 15 15' ),
            ]);

        }

        return true;
    }

    private function getArtDescription()
    {

        if ( empty($this->art_item->description) ) {
            return false;
        }

        $this->layout->scroll[] = $this->getComponentText($this->art_item->description, [], [
            'font-size' => '15',
            'color' => '#393939',
            'margin' => '15 15 15 15',
        ]);

        return true;
    }

    private function getArtistBlock()
    {

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('img2.jpg', [
                'priority' => 9
            ], [
                'width' => 60,
                'height' => 60,
                'crop' => 'round',
                'margin' => '0 10 10 0',
            ]),
            $this->getComponentText('Артист: ', [], [
                'font-size' => 20,
                'color' => '#393939',
                'font-weight' => 'bold',
            ]),
            $this->getComponentText('Насимо', [], [
                'font-size' => 20,
                'color' => '#ef4345',
                'font-weight' => 'bold',
            ])
        ], [
            'onclick' => $this->getOnclickOpenAction('artistview', '', [
                'id' => $this->art_item->play_id,
                'sync_open' => 1,
                'back_button' => 1,
            ])
        ], [
            'vertical-align' => 'middle',
            'margin' => '0 15 0 15',
        ]);

    }

    private function getArtMap()
    {

        $lat = $this->art_item->lat;
        $lon = $this->art_item->lon;

        if ( empty($lat) OR empty($lon) ) {
            return false;
        }

        $position = $lat . ',' . $lon;

        $this->layout->scroll[] = $this->getComponentMap(array(
            'position' => $position,
            'zoom' => 15,
            'map_type' => 'terrain',
            'markers' => array(
                array(
                    'position' => $position,
                    'onclick' => $this->getOnclickOpenAction('search')
                    // 'icon' => 'places-api.bmp'
                )
            )
        ), array(
            'margin' => '15 0 0 0',
            'height' => 300
        ));

        return true;
    }

}