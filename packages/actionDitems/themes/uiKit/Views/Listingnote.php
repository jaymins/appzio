<?php

namespace packages\actionDitems\themes\uiKit\Views;

use packages\actionDitems\Views\Listing as BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionDitems\Models\ItemModel;

class Listingnote extends BootstrapView
{
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->flushActionRoutes($this->model->getActionidByPermaname('addnote'));

        $items = $this->getData('items', 'array');

        $this->model->setBackgroundColor('#fafafa');
        $this->model->rewriteactionfield('subject', strtoupper('notes'));

        // Initial padding
        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $this->renderAddButton();

        if (empty($items) || is_null($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_notes_yet#}', array('style' => 'mit_no_items'));
        }

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        foreach ($items as $item) {
            $this->renderSingleItem($item);
        }

        return $this->layout;
    }

    /**
     * Render single item
     *
     * @param ItemModel $item
     * @return void
     */
    protected function renderSingleItem(ItemModel $item)
    {
        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('viewnote');
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;
        $onclick->back_button = 1;
        $onclick->id = $item->id;

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText($item->name, array(), array(
                'font-size' => 16,
                'font-weight' => 'bold',
                'margin' => '0 0 10 0',
                'width' => '70%'
            )),
            $this->getComponentColumn(array(
                $this->getComponentText(date('M d, Y', $item->date_added), array(), array(
                    'font-size' => '11',
                    'color' => '#9f9f9f',
                )),
                $this->getComponentText(count($item->reminders) . ' to-dos', array(), array(
                    'font-size' => '11',
                    'color' => '#9f9f9f',
                ))
            ), array(), array(
                'text-align' => 'right',
                'width' => '30%'
            ))
        ), array(
            'id' => 'row_' . $item['id'],
            'onclick' => $onclick,
            'swipe_right' => array(
                $this->uiKitSwipeDeleteButton(array(
                    'identifier' => $item['id']
                ))
            )
        ), array(
            'padding' => '20 15 20 15',
            'height' => 100,
            'width' => '100%',
            'vertical-align' => 'top'
        ));

        $this->layout->scroll[] = $this->uiKitDivider();
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
            $this->getComponentText(strtoupper('{#add_note#}'), array(
                'style' => 'add_item_button',
                'onclick' => $this->getOnclickOpenAction('addnote', false, [
                    'id' => 'new',
                    'sync_open' => 1,
                    'sync_close' => 1,
                    'back_button' => 1,
                    'disable_cache' => 1,
                ]),
            ))
        ), array(), array(
            'text-align' => 'center'
        ));
    }

}
