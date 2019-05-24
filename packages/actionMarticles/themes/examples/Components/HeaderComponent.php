<?php

namespace packages\actionMarticles\themes\examples\Components;

use Bootstrap\Components\BootstrapComponent;

trait HeaderComponent
{
    public function HeaderComponent($page, $params = [])
    {
        return $this->getComponentText($page, [], [
            'padding' => '20 15 20 15',
            'font-size' => '25',
            'text-align' => 'right'
        ]);
    }

    public function BackBtn($route = 'Controller/Default')
    {
        return $this->uiKitFloatingButtons([
            [
                'icon' => 'layouts-exit-icon.png',
                'onclick' => [
                    $this->getOnclickOpenAction('examples')
                ]
            ],
        ], true);

    }

    public function FullScreenBtn($tab)
    {
        return $this->uiKitFloatingButtons([
            [
                'icon' => 'fullscreen.png',
                'onclick' => [
                    $this->getOnclickTab($tab)
                ]
            ],
        ], true,[],['width'=>'10']);

    }
}