<?php

namespace Bootstrap\Components\AppzioUiKit\Listing;

use Bootstrap\Components\BootstrapComponent;

trait uiKitInfiniteUserList
{

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

    public function uiKitInfiniteUserList($content, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapComponent $this */

        $id = isset($parameters['id']) ? $parameters['id'] : 'swipe_container';

        if (!is_array($content) OR empty($content)) {
            return $this->getComponentText('{#sorry_no_users_found_at_the_moment_with_your_filter_selection#}', [], [
                'text-align' => 'center', 'font-size' => '14', 'color' => '#B2B4B3', 'margin' => '10 40 10 40']);
        }

        $page = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] : 1;
        $page++;
        $count = 1;

        if (isset($parameters['ad_threshold']) AND isset($parameters['ad_id']) AND isset($parameters['ad_size'])) {
            $advertising = true;
        } else {
            $advertising = false;
        }

        foreach ($content as $item) {
            $usr = $this->getFeaturedUserForList($item, $parameters, $count);
            if ($usr) {
                $swiper[] = $usr;
                $count++;
                if ($advertising AND $count == $parameters['ad_threshold']) {
                    $swiper[] = $this->getBannerAd($parameters['ad_id'], $parameters['ad_size']);
                    $count = 1;
                }
            }
        }

        if (isset($swiper)) {
            $out[] = $this->getComponentColumn(
                $swiper,
                array(
                    'id' => $id
                ), []);

            return $this->getInfiniteScroll($out, array(
                'next_page_id' => $page,
                'show_loader' => 1,
            ), ['background-color' => '#ffffff']);
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}', array('style' => 'steps_error2'));

    }

    private function getFeaturedUserForList($content, $parameters)
    {

        $id = isset($content['play_id']) ? $content['play_id'] : false;

        if (!$id) {
            return $this->getComponentText('Missing user play_id');
        }

        $profilepic = $content['profilepic'] ? $content['profilepic'] : 'icon_camera-grey.png';
        $profilepic2 = $content['profilepic2'] ? $content['profilepic2'] : false;
        $profilepic3 = $content['profilepic3'] ? $content['profilepic3'] : false;
        $profilepic4 = $content['profilepic4'] ? $content['profilepic4'] : false;
        $profilepic5 = $content['profilepic5'] ? $content['profilepic5'] : false;

        $name = $this->getNickname($content);

        $unlikeaction = isset($parameters['unlike_action']) ? $parameters['unlike_action'] : 'infinite/unlike/' . $id;
        $dounlike[] = $this->getOnclickHideElement('user_' . $id);
        $dounlike[] = $this->getOnclickSubmit($unlikeaction);

        if (isset($content['current_venue']) AND $content['current_venue']) {
            $location = $content['current_venue'];
        } elseif (isset($content['city'])) {
            $location = $content['city'];
        } else {
            $location = '';
        }

        $test = $this->getImageFileName($profilepic);

        if(!$test){
            return false;
        }
        
        /* top row */
        $col[] = $this->getComponentRow([
            $this->getComponentImage($profilepic, [
                'imgwidth' => '400',
                'imgheight' => '400',
                'imgcrop' => 'yes',
                'format' => 'jpg',
                'priority' => 9,
                'click_hilite' => 'none',
                'onclick' => $this->uiKitOpenProfile($id)],
                [
                    'crop' => 'round',
                    'margin' => '10 10 10 10',
                    'height' => '35',
                    'width' => '35'
                ]),
            $this->getComponentColumn(
                [
                    $this->getComponentText($name, [], ['font-size' => '16']),
                    $this->getComponentText($location, [], ['font-size' => '14'])
                ], ['onclick' => $this->uiKitOpenProfile($id)], ['vertical-align' => 'middle']),

            $this->getComponentText('{#hide#}', [
                'onclick' => $dounlike,
                'style' => 'uikit_list_hide_button'])

        ], [], ['vertical-align' => 'middle']
        );

        $width = $this->screen_width;
        $height = round($this->screen_width / 1.4, 0);

        $original_dimensions = $this->addParam('original_image_dimensions', $parameters);

        if ($original_dimensions) {
            $params = [
                'imgwidth' => 1200,
                'click_hilite' => 'none',
                'onclick' => $this->uiKitOpenProfile($id),
                'priority' => '9'];

            $styles = [
                'width' => $width,
            ];
        } else {
            $params = [
                'imgwidth' => 1200,
                'imgheight' => 867,
                'format' => 'jpg',
                'click_hilite' => 'none',
                'onclick' => $this->uiKitOpenProfile($id),
                'priority' => '9'];

            $styles = [
                'crop' => 'yes',
                'width' => $width,
                'height' => $height];
        }

        if ($profilepic2) {
            $pics[] = $this->getComponentImage($profilepic, $params, $styles);
            $pics[] = $this->getComponentImage($profilepic2, $params, $styles);

            if ($profilepic3) {
                $pics[] = $this->getComponentImage($profilepic3, $params, $styles);
            }

            $col[] = $this->getComponentSwipe($pics, ['id' => 'swiper' . $id], [
                'background-color' => '#000000',
                'width' => $width,
                'height' => $height]);

            $col[] = $this->getComponentSwipeAreaNavigation('#00BED2', '#E4E7E9', ['swipe_id' => 'swiper' . $id],
                ['margin' => '-40 0 0 0', 'text-align' => 'center', 'width' => '100%']);
        } else {
            $col[] = $this->getComponentImage($profilepic, $params, $styles);
        }


        /* bottom buttons */

        $controls = $this->UKIULGetBottom($parameters, $content, $id, $dounlike);
        $col[] = $this->getComponentRow($controls, [], ['width' => '100%', 'vertical-align' => 'middle']);
        $col[] = $this->getComponentDivider();
        return $this->getComponentColumn($col, ['id' => 'user_' . $id], ['margin' => '0 0 10 0']);

    }

    private function UKIULGetBottom($parameters, $content, $id, $dounlike)
    {

        $likeaction = isset($parameters['likeaction']) ? $parameters['likeaction'] : 'infinite/like/' . $id;
        $unlike_icon = isset($parameters['icon_unlike']) ? $parameters['icon_unlike'] : false;

        $dolike[] = $this->getOnclickHideElement('user_' . $id);
        $dolike[] = $this->getOnclickSubmit($likeaction);

        $bookmark[] = $this->getOnclickHideElement('bookmark_not_active' . $id, ['transition' => 'none']);
        $bookmark[] = $this->getOnclickShowElement('bookmark_active' . $id, ['transition' => 'none']);
        $bookmark[] = $this->getOnclickSubmit('controller/bookmark/' . $id, ['loader_off' => true]);

        $un_bookmark[] = $this->getOnclickShowElement('bookmark_not_active' . $id, ['transition' => 'none']);
        $un_bookmark[] = $this->getOnclickHideElement('bookmark_active' . $id, ['transition' => 'none']);
        $un_bookmark[] = $this->getOnclickSubmit('controller/removebookmark/' . $id, ['loader_off' => true]);


        $icon_bookmark = isset($parameters['icon_bookmark']) ? $parameters['icon_bookmark'] : 'uikit_icon_feed_bookmark.png';
        $icon_bookmark_active = isset($parameters['icon_bookmark_active']) ? $parameters['icon_bookmark_active'] : 'uikit_icon_feed_bookmark_active.png';
        $icon_like = isset($parameters['icon_like']) ? $parameters['icon_like'] : 'uikit_icon_feed_heart.png';

        $width = $this->screen_width - 155;

        if ($unlike_icon) {
            $controls[] = $this->getComponentImage($unlike_icon, [
                'onclick' => $dounlike
            ], [
                'width' => '35', 'margin' => '10 0 10 15'
            ]);
        } else {
        }

        $controls[] = $this->getComponentImage($icon_like, [
            'onclick' => $dolike
        ], [
            'width' => '35', 'margin' => '10 15 10 15'
        ]);


        if (isset($content['instagram_username']) AND $content['instagram_username']) {

            if (isset($parameters['instaclick_command'])) {
                $onclick_insta[] = $this->getOnclickSubmit($parameters['instaclick_command'] . $id);
            }

            $onclick_insta[] = $this->getOnclickOpenUrl('https://instagram.com/' . $content['instagram_username']);

            $controls[] = $this->getComponentRow([
                $this->getComponentText('{#follow#}', ['style' => 'uikit_list_follow_button',
                    'onclick' => $onclick_insta])
            ], [], ['width' => $width, 'text-align' => 'center']);
        } else {
            $controls[] = $this->getComponentRow([
                $this->getComponentText('', ['style' => 'uikit_list_follow_placeholder'])],
                [], ['width' => $width, 'text-align' => 'center']);
        }


        if (isset($content['bookmark']) AND $content['bookmark']) {
            $controls[] = $this->getComponentImage($icon_bookmark, [
                'visibility' => 'hidden', 'id' => 'bookmark_not_active' . $id, 'onclick' => $bookmark
            ], [
                'width' => '35', 'margin' => '10 10 10 15', 'floating' => '1', 'float' => 'right'
            ]);

            $controls[] = $this->getComponentImage($icon_bookmark_active, [
                'id' => 'bookmark_active' . $id, 'onclick' => $un_bookmark
            ], [
                'width' => '35', 'margin' => '10 10 10 15', 'floating' => '1', 'float' => 'right'
            ]);
        } else {
            $controls[] = $this->getComponentImage($icon_bookmark, [
                'id' => 'bookmark_not_active' . $id, 'onclick' => $bookmark
            ], [
                'width' => '35', 'margin' => '10 10 10 15', 'floating' => '1', 'float' => 'right'
            ]);

            $controls[] = $this->getComponentImage($icon_bookmark_active, [
                'visibility' => 'hidden', 'id' => 'bookmark_active' . $id, 'onclick' => $un_bookmark
            ], [
                'width' => '35', 'margin' => '10 10 10 15', 'floating' => '1', 'float' => 'right'
            ]);
        }

        return $controls;
    }

}