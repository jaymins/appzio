<?php

namespace packages\actionDitems\themes\visits\Views;

use packages\actionDitems\Views\Listing as ListingView;
use packages\actionDitems\themes\visits\Components\Components as Components;
use packages\actionDitems\themes\visits\Models\Model as BootstrapModel;

class Listing extends ListingView
{
    /**
     * @var Components
     */
    public $components;

    /**
     * @var BootstrapModel
     */
    public $model;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');
        $bookmarks = $this->getData('bookmarks', 'array');
        $this->model->setBackgroundColor('#1e1e1e');

        /* add button */
        $this->layout->scroll[] = $this->components->uiKitButtonWithIcon(
            'dials.png',
            '{#add_new#}',
            $this->getOnclickOpenAction('addvisit'));

        $this->layout->scroll[] = $this->components->uiKitItemListingInfinitePlain(
            $items,
            true,
            ''
            );




        return $this->layout;
    }



}