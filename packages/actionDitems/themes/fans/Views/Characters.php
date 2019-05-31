<?php

namespace packages\actionDitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\fans\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Characters extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $data = $this->getData('characters', 'array');
        $header = $this->getData('header', 'string');
        $subject = $this->getData('subject', 'string');

        $this->layout->scroll[] = $this->uiKitCollabpsableTextHeader($subject, $header);

        if(is_array($data) AND !empty($data)){
            $this->layout->scroll[] = $this->uiKitTextAccordion($data);
        }

        return $this->layout;
    }


}