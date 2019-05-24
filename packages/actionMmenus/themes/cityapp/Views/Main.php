<?php

namespace packages\actionMmenus\themes\cityapp\Views;

use packages\actionMmenus\themes\cityapp\Components\Components;
use packages\actionMmenus\Views\Main as BootstrapView;

class Main extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');

        if (empty($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#missing_menu_items#}', [
                'style' => 'article-uikit-error'
            ]);
        }

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('cityapp-menu-close.png', [], [
                'width' => 25
            ]),
        ], [
            'onclick' => $this->getOnclickCloseSidemenu()
        ], [
            'text-align' => 'right',
            'padding' => '20 20 0 0',
        ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentImage('cityapp-menu-logo.png', [], [
                    'width' => '80',
                    'floating' => '1',
                    'float' => 'left',
                ]),
                $this->getComponentText('Карта', [
                    'onclick' => $this->getOnclickOpenAction('home')
                ], [
                    'font-size' => 33,
                    'font-ios' => 'Exo2-SemiBold',
                    'font-android' => 'Exo2-SemiBold',
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '40 0 5 0',
                ]),
                $this->getComponentText('Блог', [
                    'onclick' => $this->getOnclickOpenAction('bloglisting')
                ], [
                    'font-size' => 33,
                    'font-ios' => 'Exo2-SemiBold',
                    'font-android' => 'Exo2-SemiBold',
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 5 0',
                ]),
                $this->getComponentText('Търси изкуство', [
                    'onclick' => $this->getOnclickOpenAction('search')
                ], [
                    'font-size' => 33,
                    'font-ios' => 'Exo2-SemiBold',
                    'font-android' => 'Exo2-SemiBold',
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 5 0',
                ]),
                $this->getComponentText('Около мен', [
                    'onclick' => $this->getOnclickOpenAction('search')
                ], [
                    'font-size' => 33,
                    'font-ios' => 'Exo2-SemiBold',
                    'font-android' => 'Exo2-SemiBold',
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 5 0',
                ]),
                $this->getComponentText('Регистрация', [
                    'onclick' => $this->getOnclickOpenAction('register')
                ], [
                    'font-size' => 33,
                    'font-ios' => 'Exo2-SemiBold',
                    'font-android' => 'Exo2-SemiBold',
                    'text-align' => 'right',
                    'color' => '#171819',
                ]),
            ], [], [
        'width' => 'auto'
    ]),
        ], [], [
        'margin' => '10 20 0 40'
    ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('cityapp-menu-line.png', [
                'width' => 1000,
                'priority' => 1
            ], [
                'width' => '60%'
            ])
        ], [], [
            'text-align' => 'right',
            'margin' => '40 20 40 0',
        ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentText('Регистрация на артисти', [], [
                    'font-size' => 25,
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 15 0',
                ]),
                $this->getComponentText('За уличното изкуство', [], [
                    'font-size' => 25,
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 15 0',
                ]),
                $this->getComponentText('Съобщете за нередност', [], [
                    'font-size' => 25,
                    'text-align' => 'right',
                    'color' => '#171819',
                    'padding' => '0 0 15 0',
                ]),
                $this->getComponentText('За апликацията', [], [
                    'font-size' => 25,
                    'text-align' => 'right',
                    'color' => '#171819',
                ]),
            ], [], [
                'width' => 'auto'
            ]),
        ], [], [
            'margin' => '0 20 0 40'
        ]);

        return $this->layout;
    }

}