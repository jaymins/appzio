<?php

namespace packages\actionMitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Imagepreview extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \StdClass;

        $image = $this->getData('image', 'string');

        $this->layout->header[] = $this->components->uiKitVisitTopbar('arrow-back-white-v2.png', '{#image_preview#}', $this->getOnclickGoHome(), array(
            'background-color' => '#000000'
        ));

        $this->layout->scroll[] = $this->getComponentImage($image, [
            'imgwidth' => 2000,
            'debug' => 1,
            'scrollable' => 1,
            'priority' => 9,
        ], [
            'width' => '100%',
            'height' => round($this->screen_height - 70,0),
        ]);

        return $this->layout;
    }

}