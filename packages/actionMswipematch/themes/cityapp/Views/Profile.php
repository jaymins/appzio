<?php

namespace packages\actionMswipematch\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMswipematch\themes\cityapp\Components\Components;

class Profile extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentImage('img2.jpg', [
                    'priority' => 9
                ], [
                    'width' => 80,
                    'height' => 80,
                    'crop' => 'round',
                    'margin' => '0 0 10 0',
                ]),
                $this->getComponentRow([
                    $this->getComponentText('Artworks: ', [], [
                        'font-size' => 12
                    ]),
                    $this->getComponentText('16', [], [
                        'font-size' => 12,
                        'font-weight' => 'bold',
                    ])
                ], [], [
                    'margin' => '0 0 7 0',
                ]),
                $this->getComponentWrapRow([
                    $this->getComponentText('City: ', [], [
                        'font-size' => 12
                    ]),
                    $this->getComponentText('Sofia, Bulgaria', [], [
                        'font-size' => 12,
                        'font-weight' => 'bold',
                    ])
                ]),
            ], [], [
                'width' => '30%'
            ]),
            $this->getComponentColumn([
                $this->getComponentText('Nasimo', [], [
                    'font-size' => 24,
                    'font-weight' => 'bold',
                    'color' => '#ef4345',
                ]),
                $this->getComponentText('Станислав Трифонов', [], [
                    'font-size' => 21,
                    'color' => '#343536',
                ]),
                $this->getComponentText('Nasimo или Станислав Трифонов, както е името му по лична карта, трудно се побира в рамки. Той е графити артист.', [], [
                    'font-size' => 14,
                    'color' => '#383838',
                    'padding' => '15 0 0 0',
                ])
            ], [], [
                'width' => 'auto',
                'padding' => '0 0 0 15',
            ])
        ], [], [
            'margin' => '15 15 15 15',
        ]);

        $this->getAuthoredArt();

        return $this->layout;
    }

    private function getAuthoredArt()
    {

        $items = $this->getData('art_items', 'mixed');

        if ( empty($items) ) {
            return false;
        }

        $rows = array_chunk($items, 2);

        foreach ($rows as $i => $row) {

            $items = [];

            foreach ($row as $j => $item) {

                $featured_image = $this->getFeaturedImage( $item->images_data );

                $items[] = $this->components->getImageCard($item->name, $featured_image, [
                    'onclick' => $this->getOnclickOpenAction('viewart', false, [
                        'id' => $item->id,
                        'back_button' => 1
                    ])
                ], [
                    'width' => '50%',
                    'margin' => ( !$j ? '0 7 0 0' : '0 0 0 7' ),
                ]);
            }

            $this->layout->scroll[] = $this->getComponentRow($items, [], [
                'margin' => ( !$i ? '15 15 15 15' : '0 15 15 15' )
            ]);
        }

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('Виж всички', [], [
                'font-size' => 19,
                'color' => '#ffffff',
                'background-color' => '#ef4345',
                'padding' => '10 15 10 15',
                'border-radius' => '8',
            ])
        ], [], [
            'text-align' => 'center',
            'margin' => '15 15 15 15',
        ]);

        return true;
    }

    private function getFeaturedImage($images_data)
    {

        if ( empty($images_data) ) {
            // To do: return a placeholder
            return 'cityapp-placeholder.png';
        }

        foreach ($images_data as $image) {
            if ( $image->featured ) {
                return $image->image;
            }
        }

        return 'cityapp-placeholder.png';
    }

}