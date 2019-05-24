<?php

namespace packages\actionMarticles\themes\examples\Views;

use packages\actionMarticles\Views\View as BootstrapView;
use packages\actionMarticles\themes\layouts\Components\Components;
use packages\actionMarticles\Models\Model as ArticleModel;

class Mainview extends BootstrapView
{

    /* @var ArticleModel */
    public $model;

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;
    private $modules;
    private $items;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->modules = $this->getData('modules', 'array');
        $this->items = $this->getData('components', 'array');

        $this->model->flushActionRoutes();

        $this->layout->scroll[] = $this->components->uiKitSearchItemUITEST();
        $this->layout->scroll[] = $this->getComponentText('Purpose of Appzio UI Kit is to help develop applications with a more unified look-and-feel and speed up', [], [
            'padding' => '15 20 25 20'
        ]);
        foreach ($this->modules as $key => $module) {
            if ($module && isset($this->items[$module])) {
                $listComponents[$module] = $this->items[$module];
            }
        }
        foreach ($this->modules as $id => $list_item) {
            if (!$list_item) {
                continue;
            }
            $this->getListComponents($list_item);

            $this->layout->scroll[] =
                $this->getComponentColumn([
                    $this->getComponentRow([
                        $this->components->uiKitListItem($list_item, [
                            'id' => 'show-' . $id,
                            'visibility' => 'visible'
                        ], [
                            'left_icon' => 'folder.png',
                            'divider' => true,
                            'onclick' => [
                                $this->getOnclickShowElement('menu-' . $id, ['transition' => 'none']),
                                $this->getOnclickShowElement('hide-' . $id, ['transition' => 'none']),
                                $this->getOnclickHideElement('show-' . $id, ['transition' => 'none']),
                            ],
                        ], [
                            'font-size' => '10px;'
                        ]),], [
                        'id' => 'show-' . $id,
                        'visibility' => 'visible'
                    ]),
                    $this->getComponentRow([
                        $this->components->uiKitListItem($list_item, [
                            'id' => 'hide-' . $id,
                            'visibility' => 'hidden'
                        ], [
                            'left_icon' => 'icon-minus-blue.png',
                            'divider' => true,
                            'onclick' => [
                                $this->getOnclickHideElement('menu-' . $id, ['transition' => 'none']),
                                $this->getOnclickHideElement('hide-' . $id, ['transition' => 'none']),
                                $this->getOnclickShowElement('show-' . $id, ['transition' => 'none']),
                            ],
                        ], [
                            'font-size' => '10px;'
                        ]),], [
                        'id' => 'hide-' . $id,
                        'visibility' => 'hidden'
                    ]),

                    $this->getComponentRow([
                            $this->getComponentColumn($this->getListComponents($list_item, $id), [], [
                                'padding' => '0 0 0 30',
                                'width' => '100%'
                            ])
                        ]
                        , [
                            'id' => 'menu-' . $id,
                            'visibility' => 'hidden'
                        ], [])
                ], [], [
                    'padding' => '0 0 0 0',
                    'vertical-align' => 'middle',
                ]);
        }

        return $this->layout;
    }

    private function getListComponents($model)
    {
        $listComponents = [];
        if ($model && isset($this->items[$model]) && is_array($this->items[$model])) {
            $listComponents = $this->items[$model];
        }
        $components = [];
        foreach ($listComponents as $component) {
            $components[] = $this->components->uiKitListItem($component, [
            ], [
                'left_icon' => 'file.png',
                'divider' => true,
                'onclick' => [
                    $this->getOnclickOpenAction('examplesview', false, [
                        'id' => $component,
                        'sync_open' => true,
                        'back_button' => true
                    ])
                ],
            ], []);
        }
        return $components;
    }
}