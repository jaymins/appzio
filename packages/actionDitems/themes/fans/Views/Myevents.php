<?php

namespace packages\actionDitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\venues\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Myevents extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public $added;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#home#}'),
                'onclick' => $this->getOnclickOpenAction('venuehome',false,['transition' => 'none']),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#matches#}'),
                'onclick' => $this->getOnclickOpenAction('venuematches', false, array(
                    'transition' => 'none'
                )),
                'active' => 0
            ),
            array(
                'text' => strtoupper('{#my_events#}'),
                'onclick' => $this->getOnclickOpenAction('mytodos', false, array(
                    'transition' => 'none'
                )),
                'active' => 1
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

        $this->getEvents('my_events','{#my_events#}');

        return $this->layout;
    }

    public function getEvents($var,$title){
        $events = $this->getData($var, 'mixed');

        if(!$events){
            $this->layout->scroll[] = $this->getComponentText('{#you_don\'t_have_any_events_yet#}', [], [
                'text-align' => 'center',
                'padding' => '20 15 20 15',
            ]);
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

            $arr[] = $event->description;
            $arr[] = $event->places->name;
            $arr[] = $event->places->address;
            $onclick = $this->getOnclickOpenAction('showevent',false,
                ['sync_open' => 1, 'id' => $event->id]);
            $this->layout->scroll[] = $this->uiKitListItem($event->name,$arr,
                ['date_icon' => 'mven_icon_home.png','onclick' => $onclick]
            );
            unset($arr);
        }
    }

}