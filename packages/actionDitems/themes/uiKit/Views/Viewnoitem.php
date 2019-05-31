<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\themes\uiKit\Views\Create as BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class Viewnoitem extends BootstrapView
{
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $this->layout->scroll[] = $this->getComponentText('#missing_item#', array(), array(
	        'text-align' => 'center',
	        'padding' => '10 0 10 0',
        ));

        return $this->layout;
    }

}