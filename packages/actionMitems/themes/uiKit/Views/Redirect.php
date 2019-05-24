<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\themes\uiKit\Views\Create as BootstrapView;

class Redirect extends BootstrapView
{

    public function tab1()
    {
        $this->layout = new \stdClass();

        $action = $this->getData('action', 'string');
        $item_id = $this->getData('item_id', 'string');
        $tab_to_open = $this->getData('tab_to_open', 'string');

        $this->layout->onload[] = $this->getOnclickOpenAction($action, false, [
            'id' => $item_id,
            'tab_id' => $tab_to_open,
            'sync_open' => 1,
        ]);

        return $this->layout;
    }

}