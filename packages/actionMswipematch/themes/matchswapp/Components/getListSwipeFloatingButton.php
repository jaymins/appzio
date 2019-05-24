<?php

namespace packages\actionMswipematch\themes\matchswapp\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getListSwipeFloatingButton {

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

    public function getListSwipeFloatingButton($mode='swipe') {
        /** @var BootstrapComponent $this */

        $layout = new \stdClass();
        $layout->bottom = 15;
        $layout->right = 15;
        $layout->width = 80;
        $layout->height = 40;

        $params_list['onclick'] = $this->getOnclickOpenAction('people',false,['transition' => 'fade']);
        $params_swipe['onclick'] = $this->getOnclickOpenAction('swipe',false,['transition' => 'fade']);

        $style['padding'] = '9 9 9 9';

        if($mode == 'swipe'){
            $row[] = $this->getComponentImage('ms_icon_swipe_green.png',[],$style);
            $row[] = $this->getComponentImage('ms_icon_list_white.png',$params_list,$style);
            $col[] = $this->getComponentRow($row);

        } elseif($mode == 'list') {
            $row[] = $this->getComponentImage('ms_icon_swipe_white.png',$params_swipe,$style);
            $row[] = $this->getComponentImage('ms_icon_list_green.png',[],$style);
            $col[] = $this->getComponentRow($row);
        } else {
            $row[] = $this->getComponentImage('ms_icon_swipe_white.png',$params_swipe,$style);
            $row[] = $this->getComponentImage('ms_icon_list_white.png',$params_list,$style);
            $col[] = $this->getComponentRow($row);
        }

        return $this->getComponentColumn($col, ['layout' => $layout],[
            'background-color' => '#000000','text-align' => 'center',
            'border-radius' => '6','vertical-align' => 'middle']);
    }


}
