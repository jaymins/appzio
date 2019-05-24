<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Grid extends BootstrapView {

    /* @var \packages\actionMswipematch\themes\matchswapp\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();
        $users = $this->getData('users', 'mixed');
        $chat = $this->getData('chats', 'int');

        $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($users, [
            'user_info' => $this->model->getActionidByPermaname('userinfo'),
            'icon_dont_like' => 'ig_nope.png',
            'icon_bookmark' => 'ig_bookmark.png',
            'icon_bookmark_active' => 'ig_bookmark_active.png',
            'icon_like' => 'ig_like.png',
            'bookmark_route' => 'grid/',
            'like_route' => 'grid/',
            'extra_icon' => 'follow-instagram.png',
        ]);
        return $this->layout;
    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();
        return $divs;
    }

}