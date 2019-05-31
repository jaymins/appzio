<?php

namespace packages\actionDitems\themes\clubs\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\clubs\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Downloads extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $downloads = $this->getData('downloads', 'array');
        $header = $this->getData('header', 'mixed');
        $subject = $this->getData('subject', 'string');

        if($header AND $subject){
            $this->layout->scroll[] = $this->uiKitCollabpsableTextHeader($subject, $header);
        }

        if($downloads AND !empty($downloads)){
            $this->layout->scroll[] = $this->uiKitDownloads($downloads);
        }


        return $this->layout;
    }


}