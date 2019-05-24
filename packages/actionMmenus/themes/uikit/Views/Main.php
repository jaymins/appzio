<?php

namespace packages\actionMmenus\themes\uikit\Views;
use packages\actionMmenus\Views\Main as BootstrapView;
use packages\actionMmenus\themes\uikit\Components\Components;

class Main extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');

        if ( empty($items) ) {
            $this->layout->scroll[] = $this->getComponentText('{#missing_menu_items#}', [
                'style' => 'article-uikit-error'
            ]);
        }

        foreach ($items as $item) {
            $this->layout->scroll[] = $this->components->uiKitMenuItem( $item );
            $this->layout->scroll[] = $this->getComponentSpacer('1', [], [
                'background-color' => '#eeeeee'
            ]);
        }

        return $this->layout;
    }

}