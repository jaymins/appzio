<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Entry extends BootstrapView
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

        $this->layout->scroll[] = $this->getComponentImage('add-photo-venue.png',[
            'onclick' => $this->getOnclickImageUpload('venue_photo'),'variable' => 'venue_photo',
            'sync_upload' => 1
        ],['height' => '250','width' => '100%','crop' => 'yes']);

        $name = $this->getData('name', 'string');
        $address = $this->getData('address', 'string');

        $this->layout->scroll[] = $this->components->getIconField('venue_name', '{#name#}','mven_icon_home.png',['value' => $name]);
        $this->layout->scroll[] = $this->components->getIconField('venue_address', '{#address#}','mven_icon_address.png',['value' => $address]);
        $this->layout->scroll[] = $this->components->getIconField('venue_phone', '{#phone#}','mven_icon_phone.png');

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentFormFieldTextArea('', [
                'hint' => 'Description',
                'variable' => 'description',
            ])
        ], [
            'parent_style' => 'mreg_shadowbox'
        ], [
            'background-color' => '#ffffff',
        ]);

        $this->layout->footer[] = $this->uiKitButtonHollow('{#add_venue#}',[
            'onclick' => $this->getOnclickSubmit('save_venue')
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(20);
        
        return $this->layout;
    }

}