<?php

namespace packages\actionMshopping\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMshopping\Components\Components as Components;
use packages\actionMshopping\Models\ItemModel;
use packages\actionMshopping\Models\Model as ArticleModel;

class Listing extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    /**
     * @var ArticleModel
     */
    public $model;

    /**
     * Main view entrypoint
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');

        $this->model->setBackgroundColor('#1e1e1e');

        if (empty($items) || is_null($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_items_yet#}', array('style' => 'mit_no_items'));
        }

        // Initial padding
        $this->layout->scroll[] = $this->getComponentSpacer(10);

        foreach ($items as $item) {
            $this->renderSingleItem($item);
        }

        $this->renderAddButton();

        return $this->layout;
    }

    /**
     * Render single item
     *
     * @param ItemModel $item
     */
    protected function renderSingleItem(ItemModel $item)
    {
        $artist = \AeplayVariable::getArrayOfPlayvariables($item->play_id);

        if ( !isset($item->images) OR empty($item->images) ) {
        	return false;
        }

        $images = json_decode($item->images);

        if ( !isset($images->itempic) OR empty($images->itempic) ) {
        	return false;
        }

        $this->layout->scroll[] = $this->components->getItemCard(array(
            'item' => $item,
            'artist' => $artist,
            'image' => $images->itempic
        ));
    }

    /**
     * Renders the button that opens the action to create
     * new item. Action is opened in a new view.
     *
     * @return void
     */
    protected function renderAddButton()
    {
        $onclick = new \stdClass();
        $onclick->id = null;
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('create');
        $onclick->sync_close = 1;
        $onclick->sync_open = 1;

        $this->layout->footer[] = $this->getComponentText(strtoupper('{#add_item#}'), array(
            'style' => 'add_item_button',
            'onclick' => $onclick
        ));
    }
}