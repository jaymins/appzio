<?php

namespace packages\actionMmenus\themes\adidas\Views;
use packages\actionMmenus\Views\Main as BootstrapView;
use packages\actionMmenus\themes\adidas\Components\Components;

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

        if ( $this->getData('show_profile', 'bool') ) {
            $this->getHeading();
        }

        foreach ($items as $item) {
            $this->layout->scroll[] = $this->components->uiKitMenuItem( $item );
            $this->layout->scroll[] = $this->getComponentSpacer('1', [], [
                'background-color' => '#eeeeee'
            ]);
        }

        $this->layout->footer[] = $this->getComponentRow([
            $this->getComponentImage('tatjack-icon-logout.png', [
                'style' => 'uikit_menu_item_image'
            ]),
            $this->getComponentText('Logout', [
                'style' => 'uikit_menu_item_label'
            ]),
        ], [
            'onclick' => $this->getOnclickOpenAction('logout'),
            'style' => 'uikit_menu_item_row'
        ]);

        return $this->layout;
    }

    private function getHeading() {

        $profile_pic = $this->getData('profilepic', 'string');
        $name = $this->getData('name', 'string');

        $this->layout->scroll[] = $this->components->uiKitMenuProfilebox($profile_pic, $name, [
            'onclick' => $this->getOnclickOpenAction($this->getData('profile_path', 'string')),
        ]);

        $this->layout->scroll[] = $this->getComponentSpacer('1', [], [
            'background-color' => '#eeeeee'
        ]);

    }

}