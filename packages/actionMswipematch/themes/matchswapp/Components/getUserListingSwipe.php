<?php

namespace packages\actionMswipematch\themes\matchswapp\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getUserListingSwipe {

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

    public function getUserListingSwipe($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));
        }

        $rows = [];
        foreach($content as $userData)
        {

            $columns = array();
            if (isset($userData['profilepic'])) {
                $columns[] = $this->getComponentImage($userData['profilepic'], array('style' => 'user_list_icon'));
            } else {
                $columns[] = $this->getComponentImage('icon_camera-grey.png', array('style' => 'user_list_icon'));
            }

            $personal = array();
            //$personal[] = $this->getComponentText($userData['username'], array('style' => 'user_list_info_name'));
            //$personal[] = $this->getComponentText($userData['job_title'], array('style' => 'user_list_info_job_title'));
            //$personal[] = $this->getComponentText($userData['description'], array('style' => 'user_list_info_description'));

            if ( !empty($userData['location']) ) {
                $personal[] = $this->getComponentRow(array(
                    $this->getComponentImage('social-location-pin.png', array(), array('width' => '16', 'height' => '16')),
                    $this->getComponentText($userData['location']->places->name, array(), array('height' => '20', 'font-size' => '16', 'margin' => '5 5 5 5'))
                ), array(), array('text-align' => 'right', 'vertical-align' => 'middle'));
            }
            $columns[] = $this->getComponentColumn($personal);


            $action = $this->getOnclickOpenAction(
                'profile',
                false,
                array('sync_open' => 1, 'back_button' => 1),
                'Profile/default/'.$userData['play_id']
            );

            $rows[] =$this->getComponentRow(array(
                   $this->getComponentColumn($columns, array('onclick' => $action, 'style' => 'user_list_container'))
                ), array(), array('text-align' => 'center')
            );
        }

        if ($rows) {
            return $this->getComponentSwipe($rows);
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}', array('style' => 'jm_notification_text'));
    }

}
