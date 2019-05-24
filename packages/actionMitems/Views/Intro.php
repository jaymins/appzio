<?php

namespace packages\actionMitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Intro extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    protected $introTexts = array(
        'artist' => array(
            1 => 'Easily sell your art to new potential customers with smart search engine.',
            2 => 'Close more deals faster. TatJack is built and designed especially for Tattoo artists to perform their art effectively.',
            3 => 'TatJack is FREE! Save money with your business marketing activities and avoid expensive advertising cost.'
        ),
        'user' => array(
            1 => 'App provides a medium for you to locate chat with a gifted artist of choice with no stress.',
            2 => 'Easy to use interface with less complexity. You can comfortably find your way out using the app.',
            3 => 'All tattoo Shops in your pocket with only a push of a button.'
        )
    );

    public function tab1()
    {
        $role = $this->model->getSavedVariable('role');

        $this->model->rewriteActionConfigField('background_color', '#000000');

        $content = array(
            array(
                'icon' => $role . '1.png',
                'description' => $this->introTexts[$role][1],
            ),
            array(
                'icon' => $role . '2.png',
                'description' => $this->introTexts[$role][2],
            ),
            array(
                'icon' => $role . '3.png',
                'description' => $this->introTexts[$role][3],
                'buttons' => array(
                    array(
                        'title' => '{#get_started#}!',
                        'onclick' => $this->getOnclickCompleteAction()
                    )
                )
            )
        );

        $this->layout->scroll[] = $this->components->uiKitIntroWithButtons($content);

        $this->layout->overlay[] = $this->components->getIntroScreenOverlay();

        return $this->layout;
    }

}
