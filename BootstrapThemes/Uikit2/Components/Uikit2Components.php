<?php

namespace BootstrapThemes\Uikit2\Components;

use Bootstrap\Components\BootstrapComponent;

class Uikit2Components extends BootstrapComponent {

    use themeButton;

    /* you need to have this constructor so that styles & images get loaded properly */
    public function __construct($obj)
    {
        parent::__construct($obj);
        $searchpath = \Yii::getPathOfAlias('application.modules.aelogic.BootstrapThemes.Uikit2.Images');
        $this->imagesobj->imagesearchpath[] = $searchpath .'/';
        $this->registerTheme('Uikit2');
    }


}