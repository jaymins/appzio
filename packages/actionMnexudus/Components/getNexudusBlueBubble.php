<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusBlueBubble {


    public function getNexudusBlueBubble(\stdClass $content){
        /** @var BootstrapComponent $this */

        $col[] = $this->getComponentImage('icon-nexudus-info.png',[],[
            'width' => '30','margin' => '5 20 30 0']);
        $col[] = $content;

        return $this->getComponentRow($col,[],[
            'background-color' => '#B32930DD',
            'padding' => '15 15 0 15',
            'vertical-align' => 'top',
            'border-radius' => '10',
            //'height' => '100',
            'margin' => '15 15 15 15'
        ]);
    }

}
