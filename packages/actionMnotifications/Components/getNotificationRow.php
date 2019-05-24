<?php

namespace packages\actionMnotifications\Components;
use Bootstrap\Components\BootstrapComponent;
use function strstr;

trait getNotificationRow {

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

    public function getNotificationRow($content) {
        /** @var BootstrapComponent $this */

        //print_r($content);
        if(!isset($content['profilepic'])){
            $content['profilepic'] = 'mreg-persona-icon.png';
        }

        $width = $this->screen_width - 130;

        $col[] = $this->getComponentImage($content['profilepic'],array(
            'imgwidth' => '400',
            'imgheight' => '400',
            'format' => 'jpg',
            'imgcrop' => 'yes',
            'style' => 'notification_image'));
        $row[] = $this->getComponentText($content['subject'],array('style' => 'notification_title'));
        $row[] = $this->getComponentText(date('m / d / Y g:ia',$content['created_date']),array('style' => 'notification_date'));

        $col[] = $this->getComponentColumn($row,array(),array('width' => $width));
        $col[] = $this->getComponentImage($content['icon'],array('style' => 'notification_icon'));

/*      $delete[] = $this->getOnclickSubmit('delete_'.$content['id'], array('sync_open' => 1));
        $page[] = $this->getComponentRow(array(
            $this->getComponentImage(
                'div-close-icon.png',
                array("onclick" => $delete),
                array("width" => '20'))
        ), array(), array('text-align' => "right", "margin" => "5 5 -20 0"));*/

        if($content['status'] == 'read') {
            $page[] = $this->getComponentRow($col, array('style' => 'notification_row_read'));
            $opacity = 0.5;
        } else {
            $page[] = $this->getComponentRow($col, array('style' => 'notification_row'));
            $clicker[] = $this->getOnclickSubmit('mark_read_'.$content['id']);
            $opacity = 1;
        }

        $page[] = $this->getComponentText('',array('style' => 'notification_divider'));

        if($content['action_id']){
            $params['sync_open'] = 1;
            $params['id'] = $content['action_param'];
            $clicker[] = $this->getOnclickOpenAction(false,$content['action_id'],$params);
        }

        if(!isset($clicker)){
            $clicker = $this->getOnclickSubmit('refresh');
        }


        return $this->getComponentColumn($page,array(
            'onclick' => $clicker,
            'opacity' => $opacity,
            'id' => 'row_'.$content['id'],
            'swipe_right' => [$this->uiKitSwipeDeleteButton(['identifier' => $content['id']])]));



	}

}
