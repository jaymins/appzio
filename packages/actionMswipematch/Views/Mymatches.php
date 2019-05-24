<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Mymatches extends BootstrapView {

    /* @var \packages\actionMswipematch\themes\igers\Components\Components */
    public $components;
    public $theme;
    public $match_data;

    public function __construct($obj) {
        parent::__construct($obj);
    }


    /*        $users = $this->getData('users', 'mixed');
        $chat = $this->getData('chats', 'int');

        $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($users, [
            'user_info' => $this->model->getActionidByPermaname('userinfo'),
            'icon_dont_like' => 'ig_nope.png',
            'icon_bookmark' => 'ig_bookmark.png',
            'icon_bookmark_active' => 'ig_bookmark_active.png',
            'icon_like' => 'ig_like.png',
            'bookmark_route' => 'mymatches/',
            'like_route' => 'mymatches/',
            'extra_icon' => 'follow-instagram.png',
        ]);*/

    /* matches */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->match_data = $this->getData('matches', 'array');
        $this->tab(1);

        if(isset($this->match_data['two-way-matches'])){

            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['two-way-matches'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ig_nope.png',
                'icon_chat' => 'blue-chat-icon.png',
                'play_id' => $this->model->playid,
                //'icon_bookmark' => 'ig_bookmark.png',
                //'icon_bookmark_active' => 'ig_bookmark_active.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 1,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }

        return $this->layout;
    }

    /* I like */
    public function tab2(){
        $this->layout = new \stdClass();
        $this->tab(2);

        if(isset($this->match_data['matches'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['matches'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ig_nope.png',
                //'icon_bookmark' => 'ig_bookmark.png',
                //'icon_bookmark_active' => 'ig_bookmark_active.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 2,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }

        return $this->layout;
    }

    /* liked me */
    public function tab3(){
        $this->layout = new \stdClass();
        $this->tab(3);
        if(isset($this->match_data['like_me'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['like_me'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ig_nope.png',
                //'icon_bookmark' => 'ig_bookmark.png',
                //'icon_bookmark_active' => 'ig_bookmark_active.png',
                'icon_like' => 'ig_like.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 3,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }

        return $this->layout;
    }

    /* bookmarks */
    public function tab4(){
        $this->layout = new \stdClass();
        $this->tab(4);
        if(isset($this->match_data['bookmark'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['bookmark'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ig_nope.png',
                'icon_bookmark' => 'ig_bookmark.png',
                'icon_bookmark_active' => 'ig_bookmark_active.png',
                'icon_like' => 'ig_like.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 4,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }

        return $this->layout;
    }

    /* blocked */
    public function tab5(){
        $this->layout = new \stdClass();
        $this->tab(5);
        if(isset($this->match_data['blocked'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['blocked'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ig_nope.png',
                //'icon_bookmark' => 'ig_bookmark.png',
                //'icon_bookmark_active' => 'ig_bookmark_active.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 5,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }
        return $this->layout;
    }

    public function tab($num){
        $this->layout->header[] = $this->uiKitTabNavigation([
            array(
                'text' => strtoupper('{#matches#}'),
                'active' => $num == 1 ? true : false,
                'onclick' => $this->getOnclickTab(1)
            ),
            array(
                'text' => strtoupper('{#i_like#}'),
                'active' => $num == 2 ? true : false,
                'onclick' => $this->getOnclickTab(2)
            ),
            array(
                'text' => strtoupper('{#liked_me#}'),
                'active' => $num == 3 ? true : false,
                'onclick' => $this->getOnclickTab(3)
            ),
            array(
                'text' => strtoupper('{#bookmarks#}'),
                'active' => $num == 4 ? true : false,
                'onclick' => $this->getOnclickTab(4)
            ),
            array(
                'text' => strtoupper('{#blocked#}'),
                'active' => $num == 5 ? true : false,
                'onclick' => $this->getOnclickTab(5)
            ),
        ],[],['font-size' => '11']);

    }

    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        return $divs;
    }
}