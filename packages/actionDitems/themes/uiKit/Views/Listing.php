<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\Views\Listing as BootstrapView;

class Listing extends BootstrapView
{
    public function tab1()
    {
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');
        $category_id = $this->getData('category_id', 'string');

        $this->model->flushActionRoutes($this->model->getActionidByPermaname('addvisit'));

        $this->model->deleteVariable('visit_pic_1');
        $this->model->deleteVariable('visit_pic_2');
        $this->model->deleteVariable('visit_pic_3');

        $this->model->setBackgroundColor('#fafafa');
        $this->model->rewriteactionfield('subject', strtoupper('visits'));

        // Initial padding
        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $this->renderAddButton();

        if (empty($items) || is_null($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_visits_yet#}', array('style' => 'mit_no_items'));
        }

        $this->layout->scroll[] = $this->getComponentSpacer(50);

        $this->layout->scroll[] = $this->uiKitList($items);

        $layout = new \stdClass();
        $layout->bottom = 10;
        $layout->right = 10;
        $layout->width = 60;
        $layout->height = 60;

        $onclick = $this->getOnclickOpenAction('materialscategorylisting', false,
            array(
                'id' => $category_id,
                'sync_open' => 1,
                'sync_close' => 1,
                'back_button' => 1
            ));

        $this->layout->overlay[] = $this->getComponentImage('icon-learning-circle.png', array(
            'layout' => $layout,
            'onclick' => $onclick,
        ));

        return $this->layout;
    }

    /**
     * Renders the button that opens the action to create
     * new item. Action is opened in a new view.
     *
     * @return void
     */
    protected function renderAddButton()
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(strtoupper('{#add_visit#}'), array(
                'style' => 'add_item_button',
                'onclick' => $this->getOnclickOpenAction('addvisit', false, [
                    'id' => 'new',
                    'back_button' => 1,
                    'sync_open' => 1,
                    'sync_close' => 1,
                    'disable_cache' => 1,
                ])
            ))
        ), array(), array(
            'text-align' => 'center'
        ));
    }

}