<?php

namespace packages\actionMitems\themes\venues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\venues\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

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