<?php

namespace packages\actionMitems\Components;

use Bootstrap\Components\BootstrapComponent;

trait ItemCard
{

    /**
     * Makes an item card that is used in different listings
     *
     * @param $options array
     * @return \stdClass
     */
    public function getItemCard($options)
    {
        /** @var BootstrapComponent $this */

        $item = $options['item'];
        $image = $options['image'];

        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->back_button = 1;
        $onclick->action_config = $this->model->getActionidByPermaname('singletattoo');
        $onclick->sync_open = 1;
        $onclick->sync_close = 1;
        $onclick->id = $item->id;

        return $this->getComponentRow(array(
            $this->getItemCardImage($image),
            $this->getComponentColumn(array(
                $this->getItemInformation($item),
                $this->getItemCategory($item),
                $this->getItemTags($item)
            ), array(), array(
                'width' => 'auto'
            ))
        ), array(
            'onclick' => $onclick,
            'style' => 'item_card'
        ));
    }

    protected function getItemCardImage($image)
    {
        return $this->getComponentImage($image, array(), array(
            'width' => '120',
            'height' => '120',
            'crop' => 'yes',
        ));
    }

    protected function getItemInformation($item)
    {
        $time = $item->time > 0 ?
            $item->time . ' h' : '';

        return $this->getComponentRow(array(
            $this->getComponentText($item->name, array('style' => 'item_card_information_name')),
            $this->getComponentText($time, array('style' => 'item_card_information_price'))
        ), array(), array(
            'padding' => '5 10 5 10',
            'width' => 'auto'
        ));
    }

    protected function getItemCategory($item)
    {
        if ($item->category) {
            $categoryName = $item->category->name;
        } else if (count($item->categories) > 0) {
            $categoryName = $item->categories[0]->name;
        } else {
            $categoryName = '';
        }

        return $this->getComponentRow(array(
            $this->getComponentText($categoryName, array('style' => 'item_tag'))
        ), array(), array(
            'margin' => '10 0 0 10'
        ));
    }

    protected function getItemTags($item)
    {
        $maxCharacters = 16;
        $currectCharacters = 0;

        $maxCount = 2;
        $count = 1;
        $tagsList = array();

        foreach ($item->tags as $tag) {
            if ($count <= $maxCount && $currectCharacters <= $maxCharacters) {
                $tagsList[] = $this->getComponentText($tag->name, array('style' => 'item_tag'));
            } else {
                $tagsList[] = $this->getComponentText('...', array('style' => 'item_tag'));
                break;
            }
            
            $count++;
            $currectCharacters = strlen($tag->name);
        }

        return $this->getComponentRow($tagsList, array(), array(
            'margin' => '5 0 0 10'
        ));
    }

}