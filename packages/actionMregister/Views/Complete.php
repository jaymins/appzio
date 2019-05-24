<?php

namespace packages\actionMregister\Views;

use packages\actionMregister\themes\stepbystep\Components\Components;
use packages\actionMregister\Views\View as BootstrapView;

class Complete extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $text1 = $this->getData('text1', 'string');
        $text2 = $this->getData('text2', 'string');

        if ($text1) {
            $this->layout->scroll[] = $this->components->getComponentText($text1, array('style' => 'steps_bigtitle'));
        }

        if ($text2) {
            $this->layout->scroll[] = $this->components->getComponentText($text2, array('style' => 'steps_smalltitle'));
        }

         $col[] = $this->getComponentFullPageLoaderAnimated();
         $this->layout->scroll[] = $this->getComponentColumn($col,[],['width' => '100%','text-align' => 'center']);

        $this->layout->onload[] = $this->getOnclickLocation();
        $this->layout->onload[] = $this->getOnclickCompleteAction();

        if ($this->model->getConfigParam('intro_action')) {
            $this->layout->onload[] = $this->getOnclickOpenAction(false, $this->model->getConfigParam('intro_action'), ['sync_open' => 1]);
        }

        return $this->layout;
    }

}