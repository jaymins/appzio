<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\themes\uiKit\Views\Create as BootstrapView;

class Download extends BootstrapView
{

    public function tab1()
    {
        $this->layout = new \stdClass();

        $file = $this->getData('file', 'string');
        
        $this->layout->onload[] = $this->getOnclickOpenUrl( $file );

        return $this->layout;
    }

}