<?php

namespace packages\actionMmenus\themes\mwb\Views;
use packages\actionMmenus\Views\Main as BootstrapView;
use packages\actionMmenus\themes\mwb\Components\Components;

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

        $col[] = $this->getComponentImage('nexudus-logo-2.png',[],['width' => '50']);
        $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center','margin' => '30 0 30 0']);

        foreach ($items as $item) {
            $this->layout->scroll[] = $this->components->uiKitMenuItem( $item );
            $this->layout->scroll[] = $this->getComponentSpacer('1', [], [
                'background-color' => '#eeeeee'
            ]);
        }

        if($this->model->getSavedVariable('logged_in')){
            $this->layout->footer[] = $this->getComponentRow([
                $this->getComponentImage('icon-nexudus-logout.png', [
                    'style' => 'uikit_menu_item_image'
                ]),
                $this->getComponentText('{#logout#}', [
                    'style' => 'uikit_menu_item_label'
                ]),
            ], [
                'onclick' => $this->getOnclickLogout(),
                'style' => 'uikit_menu_item_row'
            ]);
        }

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