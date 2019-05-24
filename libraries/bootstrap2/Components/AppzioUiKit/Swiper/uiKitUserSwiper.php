<?php

namespace Bootstrap\Components\AppzioUiKit\Swiper;
use Bootstrap\Components\BootstrapComponent;

trait uiKitUserSwiper {

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

    public function uiKitUserSwiper($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));
        }

        $count = 0;

        foreach($content as $item){
            $swiper[] = $this->getFeaturedUser($item);
            $count++;
        }

        if(isset($swiper)){
            $out[] = $this->getComponentSwipeStack(
                $swiper,
                array(
                    'id' => 'swipe_container',
                    'overlay_left' => $this->getComponentImage('uikit_swipe_nope_overlay.png',array('text-align' => 'right','width'=> '350','height'=> '350')),
                    'overlay_right' => $this->getComponentImage('uikit_swipe_like_overlay.png',array('text-align' => 'left','width'=> '350','height'=> '350'))
                ),[]);
            return $this->getComponentColumn($out);
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));
        
    }

	private function getFeaturedUser($content){

        $id = isset($content['play_id']) ? $content['play_id'] : false;

        if(!$id){
            return $this->getComponentText('Missing user play_id');
        }

        $profilepic = $content['profilepic'] ? $content['profilepic'] : 'icon_camera-grey.png';

        $width = $this->screen_width;
        $height = $this->screen_height - 150;

        $ratio = $width/$height;
        $imgheight = round(900*$ratio,0);

        $col[] = $this->getComponentImage($profilepic,[
            'imgwidth' => 900,
            'imgheight' => 600,
            'onclick' => $this->uiKitOpenProfile($id),
            'priority' => '9'],[
                'crop' => 'yes',
                'width' => $width,
                'height' => $height]);

        $name = isset($content['firstname']) ? $content['firstname'] : '{#anonymous#}';

        if(isset($content['age']) AND $content['age']){
            $name .= ', '.$content['age'];
        } elseif(isset($content['birth_year'])){
            $name .= ', ' .date('Y') - $content['birth_year'];
        }

        $row[] = $this->getComponentText($name,[],['color' => $this->color_top_bar_text_color,'font-size' => 16]);

        if(isset($content['instagram_username']) AND $content['instagram_username']){
            $row[] = $this->getComponentImage('uikit_swipe_insta.png',['style' => 'ukit_user_swiper_insta',
                'onclick' => $this->getOnclickOpenUrl('https://instagram.com/'.$content['instagram_username'])]);
        }

        $col[] = $this->getComponentRow($row,[],['padding' => '10 15 10 15','background-color' =>
        $this->color_top_bar_color,'vertical-align' => 'middle']);


        $col[] =


        $out[] = $this->getComponentColumn($col, array(
            'style' => 'ukit_user_swiper'
        ));

        $out2[] = $this->getComponentColumn($out,[
            'leftswipeid' => 'left' . $id,
            'rightswipeid' => 'right' . $id,
        ],['text-align' => 'center','width' => '100%']);

        if(isset($content['bookmark']) AND $content['bookmark']){
            $params['is_bookmarked'] = true;
        }

        $params['id'] = $id;

        $out2[] = $this->getComponentSpacer(25);
        $out2[] = $this->uiKitUserSwiperControls($params,['margin' => '-150 0 0 0']);

        return $this->getComponentColumn($out2);

    }

}