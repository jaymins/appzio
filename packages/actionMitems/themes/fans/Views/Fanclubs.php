<?php

namespace packages\actionMitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\themes\fans\Components\Components as Components;
use packages\actionMitems\themes\fans\Models\Model as ArticleModel;

class Fanclubs extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#ffffff');

        $image = $this->model->getConfigParam('actionimage1');
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage($image, array(), array(
                'width' => 'auto',
                'margin' => '0 0 10 0'
            ))
        ), array(), array(
            'text-align' => 'center'
        ));

        $data = $this->getData('fanclubs', 'mixed');
        
        if ( empty($data) ) {
            $this->layout->scroll[] = $this->uiKitInfoTileTitle('{#missing_fan_clubs#}');
            return $this->layout;
        }
        
        foreach ($data as $item) {
            $this->layout->scroll[] = $this->fanClubItem($item);
            $this->layout->scroll[] = $this->uiKitDivider();
        }

        $this->layout->footer[] = $this->uiKitButtonHollow('{#see_events#}', array(
            'onclick' => $this->getOnclickGoHome(),
        ), array(
            'margin' => '20 80 20 80'
        ));

        return $this->layout;
    }

    public function fanClubItem($item) {

        $item_id = $this->model->isFanclubLiked($item->id);
        $actionLike = [$this->getOnclickSubmit('like_item_' . $item->id)];

        $likedEl = $this->getComponentText(' ', array(), array('width' => 20));

        if ($item_id) {
            // $actionLike = [];
            $actionLike = $this->getOnclickSubmit('dislike_item_' . $item_id);
            $likedEl = $this->getComponentImage('success-3.png', array(), array(
                'width' => 20,
                'floating' => '1',
                'float' => 'right',
            ));
        }

        return $this->getComponentRow(array(
            $this->getComponentText($item->name, array(), array(
                'font-size' => 12,
                'margin' => '0 0 0 0',
                'width' => '45%'
            )),
            $this->getComponentRow(array(
                $this->getComponentImage('avatar.png', array(), array(
                    'width' => 20,
                    'margin' => '0 5 0 0',
                )),
                $this->getComponentText('2568 fans', array(), array('font-size' => 12))
            ), array(), array(
                'margin' => '5 15 5 0'
            )),
            $this->getComponentRow(array(
                $this->getComponentImage('flag_img.png', array(), array(
                    'width' => 20,
                    'margin' => '0 5 0 0',
                )),
                $this->getComponentText('Bulgaria', array(), array('font-size' => 12))
            ), array(), array('margin' => '5 10 5 0')),
            $likedEl
        ), array(
            'onclick' => $actionLike
        ), array(
            'margin' => '10 15 10 15',
            'vertical-align' => 'middle',
        ));

    }

}