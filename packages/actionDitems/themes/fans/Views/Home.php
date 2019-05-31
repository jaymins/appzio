<?php

namespace packages\actionDitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\venues\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Home extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public $added;

    public function tab1()
    {
        $this->layout = new \stdClass();

        if (!$this->model->getSavedVariable('selected_football_club')) {
            $this->layout->onload[] = $this->getOnclickOpenAction('footballteams');
        }

        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#home#}'),
                'onclick' => $this->getOnclickOpenAction('fanhome',false,['transition' => 'none']),
                'active' => 1
            ),
            array(
                'text' => strtoupper('{#matches#}'),
                'onclick' => $this->getOnclickOpenAction('matches', false, array(
                    'transition' => 'none'
                )),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#fan_shop#}'),
                'onclick' => $this->getOnclickOpenAction('fanshop', false, array(
                    'transition' => 'none'
                )),
                'active' => 0
            )
        ));

        $city = $this->getData('city', 'mixed') ? $this->getData('city', 'string') : '{#unknown_location#}';

        /* this is outside of the component, so that it can be included in the overlay */
        $layout = new \stdClass();
        $layout->bottom = '10';
        $layout->height = '60';
        $layout->left = '10';

        $col[] = $this->getComponentImage('fan_icon_location.png',[],['width' => '40']);
        $col[] = $this->getComponentText($city,['style' => 'fan_title_text']);

        $this->layout->overlay[] = $this->getComponentRow($col,[
            'layout' => $layout,
            'onclick' => $this->getOnclickLocation(['sync_open' => 1])],
            [
                'text-align' => 'center',
                'margin' => '10 10 10 10',
                'padding' => '10 10 10 10',
                'background-color' => '#ffffff',
                'shadow-color' => '#DDE2DE',
                'shadow-radius' => '1',
                'shadow-offset' => '0 3',
            ]
        );

        if($this->getData('my_events', 'mixed')){
            $this->getEvents('my_events','{#my_events#}');
        }

        $this->getEvents('events','{#match_events_close_to_you#}');

        return $this->layout;

    }

    public function getEvents($var,$title){
        $events = $this->getData($var, 'mixed');
        $this->layout->scroll[] = $this->uiKitFormSectionHeader($title);

        if(!$events){
            $this->layout->scroll[] = $this->getComponentText('{#no_events_added#}');
            return true;
        }

        foreach($events as $event){
            if(!isset($event->places->name)){
                continue;
            }
            
            if(in_array($event->id, $this->added)){
               //continue;
            }

            $this->added[] = $event->id;

            $image = $event->places->headerimage1 ? $event->places->headerimage1 : 'mven_icon_home.png';

            $arr[] = $event->description;
            $arr[] = $event->places->name;
            $arr[] = $event->places->address;
            $onclick = $this->getOnclickOpenAction('showevent',false,
                ['sync_open' => 1, 'id' => $event->id]);
            $this->layout->scroll[] = $this->components->getVenueList($event->name,$arr,
                ['date_icon' => $image,'onclick' => $onclick]
            );
            unset($arr);
        }
    }



}