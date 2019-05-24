<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Done extends BootstrapView
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

        $subject = $this->model->localize('{#venue_added#}');

        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $this->model->rewriteActionField('subject', $subject);

        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentText('{#your_venue_was_succesfully_added#}',['style' => 'mved_intro']);

        $this->layout->footer[] = $this->uiKitButtonHollow('{#home#}',[
            'onclick' => $this->getOnclickGoHome()
        ]);
        $this->layout->footer[] = $this->getComponentSpacer(20);
            
        return $this->layout;
    }



}
