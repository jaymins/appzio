<?php

namespace packages\actionMnexudus\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Faq extends BootstrapView
{

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        $data = $this->getData('faq_items', 'array');
        $header = $this->getData('header', 'string');
        $subject = $this->getData('subject', 'string');

        $this->layout->header[] = $this->components->getBigNexudusHeader('{#help_and_trouble_shooting#}');
        $this->layout->scroll[] = $this->uiKitCollabpsableTextHeader('', $header);

        if (is_array($data) AND !empty($data)) {
            $this->layout->scroll[] = $this->uiKitTextAccordion($data,[
                'icon_closed' => 'icon-nexudus-arrow-down.png',
                'icon_open' => 'icon-nexudus-arrow-up.png',
            ], ['color' => $this->color_text_color]);
        }

        return $this->layout;
    }

    public function getDivs()
    {
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }


}
