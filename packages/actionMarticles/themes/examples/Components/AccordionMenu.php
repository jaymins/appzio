<?php

namespace packages\actionMarticles\themes\examples\Components;

use Bootstrap\Components\BootstrapComponent;

trait AccordionMenu
{
    public function AccordionMenu()
    {
        $categories = [
         [
              'show' => array (
                   'title' => 'Article',
                   'description' => 'Articles',
              ),
               'hide' => array (
                   'title' => 'Article',
                   'description' => 'Article hide',
              ),
               'hidden' => [
                  'description' => 'hidden Article',
             ]
          ],
            [
                'show' => array (
                    'title' => 'Article2',
                    'description' => 'Articles',
                ),
                'hide' => array (
                    'title' => 'Article2',
                    'description' => 'Article hide',
                ),
                'hidden' => [
                    'description' => [$this->getComponentRow([
                        $this->getComponentText('Article1')
                    ])],
                ]
            ]

      ];

        return $this->uiKitAccordion($categories,[
            'width'=>'100%'
        ]);
    }
}