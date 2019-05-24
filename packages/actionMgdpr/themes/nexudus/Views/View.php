<?php

namespace packages\actionMgdpr\themes\nexudus\Views;
use packages\actionMgdpr\Views\View as BootstrapView;
use packages\actionMgdpr\themes\nexudus\Components\Components;

class View extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;


    public function setHeader($tab=1)
    {
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#terms_and_conditions#}');
    }


}
