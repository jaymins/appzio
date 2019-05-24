<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Edit extends BootstrapView
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

        $subject = $this->model->localize('{#edit_your_venue#}');

        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $this->model->rewriteActionField('subject', $subject);

        $this->layout = new \stdClass();
        $venue = $this->getData('venue', 'mixed');

        if($venue){
            $photo = $venue->headerimage1 ? $venue->headerimage1 : 'add-photo-venue.png';

            $this->layout->scroll[] = $this->getComponentImage($photo,[
                'onclick' => $this->getOnclickImageUpload('venue_photo'),'variable' => 'venue_photo',
                'sync_upload' => 1
            ],['height' => '250','width' => '100%','crop' => 'yes']);

            $this->layout->scroll[] = $this->components->getIconField('venue_name', '{#name#}','mven_icon_home.png',['value' => $venue->name]);
            $this->layout->scroll[] = $this->components->getIconField('venue_address', '{#address#}','mven_icon_address.png',['value' => $venue->address]);
            $this->layout->scroll[] = $this->components->getIconField('venue_phone', '{#phone#}','mven_icon_phone.png',['value' => $venue->phone]);

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentFormFieldTextArea($venue->info, [
                    'hint' => 'Description',
                    'variable' => 'description',
                ])
            ], [
                'parent_style' => 'mreg_shadowbox'
            ], [
                'background-color' => '#ffffff',
            ]);

            $this->layout->footer[] = $this->uiKitButtonHollow('{#save_venue#}',[
                'onclick' => $this->getOnclickSubmit('save_venue')
            ]);

            $this->layout->footer[] = $this->getComponentSpacer(20);

        } else {
            $this->layout->scroll[] = $this->getComponentText('{#venue_not_found#}');
        }




        $this->layout->footer[] = $this->uiKitButtonHollow('{#home#}',[
            'onclick' => $this->getOnclickGoHome()
        ]);
        $this->layout->footer[] = $this->getComponentSpacer(20);


        return $this->layout;
    }



}
