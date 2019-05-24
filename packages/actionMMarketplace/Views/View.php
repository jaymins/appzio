<?php

namespace packages\actionMMarketplace\Views;

use Bootstrap\Views\BootstrapView;

class View extends BootstrapView
{

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('Marketplace main view');

        return $this->layout;
    }

}