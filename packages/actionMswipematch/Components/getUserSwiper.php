<?php

namespace packages\actionMswipematch\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getUserSwiper {

    public $page = 0;

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getUserSwiper($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */


        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));
        }

        $count = 0;
        foreach($content as $item){

            $userData = $this->model->foreignVariablesGet($item['play_id']);
            $userData['playid'] = $item['play_id'];
            $swiper[] = $this->getFeaturedUser($userData);
            $count++;
        }


        if(isset($swiper)){
            $out[] = $this->getSwipeStack(
                $swiper,
                array(
                    'id' => 'swipe_container',
                    'remember_position' => 1,
                    'transition' => 'tablet',
                    'dynamic' => 1
                ));
            return $this->getComponentColumn($out);
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));



    }

	private function getFeaturedUser($content){
        $icon = $content['profilepic'] ? $content['profilepic'] : 'icon_camera-grey.png';

        $col[] = $this->getComponentImage($icon,array(
            'style' => 'matching_featured_image','imgwidth' => '300','imgheight' => '300'));

        $swipeRight[]  = $this->getOnclickSwipeStackControl('swipe_container', 'right');
        $swipeLeft[] = $this->getOnclickSwipeStackControl('swipe_container', 'left');

        $col[] = $this->getComponentRow([
            $this->getComponentText('Dislike', array('onclick' => $swipeLeft), array('color' => "#FFFFFF")),
            $this->getComponentText('Like', array('onclick' => $swipeRight), array('color' => "#FFFFFF")),
        ]);

        $action = $this->getOnclickOpenAction(
            'profile',
            false,
            array('sync_open' => 1, 'back_button' => 1),
            'Profile/default/open_match-' . $content['playid'],
            false,
            array('profile_id' => $content['playid'])
        );

        return
            $this->getComponentColumn(
                $col,
                array(
                    'onclick' => $action,
                    'leftswipeid' => 'left' . $content['playid'],
                    'rightswipeid' => 'right' . $content['playid'],
                    'style' => 'matching_featured_column'
                )
            );
    }


    public function getSwipeStack($content, $parmas = array()) {
        $obj = new \stdClass();
        $obj->type = 'swipestack';

        $obj->swipe_content = $content;

        $allowed = array(
            'id','swipe_content','overlay_left','overlay_right','rightswipeid','leftswipeid'
        );

        foreach ($allowed as $param) {
            if ( isset($parmas[$param]) ) {
                $obj->$param  = $parmas[$param];
            }
        }

        return $obj;
    }

}
