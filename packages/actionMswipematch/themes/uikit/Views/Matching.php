<?php

namespace packages\actionMswipematch\themes\uikit\Views;

use Bootstrap\Views\BootstrapView;

class Matching extends BootstrapView {

    /* @var \packages\actionMswipematch\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

//        $this->model->rewriteActionConfigField('hide_menubar', 1);
//        $this->layout->header[] = $this->components->getCustomTopbar(array('logo' => 1));
        $users = $this->getData('users', 'mixed');
        $chat = $this->getData('chats', 'int');

        $this->layout->scroll[] = $this->components->getUserListing($users, array('chat' => $chat));

        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }

}