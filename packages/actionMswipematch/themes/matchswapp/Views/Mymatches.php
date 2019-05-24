<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;


class Mymatches extends \packages\actionMswipematch\Views\Mymatches {

    /* @var \packages\actionMswipematch\themes\matchswapp\Components\Components */
    public $components;
    public $theme;
    public $match_data;

    public function __construct($obj) {
        parent::__construct($obj);
    }

    /* matches */
    public function tab1(){
        $this->layout = new \stdClass();
        $this->match_data = $this->getData('matches', 'array');
        $this->tab(1);

        if(isset($this->match_data['two-way-matches'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['two-way-matches'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ms_icon_broken_heart.png',
                'icon_chat' => 'ms_icon_chat.png',
                'play_id' => $this->model->playid,
                //'icon_bookmark' => 'ig_bookmark.png',
                //'icon_bookmark_active' => 'ig_bookmark_active.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 1,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        } else {
            $col[] = $this->getComponentImage('ghost_icon_outline.png',[],['width' => '30%','margin' => '0 0 20 0']);
            $col[] = $this->getComponentText('{#once_you_have_a_match_you_will_see_the_people_here#}',[],['text-align' => 'center','font-size' => 13]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 80 40 80','text-align' => 'center','opacity' => '0.5']);

        }

        return $this->layout;
    }


    /* I like */
    public function tab2(){
        $this->layout = new \stdClass();
        $this->tab(2);
        $this->match_data = $this->getData('matches', 'array');

        if(isset($this->match_data['matches'])){
            $this->layout->scroll[] = $this->uiKitSearchField(['filter' => 1]);

            $this->layout->scroll[] = $this->components->uiKitPeopleListWithLikes($this->match_data['matches'], [
                'user_info' => $this->model->getActionidByPermaname('userinfo'),
                'icon_dont_like' => 'ms_icon_broken_heart.png',
                'icon_bookmark' => 'uikit_icon_slimbookmark.png',
                'icon_bookmark_active' => 'uikit_icon_slimbookmark_active.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 2,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }else {
            $col[] = $this->getComponentImage('ghost_icon_outline.png',[],['width' => '30%','margin' => '0 0 20 0']);
            $col[] = $this->getComponentText('{#once_you_like_someone_you_can_find_them_here#}',[],['text-align' => 'center','font-size' => 13]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 80 40 80','text-align' => 'center','opacity' => '0.5']);

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
                //'icon_dont_like' => 'ig_nope.png',
                'icon_bookmark' => 'uikit_icon_slimbookmark.png',
                'icon_bookmark_active' => 'uikit_icon_slimbookmark_active.png',
                'icon_like' => 'uikit_icon_slimheart.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 3,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }else {
            $col[] = $this->getComponentImage('ghost_icon_outline.png',[],['width' => '30%','margin' => '0 0 20 0']);
            $col[] = $this->getComponentText('{#once_someone_likes_you_they_can_be_seen_here#}',[],['text-align' => 'center','font-size' => 13]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 80 40 80','text-align' => 'center','opacity' => '0.5']);
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
                //'icon_dont_like' => 'ig_nope.png',
                'icon_bookmark' => 'uikit_icon_slimbookmark.png',
                'icon_bookmark_active' => 'uikit_icon_slimbookmark_active.png',
                'icon_like' => 'uikit_icon_slimheart.png',
                'bookmark_route' => 'mymatches/',
                'tab' => 4,
                'debug' => true,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }else {
            $col[] = $this->getComponentImage('ghost_icon_outline.png',[],['width' => '30%','margin' => '0 0 20 0']);
            $col[] = $this->getComponentText('{#once_you_bookmark_a_user_you_can_find_them_here#}',[],['text-align' => 'center','font-size' => 13]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 80 40 80','text-align' => 'center','opacity' => '0.5']);
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
                //'icon_dont_like' => 'ig_nope.png',
                //'icon_bookmark' => 'uikit_icon_slimbookmark.png',
                //'icon_bookmark_active' => 'uikit_icon_slimbookmark_active.png',
                //'bookmark_route' => 'mymatches/',
                'tab' => 5,
                'like_route' => 'mymatches/',
                'extra_icon' => 'follow-instagram.png',
                'instaclick_command' => 'Controller/recordinstaclick/'
            ]);
        }else {
            $col[] = $this->getComponentImage('ghost_icon_outline.png',[],['width' => '30%','margin' => '0 0 20 0']);
            $col[] = $this->getComponentText('{#any_blocked_users_will_appear_here#}',[],['text-align' => 'center','font-size' => 13]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '40 80 40 80','text-align' => 'center','opacity' => '0.5']);
        }
        return $this->layout;
    }

    public function tab($num){

        $this->layout->overlay[] = $this->components->getCheckinFloatingButton('messaging');
        $this->layout->overlay[] = $this->components->getListSwipeFloatingButton('mymatches');

        /* top bar if logo is set */
        $params['mode'] = 'sidemenu';
        $params['hairline'] = '#e5e5e5';
        $params['icon_color'] = $this->getData('icon_color', 'string');;
        $params['title'] = $this->model->getConfigParam('subject');

        if(!empty($menu)){
            $params['right_menu'] = $menu;
        }

        $this->layout->header[] = $this->components->uiKitFauxTopBar($params);

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
                'text' => strtoupper('{#hidden#}'),
                'active' => $num == 5 ? true : false,
                'onclick' => $this->getOnclickTab(5)
            ),
        ],[],['font-size' => '11','active_tab_color' => '#BDB893']);

    }
}