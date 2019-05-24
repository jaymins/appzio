<?php

namespace packages\actionMitems\themes\cityapp\Controllers;

use Bootstrap\Controllers\BootstrapController;

class Intro extends BootstrapController
{

    public function actionDefault()
    {

        $data['intro'] = [
            'cityapp-intro-1.png',
            'cityapp-intro-2.png',
            'cityapp-intro-3.png',
        ];

        return ['Intro', $data];
    }

}