<?php

namespace packages\actionMlogin\themes\demo\Views;

use Bootstrap\Views\BootstrapView;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class RedirectBranch extends BootstrapView
{

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getComponentFullPageLoader();
        $redirect = $this->getData('branch', 'string');
        $this->layout->onload[] = $this->getOnclickOpenBranch($redirect);
        return $this->layout;
    }

}