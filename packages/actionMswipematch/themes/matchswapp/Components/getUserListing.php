<?php

namespace packages\actionMswipematch\themes\matchswapp\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getUserListing {

    public $page = 0;

    /**
     * @param $content array
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getUserListing(array $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $rows = array();

        foreach($content as $user){
            $rows[] = $this->getUserRowListing($user);
        }

        if ($rows) {
            return $this->getComponentColumn($rows, array('background-color' => '#000000'));
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}', array('style' => 'jm_notification_text'));
    }

    public function getUserRowListing($user){
        $col[] = $this->getComponentImage($user['profilepic'],['style' => '']);
        $col[] = $this->getComponentDivider();

        return $this->getComponentColumn($col);

    }

}
