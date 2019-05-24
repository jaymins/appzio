<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusMainMenuButton {


    public function getNexudusMainMenuButton($parameters=array()){
        /** @var BootstrapComponent $this */


        $icon = $this->addParam('icon',$parameters,'icon-nexudus-plus.png');
        $title = $this->addParam('title',$parameters,'{#make_a_booking#}');
        $onclick = $this->addParam('onclick',$parameters,$this->getOnclickGoHome());

        $width = ($this->screen_width / 3) - 30;

        $col[] = $this->getComponentImage($icon,[],['width' => '50']);
        
        $output[] = $this->getComponentColumn($col, [], [
            'background-color' => $this->color_top_bar_color,
            'height' => '80',
            'text-align' => 'center',
            'vertical-align' => 'middle',
            'width' => '80',
            'border-radius' => '40'
        ]);

        $output[] = $this->getcomponentText($title,[],[
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'center',
            'font-size' => '12',
            'margin' => '5 0 0 0'
        ]);

        return $this->getComponentColumn($output,['onclick' => $onclick],['text-align' => 'center',
            'margin' => '15 15 15 15','width' =>$width
        ]);




    }

}
