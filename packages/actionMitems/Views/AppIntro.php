<?php

namespace packages\actionMitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class AppIntro extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->colors['button_color'] = '#000000';

        $this->model->rewriteActionConfigField('background_color', '#fbe327');

        $content = array(
            array(
                'icon' => 'main.png',
                'description' => 'TatJack is a 24/7 Online Tattoo Convention. You start your search by inputting your location and some of your other criteria and instantly get a list of qualified artists, their bios, their work samples, their costs, and their reviews. You can easily contact or book them through the app. Suddenly, searching for a body artist is actually fun.Rather than scary.',
                'buttons' => array(
                    array(
                        'title' => '{#get_started#}!',
                        'onclick' => $this->getOnclickCompleteAction()
                    )
                )
            )
        );

        $this->layout->scroll[] = $this->uiKitIntroWithButtons($content);

        $this->layout->overlay = [];

        return $this->layout;
    }

}