<?php

namespace packages\actionMMarketplace\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMMarketplace\Models\Model as ArticleModel;
use packages\actionMMarketplace\Components\Components;

class UserListing extends BootstrapView
{

    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $bidItems = $this->getData('bidItems', 'array');
        $this->layout->header[] = $this->setHeader(1);

        if ( empty($bidItems) ) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#you_don\'t_have_any_active_bids_at_the_moment#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        } else {
            $this->buildItems($bidItems);
        }
        
        $this->addButtons();
        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $bidItems = $this->getData('bidItems', 'array');
        $this->layout->header[] = $this->setHeader(2);

        if ( empty($bidItems) ) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#you_don\'t_have_any_completed_bids#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        } else {
            $this->buildItems($bidItems);
        }

        $this->addButtons();
        return $this->layout;
    }

    public function buildItems($items)
    {
        
        $chunks = array_chunk($items, 2);

        foreach ($chunks as $items_row) {

            $container = [];

            foreach ($items_row as $item) {

                $bidsCount = count($item->bids);
                $bidCountInfo = $bidsCount . ' bids';

                if($bidsCount == 1){
                    $bidCountInfo = $bidsCount . ' bid';
                }
                $parameters['info'] = $bidCountInfo;
                $parameters['image'] = isset($item->images) ? $item->images[0]->image : null;
                $parameters['action'] = 'viewbid';

                $container[] = $this->components->uiKitItemCardHalfRow($item, $parameters);
            }

            $this->layout->scroll[] = $this->getComponentRow($container, array(), array(
                'width' => '100%',
                'text-align' => ( count($container) > 1 ? 'center' : 'left' ),
                'margin' => '15 5 0 5',
            ));

        }

    }

    public function addButtons()
    {
        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentText('{#create_bid#}', array(
                'onclick' => $this->getOnclickOpenAction('createbid', false, [
                    'sync_open' => 1
                ]),
                'style' => 'tattoo_marketplace_submit_button'
            ))
        ), array(
            'style' => 'tattoo_marketplace_submit_button_wrapper'
        ));
    }

    protected function setHeader($activeTab)
    {
        $styles = array(
            "font-size" => "10",
            "padding" => "10 0 10 0",
            "color" => '#9c9b9b',
            "border-color" => '#dedede',
            "width" => "50%",
            "text-align" => 'center'
        );
        return $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#active_bids#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $activeTab === 1 ?? false,
            ),
            array(
                'text' => strtoupper('{#completed#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $activeTab === 2 ?? false
            )
        ), array(), $styles);
    }

}