<?php

/*

    Layout code codes here. It should have structure of
    $this->data->scroll[] = $this->getElement();

    supported sections are header,footer,scroll,onload & control
    and they should always be arrays

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MyitemsMobileclassifiedsView extends MobileclassifiedsView {

    public $data;
    public $theme;
    public $margin;
    public $itemWidth;

    public function tab1(){
        $this->data = new StdClass();

        //$this->data->onload = $this->getRedirect( $this->getActionidByPermaname( 'items' ) );

        $this->getFilter();

        $this->getSearch();

        $this->setDimensions();

        $this->renderItems();

        return $this->data;
    }

    private function setDimensions()
    {
        $this->margin = 10;
        $this->itemWidth = ($this->screen_width - ($this->margin * 3) ) / 2;
    }

    private function renderItems()
    {
        if ($this->menuid == 'searchbox') {
            $searchterm = '';
            if(isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 0) {
                $searchterm = $this->submitvariables['searchterm'];
            }
            $items = $this->itemsModel->getMyItems($searchterm);
        } else {
            $items = $this->itemsModel->getMyItems();
        }

        foreach ($items as $key => $item) {
            $column[] = $this->renderItem($item);

            if ($key % 2 == 1 || $key == count($items) - 1) {
                $this->data->scroll[] = $this->getRow($column, [
                    'margin' => $this->margin . ' 0 0 ' . $this->margin
                ]);
                unset($column);
            } else {
                $column[] = $this->getVerticalSpacer($this->margin);
            }
        }
    }

    private function renderItem($item)
    {
        $clicker = new StdClass();
        $clicker->action = 'open-action';
        $clicker->action_config = $this->getActionidByPermaname('details');
        $clicker->id = $item['id'];
        $clicker->sync_open = '1';
        $clicker->back_button = '1';

        $picture = !is_null($item['pictures']) && isset(json_decode($item['pictures'])[0])? json_decode($item['pictures'])[0] : '';
        $row[] = $this->getImage($picture, [
            'crop' => 'yes',
            'height' => $this->itemWidth,
            'defaultimage' => 'profile-add-photo-grey.png',
            'onclick' => $clicker
        ]);

        $row[] = $this->getText($item['title'], [
            'background-color' => '#FFFFFF',
            'color' => '#4B4B4B',
            'padding' => '5 5 0 5'
        ]);

        $row[] = $this->getRow([
            $this->getText('$ '. $item['price'] / 100, [
                'color' => '#6DC077'
            ])
        ], [
            'background-color' => '#FFFFFF',
            'padding' => '5 5 5 5'
        ]);

        return $this->getColumn($row, [
            'width' => $this->itemWidth,
            'border-radius' => '5'
        ]);
    }
}