<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Show extends BootstrapView
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
        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $this->layout = new \stdClass();
        $venue = $this->getData('venue', 'mixed');

        if($venue){
            if($venue->headerimage1){
                $this->layout->scroll[] = $this->getComponentImage($venue->headerimage1,[],['height' => '250','width' => '100%','crop' => 'yes']);
            }
            
            $this->layout->scroll[] = $this->uiKitFormSectionHeader($venue->name);
            $this->layout->scroll[] = $this->getComponentSpacer(20);
            $row[] = $this->getComponentImage('mven_icon_address.png',[],['margin' => '0 20 0 20','width'=> '25']);
            $row[] = $this->getComponentText($venue->address,['style' => 'mivenue_address']);
            $this->layout->scroll[] = $this->getComponentRow($row,[],['margin' => '0 0 10 0']);
            unset($row);

            $row[] = $this->getComponentImage('mven_icon_phone.png',[],['margin' => '0 20 0 20','width'=> '25']);
            $row[] = $this->getComponentText($venue->phone,['style' => 'mivenue_address']);
            $this->layout->scroll[] = $this->getComponentRow($row,[
                'onclick' => $this->getOnclickOpenUrl('tel://'.$venue->phone)
            ],['margin' => '0 0 20 0']);

            $position = $venue->lat .',' .$venue->lon;

            $marker = new \stdClass();
            $marker->position = $position;
            $markers[] = $marker;

            $this->layout->scroll[] = $this->getComponentMap([
                'position' => $position,
                'zoom' => '15',
                'markers' => $markers],['height' => '250','margin' => '0 20 0 20']);

            $this->layout->footer[] = $this->getComponentSpacer('10');

            if($this->model->playid == $venue->playid){
                $this->layout->footer[] = $this->uiKitButtonFilled('{#edit#}',[
                    'onclick' => $this->getOnclickOpenAction('editvenue',false,[
                        'sync_open' => 1, 'id' => $venue->id
                    ])
                ]);
                $this->layout->footer[] = $this->getComponentSpacer(20);
            } else {
                $this->layout->footer[] = $this->uiKitButtonFilled('{#navigate#}',[
                    'onclick' => $this->getOnclickOpenUrl('https://www.google.com/maps/place/'.$position)
                ]);
                $this->layout->footer[] = $this->getComponentSpacer(20);
            }



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
