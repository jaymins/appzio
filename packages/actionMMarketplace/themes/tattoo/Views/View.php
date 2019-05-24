<?php

namespace packages\actionMMarketplace\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMMarketplace\Models\BidItemModel;
use packages\actionMMarketplace\Models\Model as ArticleModel;
use packages\actionMMarketplace\Components\Components;

class View extends BootstrapView
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
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $bidObject = $this->getData('bidObject', 'object');

        $this->model->rewriteActionField('subject', !empty($bidObject->title) ? $bidObject->title : '');

        if ($this->model->getSavedVariable('role') != 'artist') {
            $status = isset($bidObject->status) ? $bidObject->status : 'active';
            $this->layout->scroll[] = $this->setHeader(1, $status);
        }

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#tattoo_style#}');
        if (!empty($bidObject->styles)) {
            $myStyles = json_decode($bidObject->styles, true);
            if ($myStyles) {
                foreach ($myStyles as $myStyle) {
                    $this->layout->scroll[] = $this->getComponentRow(
                        array(
                            $this->getComponentText('{#' . $myStyle . '#}', array(), array(
                                "padding" => "0 15 10 15",
                            ))
                        ));
                }
            }
        }else {
            $this->layout->scroll[] = $this->getComponentText('{#this_tattoo_doesn\'t_have_any_styles#}', [], [
                'padding' => '10 15 20 15',
                'margin' => '0 0 0 0',
            ]);
        }

        $images = $this->getData('images', 'array');

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#example_pictures#}');
        $imageElements = [];
        for ($i = 0; $i <= 2; $i++) {
            $imageName = '';
            if (isset($images[$i])) {
                $imageName = $images[$i]->image;
            }

            $imageElements[] = $this->getComponentImage($imageName, array(
                'priority' => 9,
                'defaultimage' => 'formkit-photo-placeholder.png',
                'tap_to_open' => 1,
            ), array(
                'width' => '85',
                'height' => '85',
                'imgcrop' => 'yes',
                'crop' => 'yes',
                'border-radius' => '3',
                'margin' => '0 5 0 5'
            ));
        }
        $this->layout->scroll[] = $this->getComponentRow($imageElements, array(), array(
            'text-align' => 'center',
            'padding' => '20 0 20 0'
        ));

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#description#}');

        $this->layout->scroll[] = $this->getComponentText(!empty($bidObject->description) ? $bidObject->description : '', array(), array(
            "color" => "#777777",
            "margin" => "0 15 10 15"
        ));

        $this->layout->scroll[] = $this->uiKitBackgroundHeader('{#valid_until#}');

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage('calendar_black.png',array(), array(
                'width' => '20',
            )),
            $this->getComponentText(!empty($bidObject->valid_date) ? date('d M Y', $bidObject->valid_date) : '', array(), array(
                'margin' => '0 0 0 10',
                'font-weight' => 'bold'
            ))
        ), array(), array(
            'margin' => '0 15 0 15'
        ));

        if (!empty($bidObject->id)) {
            $this->renderButtons($bidObject->id);
        }

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();
        $bidObject = $this->getData('bidObject', 'object');
        $status = isset($bidObject->status) ? $bidObject->status : 'active';
        $this->layout->scroll[] = $this->setHeader(2, $status);
        $userBidObjects = $this->getData('userBidObjects', 'array');
        $this->model->rewriteActionField('subject', !empty($bidObject->title) ? $bidObject->title : '');

        if ( empty($userBidObjects) ) {
            $this->layout->scroll[] = $this->components->uiKitDefaultHeader('{#no_bidders_yet#}', [], [
                'padding' => '15 15 15 15',
                'text-align' => 'center',
            ]);
        }

        foreach ($userBidObjects as $userBidObject) {

            $user = $this->model->foreignVariablesGet($userBidObject->play_id);
            $content = array(
                "image" => $user['profilepic'],
                "details" => array(
                    array (
                        "type" => "title",
                        "text" => $bidObject->title
                    ),
                    array (
                        "type" => "description",
                        "text" => "{#bid_submited_on#} " .  date('d M Y', $userBidObject->created_date),
                    )
                ),
                "price" => array (
                    array(
                        "type" => "text",
                        "text" => "{#your_price#}"
                    ),
                    array(
                        "type" => "image_text",
                        "image" => "american-dollar-symbol.png",
                        "text" => $userBidObject->price
                    )
                )
            );

            $action = $this->getOnclickOpenAction('viewuserbid', false,
                array("sync_open" =>1, "id" => $userBidObject->id, "back_button" => 1));
            $this->layout->scroll[] =$this->components->uiKitThreeColumnRow($content, array('onclick' => $action));
            $this->layout->scroll[] = $this->getComponentDivider(array("height" => 2));

        }

        return $this->layout;
    }


    protected function setHeader($activeTab, $status = 'active')
    {
        if($status == BidItemModel::COMPLETE_STATUS){
            $tabName = '{#selected_artist#}';
        }
        else{
            $tabName = '{#bidders#}';
        }

        $styles = array(
            "font-size" => "10",
            "padding" => "10 0 10 0",
            "color" => '#9c9b9b',
            "border-color" => '#dedede',
            "width" => "25%",
            "text-align" => 'center'
        );
        return $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#bid_info#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $activeTab === 1 ?? false,
            ),
            array(
                'text' => strtoupper($tabName),
                'onclick' => $this->getOnclickTab(2),
                'active' => $activeTab === 2 ?? false
            )
        ), array(), $styles);
    }

    public function renderButtons($id) {

        $bidObject = $this->getData('bidObject', 'object');
        if ($bidObject->status != BidItemModel::COMPLETE_STATUS) {
            if ($this->model->getSavedVariable('role') == 'artist') {
                $placeBid = $this->getOnclickShowDiv('place_bid', array(
                    'background' => 'blur',
                    'tap_to_close' => 1,
                    'transition' => 'from-bottom',
                    'layout' => $this->getDivLayout()
                ));

                $this->layout->footer[] = $this->getComponentRow(array(
                    $this->getComponentText('{#place_bid#}', array(
                        'onclick' => $placeBid,
                        'style' => 'tattoo_marketplace_submit_button'
                    ))
                ), array(
                    'style' => 'tattoo_marketplace_submit_button_wrapper'
                ));
            } else {
                $this->layout->footer[] = $this->getComponentRow(array(
                    $this->getComponentText('{#cancel_bid#}', array(
                        'onclick' => array(
                            $this->getOnclickSubmit('Controller/cancel/' . $id),
                            $this->getOnclickOpenAction('marketplace', false, array("sync_open" => 1))
                        ),
                        'style' => 'tattoo_marketplace_submit_button'
                    ))
                ), array(
                    'style' => 'tattoo_marketplace_submit_button_wrapper'
                ));
            }
        }
    }

    public function getDivs()
    {

        $bidObject = $this->getData('bidObject', 'object');

        $id = '';
        if (!empty($bidObject->id)) {
            $id = $bidObject->id;
        }

        $divs['place_bid'] =
            $this->getComponentColumn(array(
                $this->uiKitDivHeader('{#place_a_bid#}', array(
                    'close_icon' => 'cross-sign.png',
                    'div_id' => 'place_bid',
                )),
                $this->getComponentFormFieldText('', array(
                    'hint' => '{#price#}',
                    'variable' => 'price',
                    "input_type" => "number"
                ), array(
                    'padding' => '0 0 0 0',
                    'margin' => '20 15 0 15'
                )),
                $this->getComponentSpacer('1', array(), array(
                    'background-color' => '#dadada',
                    'opacity' => '0.5',
                    'margin' => '0 15 10 15'
                )),
                $this->getComponentFormFieldTextArea('', array(
                    'hint' => '{#message#}',
                    'variable' => 'message'
                ), array(
                    'padding' => '0 0 0 0',
                    'margin' => '5 15 5 15',
                    'height' => '75'
                )),
                $this->getComponentSpacer('1', array(), array(
                    'background-color' => '#dadada',
                    'opacity' => '0.5',
                    'margin' => '0 15 0 15'
                )),
                $this->getComponentSpacer(50),
                $this->uiKitWideButton('{#place_bid#}', array(
                    'onclick' => array(
                        $this->getOnclickHideDiv('place_bid'),
                        $this->getOnclickSubmit('Controller/placebid/'. $id),
                        $this->getOnclickOpenAction('marketplace', false, array("sync_open" => 1))
                    )
                ))
            ), array(), array(
                'background-color' => '#ffffff'
            ));
        return $divs;
    }

    private function getDivLayout() {
        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        return $layout;
    }
}