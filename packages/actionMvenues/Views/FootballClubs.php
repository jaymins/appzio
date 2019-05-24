<?php

namespace packages\actionMvenues\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMvenues\Components\Components;
use packages\actionMvenues\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class FootballClubs extends BootstrapView
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
        $this->layout = new \stdClass();

        if ($this->getData('saved', 'num')) {
            $action = $this->getOnclickOpenAction('creatclubitem');
            if ($this->model->getSavedVariable('role') == 'fan') {
                $action = $this->getOnclickOpenAction('fanclubs', array('sync_open' => 1));
            }

            $this->layout->onload[] = $action;
        }

        $image = $this->model->getConfigParam('actionimage1');
        $this->layout->header[] = $this->components->HeaderWithImageBgr($image,
            array('title' => '{#please_select_your_club#}',
                'description' => '{#and_connect_with_fans_around_the_world#}'));


        $footballClubs = $this->getData('footballClubs', 'mixed');
        
        $selected = $this->getData('selectedFootballClub', 'num');

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        $row = [];
        $count = 0;
        foreach ($footballClubs as $footballClub) {

            $checkbox = [];

            $active = 0;
            if ($selected == $footballClub->id) {
                $active = 1;
            }

            $selectstate = array(
                'variable' => 'selectedFootballClub',
                'style_content' => array(
                    "background-image" => $this->getImageFileName("Bayern.png"),
                    "background-size" => "contain",
                    "width" => "150",
                    "height" => "150",
                    "text-align" => "center",
                    "vertical-align" => "middle",
                ),
                'allow_unselect' => 1,
                'variable_value' => $footballClub->id,
                'animation' => 'flip',
                'active' => $active
            );
            $checkbox[] = $this->getComponentText(' ', array(
                'variable' => 'selectedFootballClub',
                'allow_unselect' => 1,
                'variable_value' => 0,
                'selected_state' => $selectstate
            ), array(
                "background-image" => $this->getImageFileName("Bayern.png"),
                "background-size" => "contain",
                "width" => "150",
                "height" => "150",
                "text-align" => "center",
                "vertical-align" => "middle",
                "filter" => 'gray'
            ));


            $row[] = $this->getComponentRow($checkbox, array(), array(
                'color' => '#161616',
                'text-align' => 'center',
                'width' => '48%',
                'vertical-align' => 'middle'
            ));

            if ($count) {
                $this->layout->scroll[] = $this->getComponentRow($row, array(), array(
                    'text-align' => 'center'
                ));
                $row = [];
                $count = 0;
            }

            $count ++;
        }

        if ($row) {
            $this->layout->scroll[] = $this->getComponentRow($row, array(), array(
                'text-align' => 'center', 'width' => '48%'
            ));
        }

        $this->layout->footer[] = $this->uiKitButtonHollow('{#select#}',[
            'onclick' => [
                $this->getOnclickSubmit('select_football_club', array('sync_open')),
            ]
        ], [
            'margin' => '20 80 20 80'
        ]);

        return $this->layout;
    }
}
