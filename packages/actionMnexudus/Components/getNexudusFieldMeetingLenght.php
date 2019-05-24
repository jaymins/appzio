<?php

namespace packages\actionMnexudus\Components;
use Bootstrap\Components\BootstrapComponent;
use function str_replace;
use function str_replace_array;
use function strtolower;
use function substr;

trait getNexudusFieldMeetingLenght {


    public function getNexudusFieldMeetingLenght(){
        /** @var BootstrapComponent $this */
        return $this->getComponentText('nexudus field meeting length');
    }

}
