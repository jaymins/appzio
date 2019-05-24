<?php

namespace packages\actionMitems\themes\clubs\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\clubs\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Dashboard extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $table = $this->getData('table', 'array');
        $header = $this->getData('header', 'string');
        $subject = $this->getData('subject', 'string');

        $this->layout->scroll[] = $this->uiKitCollabpsableTextHeader($subject, $header);

        if(is_array($table) AND !empty($table)){
            $this->layout->scroll[] = $this->uiKitTableData($table);
        }

        return $this->layout;
    }


}