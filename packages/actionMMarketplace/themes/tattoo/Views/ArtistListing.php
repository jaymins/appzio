<?php

namespace packages\actionMMarketplace\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMMarketplace\Models\UserBidModel;
use packages\actionMMarketplace\Models\Model as ArticleModel;
use packages\actionMMarketplace\Components\Components;

class ArtistListing extends BootstrapView
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
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#there_aren\'t_any_active_bids_at_the_moment#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        } else {
            $this->buildItems($bidItems);
        }

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $bidItems = $this->getData('bids', 'array');
        $user = $this->getData('user', 'array');

        $this->layout->header[] = $this->setHeader(2);

        if ( empty($bidItems) ) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#you_have_no_active_bids#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        } else {

            foreach ($bidItems as $item){
                $content = array(
                    "image" => isset($item->bidItem->images) ? $item->bidItem->images[0]->image : 'formkit-photo-placeholder.png',
                    "details" => array(
                        array(
                            "type" => "title",
                            "text" => $item->bidItem->title
                        ),
                        array (
                            "type" => "description",
                            "text" => "{#bid_submited_on#} " . date('d M Y', $item->created_date),
                        )
                    ),
                    "price" => array (
                        array(
                            "type" => "text",
                            "text" => "{#price#}"
                        ),
                        array(
                            "type" => "image_text",
                            "image" => "american-dollar-symbol.png",
                            "text" => $item->price
                        )
                    )
                );

                $this->layout->scroll[] = $this->components->uiKitThreeColumnRow($content);
                $this->layout->scroll[] = $this->getSeparator();
            }

        }

        return $this->layout;
    }

    public function tab3()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $bidItems = $this->getData('bids', 'array');
        $user = $this->getData('user', 'array');

        $this->layout->header[] = $this->setHeader(3);

        if (empty($bidItems)) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#you_have_no_completed_bids_yet#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        } else {
            foreach ($bidItems as $item){
                $status_label = '';
                $status_state = '';

                if ( $item->status == UserBidModel::ACCEPTED_STATUS ){
                    $status_label = '{#bid_won#}';
                    $status_state = 'won';
                } else if($item->status == UserBidModel::DECLINED_STATUS){
                    $status_label = '{#bid_lost#}';
                    $status_state = 'lost';
                }

                $content = array(
                    "image" => isset($item->bidItem->images) ? $item->bidItem->images[0]->image : 'formkit-photo-placeholder.png',
                    "details" => array(
                        array(
                            "type" => "title",
                            "text" => $item->bidItem->title
                        ),
                        array (
                            "type" => "status",
                            "text" => strtoupper($status_label),
                            "status_state" => $status_state,
                        )
                    ),
                    "price" => array (
                        array(
                            "type" => "text",
                            "text" => "{#price#}"
                        ),
                        array(
                            "type" => "image_text",
                            "image" => "american-dollar-symbol.png",
                            "text" => $item->price
                        )
                    )
                );

                $action = $this->getOnclickOpenAction('viewbid', false,
                    array("sync_open" =>1, "id" => $item->bidItem->id, "back_button" => 1));

                $this->layout->scroll[] = $this->components->uiKitThreeColumnRow($content, array('onclick' => $action));
                $this->layout->scroll[] = $this->getSeparator();
            }
        }

        return $this->layout;
    }

    public function getSeparator()
    {
        return $this->getComponentText('', array(), array(
            'background-color' => '#eeeeee',
            'height' => '1',
            'width' => 'auto',
        ));
    }

    public function buildItems($items)
    {

        $chunks = array_chunk($items, 2);

        foreach ($chunks as $items_row) {

            $container = [];

            foreach ($items_row as $item) {
                $parameters['info'] = isset($item->bids) ? count($item->bids) . ' bids' : '';
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
                'text' => strtoupper('{#available_bids#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $activeTab === 1 ?? false,
            ),
            array(
                'text' => strtoupper('{#active#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $activeTab === 2 ?? false
            ),
            array(
                'text' => strtoupper('{#completed#}'),
                'onclick' => $this->getOnclickTab(3),
                'active' => $activeTab === 3 ?? false
            )
        ), array(), $styles);
    }

}