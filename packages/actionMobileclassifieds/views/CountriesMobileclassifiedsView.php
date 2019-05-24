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

class CountriesMobileclassifiedsView extends MobileclassifiedsView {

    public $data;
    public $theme;

    public function tab1(){
        $this->data = new StdClass();
        $this->data->scroll[] = $this->renderItem(['id' => 'id']);
        return $this->data;
    }

    private function renderItem($item)
    {
        $clicker = new StdClass();
        $clicker->action = 'open-action';
        $clicker->action_config = $this->getActionidByPermaname('edititem');
        $clicker->id = $item['id'];
        $clicker->sync_open = '1';
        $clicker->back_button = '1';

        return $this->getText('{#Edit item#}', [
            'width' => '50%',
            'background-color' => '#0000FF',
            'color' => '#FFFFFF',
            'font-size' => '16',
            'margin' => '10 10 10 10',
            'padding' => '15 15 15 15',
            'text-align' => 'center',
            'border-width' => '1',
            'border-color' => '#000000',
            'onclick' => $clicker

        ]);
    }
}