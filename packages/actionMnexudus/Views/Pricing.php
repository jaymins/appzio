<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Pricing extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#pricing#}');

        $col[] = $this->getComponentText('{#please_note_the_booking_can_only_be_made#}',
            ['style' => 'nexudus_uikit_formheader']);

        $col[] = $this->getComponentText('{#if_you_need_thirty_min#}',
            ['style' => 'nexudus_uikit_formheader']);

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '20 30 10 30']);


        $min15 = $this->model->getConfigParam('price_15') ? $this->model->getConfigParam('price_15') : '£12';
        $min30 = $this->model->getConfigParam('price_30') ? $this->model->getConfigParam('price_30') : '£20';
        $min45 = $this->model->getConfigParam('price_45') ? $this->model->getConfigParam('price_45') : '£30';
        $min60 = $this->model->getConfigParam('price_60') ? $this->model->getConfigParam('price_60') : '£40';

        unset($col);
        $col[] = $this->components->getNexudusPriceBall('15m',$min15);
        $col[] = $this->components->getNexudusPriceBall('30m',$min30);
        $this->layout->scroll[] = $this->getComponentRow($col,[],['margin' => '20 15 10 15','text-align' => 'center']);

        unset($col);
        $col[] = $this->components->getNexudusPriceBall('45m',$min45);
        $col[] = $this->components->getNexudusPriceBall('60m',$min60);
        $this->layout->scroll[] = $this->getComponentRow($col,[],['margin' => '20 15 10 15','text-align' => 'center']);


        return $this->layout;
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
