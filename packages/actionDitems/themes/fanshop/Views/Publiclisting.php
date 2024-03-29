<?php

namespace packages\actionDitems\themes\fanshop\Views;

use packages\actionDitems\Views\Listing as ListingView;
use packages\actionDitems\themes\fanshop\Components\Components as Components;
use packages\actionDitems\themes\fanshop\Models\Model as BootstrapModel;

class Publiclisting extends ListingView
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

        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#home#}'),
                'onclick' => $this->getOnclickOpenAction('fanhome',false,['transition' => 'none']),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#matches#}'),
                'onclick' => $this->getOnclickOpenAction('matches', false, array(
                    'transition' => 'none'
                )),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#fan_shop#}'),
                'onclick' => $this->getOnclickOpenAction('fanshop', false, array(
                    'transition' => 'none'
                )),
                'active' => 1
            )
        ));

        $items = $this->getData('items', 'array');
        $bookmarks = $this->getData('bookmarks', 'array');
        $this->model->setBackgroundColor('#1e1e1e');
        
        $params['onclick_close'] = $this->getOnclickSetVariables(array('searchterm' => ''));
        $this->layout->header[] = $this->uiKitSearchField($params);
        $this->layout->scroll[] = $this->getComponentLoader(array('color' => '#000000','visibility' => 'onloading'));

        if (empty($items) || is_null($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_items#}', array('style' => 'fanshop_fanshop_mit_no_items'));
        }

        $params['route_add'] = 'Publiclisting/savefavorite/';
        $params['route_del'] = 'Publiclisting/delfavorite/';
        $params['bookmarks'] = $bookmarks;

        /* this is little special component, as it returns item that should be put
        directly to scroll, as scroll is most efficient when rendering large lists */
        $this->layout->scroll[] = $this->components->uiKitItemListingInfinite($items,$params);

        if($this->model->getSavedVariable('system_source') == 'client_iphone') {
            $this->layout->overlay[] = $this->components->getAddButton();
        } else {
            $this->layout->footer[] = $this->components->getAddButton();
        }

        return $this->layout;
    }



    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs(){
        $divs = new \stdClass();

        $onclick = new \stdClass();
        $onclick->id = null;
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('create');
        $onclick->sync_close = 1;
        $onclick->sync_open = 1;

        $btn = $this->getComponentText(strtoupper('+'), array(
            'style' => 'fanshop_fanshop_add_item_button',
            'onclick' => $onclick,
            'bottom' => '10',
            'text-align' => 'center'
        ));

        /* look for traits under the components */
        $divs->createbtn = $btn;
        return $divs;
    }
}