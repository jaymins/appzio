<?php

namespace packages\actionMswipematch\themes\uikit\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getUserListing {

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

    public function getUserListing($content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        if(!is_array($content) OR empty($content)){
            return $this->getComponentText('{#no_users_found_at_the_moment#}',array('style' => 'steps_error2'));
        }

        $matches = $this->model->obj_datastorage->get('matches');
        $rows = [];
        $count = 1;
        foreach($content as $item)
        {

            $userData = $this->model->foreignVariablesGet($item['play_id']);
            if (!isset($userData['real_name'])) {
                $userData['real_name'] = ucfirst($userData['firstname']) . ' ' . ucfirst($userData['lastname']);
            }

            $columns = array();
            if (isset($userData['profilepic'])) {
                $columns[] = $this->getComponentImage($userData['profilepic'], array('style' => 'user_list_icon'));
            } else {
                $columns[] = $this->getComponentImage('icon_camera-grey.png', array('style' => 'user_list_icon'));
            }

            $personal = array();

            if (in_array($item['play_id'], $matches)) {
                $personal[] = $this->getComponentRow(array(
                    $this->getComponentImage('icon-yes-v2.png', array(), array('width' => 14, 'margin' => '0 5 0 0')),
                    $this->getComponentText($userData['real_name'], array('style' => 'user_list_info_name'))
                ));
            } else {
                $personal[] = $this->getComponentRow(array(
                    $this->getComponentText(' ', array(), array('width' => 14, 'margin' => '0 5 0 0')),
                    $this->getComponentText($userData['real_name'], array('style' => 'user_list_info_name'))
                ));
            }

            $age = ( isset($userData['age']) ? $userData['age'] . ' yrs' : 'N/A yrs' );
            $weight = ( isset($userData['weight']) ? $userData['weight'] . ' kg' : 'N/A kg' );
            $height = ( isset($userData['height']) ? $userData['height'] . ' cm' : 'N/A cm' );

            $details = array(
                $this->getComponentText($age, array(), array(
                    'color' => '#a0a0a0',
                    'font-size' => '15',
                    'margin' => '0 0 0 17',
                )),
                $this->getComponentImage('dot.png', array(), array(
                    'width' => 3,
                    'margin' => '0 5 0 5',
                )),
                $this->getComponentText($weight, array(), array(
                    'color' => '#a0a0a0',
                    'font-size' => '15',
                )),
                $this->getComponentImage('dot.png', array(), array(
                    'width' => 3,
                    'margin' => '0 5 0 5',
                )),
                $this->getComponentText($height, array(), array(
                    'color' => '#a0a0a0',
                    'font-size' => '15',
                ))
            );

            $personal[] = $this->getComponentRow($details);
            $columns[] = $this->getComponentColumn($personal);

            $columns[] = $this->getComponentImage('arrow.png', array('style' => 'user_list_info_arrow'));

            $action = $this->getOnclickOpenAction(
                'userinfo',
                false,
                array('sync_open' => 1, 'back_button' => 1, 'id' => 'trainer-' . $item['play_id'])
            );

            $rows[] = $this->getComponentRow($columns, array('onclick' => $action), array(
            	'vertical-align' => 'middle'
            ));

            if ($count != count($content)) {
                $rows[] = $this->getComponentText('', array('style' => 'user_list_divider'));
            }
            $count++;
        }

        if ($rows) {
            return $this->getComponentColumn($rows, array('background-color' => '#000000'));
        }

        return $this->getComponentText('{#no_users_found_at_the_moment#}', array('style' => 'jm_notification_text'));
    }

}
