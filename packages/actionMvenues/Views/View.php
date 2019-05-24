<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {

        $subject = $this->model->localize('{#add_your_venue#}');

        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $this->model->rewriteActionField('subject', $subject);

        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('{#place_adding_intro_text#}',['style' => 'mved_intro']);
        $this->layout->scroll[] = $this->components->getPlacesField('venue_raw_address','{#choose_your_venue#}','mven_icon_home.png');

        $this->layout->scroll[] = $this->getComponentSpacer(20);
        $this->layout->scroll[] = $this->getComponentText('{#didnt_find_your_venue#}? 
{#input_it_manually_by_clicking_here#}',[
            'style' => 'mved_intro','onclick' => $this->getOnclickTab(2)]);

        return $this->layout;
    }


    public function getDivs(){
        //$divs[] = $this->googl
    }

    public function tab2(){
        $layout = new \stdClass();
        $layout->scroll[] = $this->getComponentImage('add-photo-venue.png',[
            'onclick' => $this->getOnclickImageUpload('venue_photo'),'variable' => 'venue_photo'
        ]);

        $name = $this->getData('name', 'string');
        $address = $this->getData('address', 'string');

        $layout->scroll[] = $this->components->getIconField('venue_name', '{#name#}','mven_icon_home.png',['value' => $name]);
        $layout->scroll[] = $this->components->getIconField('venue_address', '{#address#}','mven_icon_address.png',['value' => $address]);
        $layout->scroll[] = $this->components->getIconField('venue_phone', '{#phone#}','mven_icon_phone.png',['value' => $address]);

        $content = $this->getComponentFormFieldTextArea('',['hint' => 'description']);
        $layout->scroll[] = $this->components->getShadowBox($content);

        $layout->footer[] = $this->uiKitButtonHollow('{#add_venue#}',[
            'onclick' => $this->getOnclickSubmit('save_venue')
        ]);

        $layout->footer[] = $this->getComponentSpacer(20);
        return $layout;


    }

}
