<?php

namespace packages\actionMlogin\themes\demo\Views;

use Bootstrap\Views\BootstrapView;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Redirect extends BootstrapView {

    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1(){
        // Note: the redirect is controlled through the admin area
        $this->layout->scroll[] = $this->getComponentFullPageLoader();
        $this->layout->onload[] = $this->getOnclickCompleteAction();
        return $this->layout;

    }


}
