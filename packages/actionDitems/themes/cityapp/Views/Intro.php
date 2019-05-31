<?php

namespace packages\actionDitems\themes\cityapp\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\cityapp\Components\Components as Components;

class Intro extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;


    public function tab1() {
        $this->layout = new \stdClass();

        $this->renderIntroSlider();

        return $this->layout;
    }

    public function renderIntroSlider()
    {

        $content = [];

        $slides = $this->getData('intro','mixed');

        foreach ($slides as $i => $slide) {

            $events = [];

            if ( $i+1 == count($slides) ) {
                $events['onclick'] = [
                    $this->getOnclickCompleteAction(),
                    $this->getOnclickOpenAction('home'),
                ];
            }

            $content[] = $this->getComponentColumn([], $events, [
                'background-image' => $this->getImageFileName($slide),
                'background-size' => 'cover',
                'height' => $this->screen_height,
            ]);
        }

        $this->layout->scroll[] = $this->getComponentSwipe($content, [], [
            'height' => $this->screen_height,
        ]);

        $layout = new \stdClass();
        $layout->bottom = 0;
        $layout->height = '80';
        $layout->center = 0;
        $layout->width = $this->screen_width;

        $this->layout->overlay[] = $this->getComponentSwipeAreaNavigation('#000000','#B2B4B3', [
            'layout' => $layout
        ]);

    }

}