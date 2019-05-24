<?php

namespace packages\actionMswipematch\themes\matchswapp\Components;
use Bootstrap\Components\BootstrapComponent;
use function is_array;

trait getCheckinFloatingButton {

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

    public function getCheckinFloatingButton($mode=false) {
        /** @var BootstrapComponent $this */

        $onclick_checkin = $this->getOnclickOpenAction('checkin',false,['transition' => 'fade']);

        $layout = new \stdClass();
        $layout->bottom = 15;
        $layout->left = 15;
        $layout->width = 80;
        $layout->height = 40;

        $params_checkin['onclick'] = $this->getOnclickOpenAction('checkin',false,['transition' => 'fade']);
        $params_messaging['onclick'] = $this->getOnclickOpenAction('messaging',false,['transition' => 'fade']);

        $style['padding'] = '7 7 7 7';

        if($mode == 'checkin'){
            $row[] = $this->getComponentImage('ms_contact_white.png',$params_messaging,$style);
            $row[] = $this->getComponentImage('ms_marker_green.png',[],$style);
            $col[] = $this->getComponentRow($row);
        } elseif($mode == 'messaging') {
            $row[] = $this->getComponentImage('ms_contact_green.png',[],$style);
            $row[] = $this->getComponentImage('ms_marker_white.png',$params_checkin,$style);
            $col[] = $this->getComponentRow($row);
        } else {
            $row[] = $this->getComponentImage('ms_contact_white.png',$params_messaging,$style);
            $row[] = $this->getComponentImage('ms_marker_white.png',$params_checkin,$style);
            $col[] = $this->getComponentRow($row);
        }

        return $this->getComponentColumn($col, ['layout' => $layout,'onclick' => $onclick_checkin],[
            'background-color' => '#000000','text-align' => 'center',
            'border-radius' => '6','vertical-align' => 'middle']);
    }


}
