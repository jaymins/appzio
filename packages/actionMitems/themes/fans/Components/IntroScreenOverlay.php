<?php

namespace packages\actionMitems\themes\fans\Components;

use Bootstrap\Components\BootstrapComponent;

trait IntroScreenOverlay
{
    public $isLiked;

    public function introScreenOverlay($params = array())
    {
        /* this is outside of the component, so that it can be included in the overlay */
        $layout = new \stdClass();
        $layout->bottom = -25;
        $layout->height = '80';
        $layout->center = 0;
        $layout->width = $this->screen_width;

        return $this->getComponentSwipeAreaNavigation('#000000','#B2B4B3',
            array('layout' => $layout)
        );

    }
}