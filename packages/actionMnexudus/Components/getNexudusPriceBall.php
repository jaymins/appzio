<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusPriceBall {


    public function getNexudusPriceBall($time,$price){
        /** @var BootstrapComponent $this */


        $width = ($this->screen_width / 2) - 30;

        $col[] = $this->getComponentText($time,[],[
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'center',
            'font-size' => '22',
            'font-weight' => 'bold'
        ]);
        
        $output[] = $this->getComponentColumn($col, [], [
            'background-color' => $this->color_top_bar_color,
            'height' => '80',
            'text-align' => 'center',
            'vertical-align' => 'middle',
            'width' => '80',
            'border-radius' => '40'
        ]);

        unset($col);

        $col[] = $this->getComponentText($price,[],[
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'center',
            'font-size' => '22',
            'font-weight' => 'bold'
            ]);

        $output[] = $this->getComponentColumn($col, [], [
            'background-color' => '#B3ffffff',
            'height' => '80',
            'margin' => '0 0 0 -12',
            'text-align' => 'center',
            'vertical-align' => 'middle',
            'width' => '80',
            'border-radius' => '40'
        ]);

        return $this->getComponentRow($output,[],['text-align' => 'center',
            'margin' => '15 0 15 0','width' =>$width
        ]);




    }

}
