<?php

namespace packages\actionMitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\fans\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class About extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');
        
        $pic = $this->model->getConfigParam('image_portrait');
        $subject = $this->model->getConfigParam('subject');

        if($pic){
            $this->layout->scroll[] = $this->getComponentImage($pic,['imgwidth' => '900']);
        }

        $this->layout->scroll[] = $this->getComponentSpacer(20);
        $this->layout->scroll[] = $this->uiKitInfoTileTitle($subject);

        $content = $this->getData('text', 'string');
        $this->layout->scroll[] = $this->getComponentText($content);
        return $this->layout;
    }


}