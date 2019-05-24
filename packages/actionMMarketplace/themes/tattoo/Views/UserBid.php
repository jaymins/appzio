<?php

namespace packages\actionMMarketplace\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\Model as ArticleModel;
use packages\actionMMarketplace\Components\Components;

class UserBid extends BootstrapView
{

    /**
     * @var ArticleModel
     */
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
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $userBidObject = $this->getData('userBidObject', 'object');
        $user = $this->getData('user', 'array');
        $bidObject = $this->getData('bidObject', 'object');

        if ( empty($user) ) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#missing_data#}');
            return $this->layout;
        }

        $this->model->rewriteActionField('subject', !empty($user['firstname']) ? $user['firstname'] . "'s bid" : '');

        $content = array(
            "image" => $user['profilepic'],
            "details" => array(
                array (
                    "type" => "title",
                    "text" => $user['firstname'] . ' ' . $user['lastname']
                ),
                array (
                    "type" => "description",
                    "text" => "{#bid_submited_on#} " . date('d M Y', $userBidObject->created_date),
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
                    "text" => $userBidObject->price
                )
            )
        );

        $action = $this->getOnclickOpenAction('profile', false,
            array("sync_open" =>1, "id" => $userBidObject->play_id, "back_button" => 1));
        $this->layout->scroll[] =$this->components->uiKitThreeColumnRow($content, array('onclick' => $action));

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#message_from#} '. $user['firstname']);
        $this->layout->scroll[] = $this->getComponentText($userBidObject->message, array(), array(
            "color" => "#777777",
            "margin" => "0 15 0 15"
        ));

        $this->renderOtherItems();

        if ($this->model->getSavedVariable('role') != 'artist'
        && $bidObject->status != BidItemModel::COMPLETE_STATUS) {
            $this->renderButtons($userBidObject);
        }

        return $this->layout;
    }


    public function renderButtons($userBidObject) {

            $this->layout->footer[] = $this->getComponentRow(array(
                $this->getComponentText('{#select_artist#}', array(
                    'onclick' => array(
                        $this->getOnclickSubmit('Userbid/select/' . $userBidObject->id),
                        $this->getOnclickOpenAction('viewbid', false, array("id" =>  $userBidObject->bid_item_id, "sync_open" => 1))
                    )
                ), array(
                    "background-color" => "#fbe121",
                    "color" => "#333333",
                    "border-radius" => "20",
                    "width" => "80%",
                    "padding" => "10 30 10 30",
                    "text-align" => "center"
                ))
            ), array(), array(
                "text-align" => "center",
                'margin' => '10 0 10 0'
            ));

        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentText('{#decline_artist#}', array(
                'onclick' => array(
                    $this->getOnclickSubmit('Userbid/decline/' .  $userBidObject->id),
                    $this->getOnclickOpenAction('viewbid', false, array("id" =>  $userBidObject->bid_item_id, "sync_open" => 1))
                )
            ), array(
                "background-color" => "#787777",
                "color" => "#ffffff",
                "border-radius" => "20",
                "width" => "80%",
                "padding" => "10 30 10 30",
                "text-align" => "center"
            ))
        ), array(), array(
            "text-align" => "center",
            'margin' => '10 0 10 0'
        ));


    }

    public function renderOtherItems()
    {

        $user = $this->getData('user', 'array');

        $otherItems = $this->model->getOtherArtistItems($user['playid'], 100);

        if (empty($otherItems)) {
            return false;
        }

        $this->layout->scroll[] = $this->components->getComponentSpacer(30);

        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper('{#other_works_from#} ' . $user['firstname']));

        $rows = array_chunk($otherItems, 3);

        $output_rows = array();

        foreach ($rows as $row_items) {

            $otherItemsRow = array();

            foreach ($row_items as $row_item) {
                $onclick = new \stdClass();
                $onclick->action = 'open-action';
                $onclick->back_button = 1;
                $onclick->action_config = $this->model->getActionidByPermaname('singletattoo');
                $onclick->sync_open = 1;
                $onclick->id = $row_item->id;

                $images = $row_item->getImages();

                if (strlen($row_item->name) > 10) {
                    $row_item->name = substr($row_item->name, 0, 8) . '...';
                }

                $otherItemsRow[] = $this->getComponentColumn(array(
                    $this->getComponentRow(array(
                        $this->getComponentImage($images->itempic, array(
                            'priority' => 9,
                            'onclick' => $onclick
                        ), array(
                            'width' => '100%',
                            'height' => $this->screen_width / 3.3,
                            'crop' => 'yes',
                            'border-radius' => '3',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'height' => $this->screen_width / 3.3,
                    )),
                    $this->getComponentRow(array(
                        $this->getComponentText($row_item->name, array(), array(
                            'color' => '#000000',
                            'margin' => '0 0 0 0',
                            'padding' => '3 0 0 0',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'text-align' => 'center',
                    ))
                ), array(), array(
                    'width' => $this->screen_width / 3.3,
                    'padding' => '0 3 0 3',
                ));

            }

            $output_rows[] = $this->getComponentRow($otherItemsRow, array(), array(
                'margin' => '20 15 20 15',
                'width' => 'auto'
            ));

        }

        $this->layout->scroll[] = $this->getComponentSwipe($output_rows);
    }
}