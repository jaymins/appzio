<?php

namespace packages\actionDitems\themes\fans\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\venues\Components\Components as Components;
use packages\actionDitems\Models\Model as ArticleModel;

class Showevent extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->showEvent();

        return $this->layout;

    }

    public function showEvent(){
        $event = $this->getData('event', 'mixed');

        if(!isset($event->places)){
            $this->layout->scroll[] = $this->getComponentText('{#no_info#}',[],[
                'margin' => '15 20 15 20','font-size' => '12', 'color' => '#545050'
            ]);

            return false;
        }

        $this->layout->scroll[] = $this->getComponentImage('event_header.png');

        $venue = $event->places;

        $extra_info = @json_decode($event->extra_data);

        $this->layout->scroll[] = $this->uiKitFormSectionHeader($event->name);

        if(isset($extra_info->venue)){
            $col[] = $this->getComponentText('{#venue#}',[],[
                'margin' => '15 20 15 20','width' => '15%','font-size' => '12', 'color' => '#545050'
            ]);
            $col[] = $this->getComponentText($extra_info->venue,[],[
                'margin' => '15 20 15 20','font-size' => '12', 'color' => '#545050'
            ]);

            $this->layout->scroll[] = $this->getComponentRow($col);
        }

        if(isset($extra_info->venue_city)){
            unset($col);

            $col[] = $this->getComponentText('{#city#}',[],[
                'margin' => '15 20 15 20','width' => '15%','font-size' => '12', 'color' => '#545050'
            ]);
            $col[] = $this->getComponentText($extra_info->venue_city,[],[
                'margin' => '15 20 15 20','font-size' => '12', 'color' => '#545050'
            ]);

            $this->layout->scroll[] = $this->getComponentRow($col);
        }


        unset($col);

        if($venue){
            $this->layout->scroll[] = $this->uiKitFormSectionHeader('{#local_event#}');

            $col[] = $this->getComponentText('{#venue#}',[],[
                'margin' => '15 20 15 20','width' => '15%','font-size' => '12', 'color' => '#545050'
            ]);
            $col[] = $this->getComponentText($venue->name,[],[
                'margin' => '15 20 15 20','font-size' => '12', 'color' => '#545050'
            ]);

            $this->layout->scroll[] = $this->getComponentRow($col);

            unset($col);

            $col[] = $this->getComponentText('{#description#}',[],[
                'margin' => '15 20 15 20','width' => '15%','font-size' => '12', 'color' => '#545050'
            ]);
            $col[] = $this->getComponentText($event->description,[],[
                'margin' => '15 20 15 20','font-size' => '12', 'color' => '#545050'
            ]);

            $this->layout->scroll[] = $this->getComponentRow($col);

            
            if($venue->headerimage1){
                $this->layout->scroll[] = $this->getComponentImage($venue->headerimage1,[],['height' => '250','width' => '100%','crop' => 'yes']);
            }

            $row[] = $this->getComponentImage('mven_icon_phone.png',[],['margin' => '0 20 0 20','width'=> '25']);
            $row[] = $this->getComponentText($venue->phone,['style' => 'mivenue_address_2']);
            $this->layout->scroll[] = $this->getComponentRow($row,[
                'onclick' => $this->getOnclickOpenUrl('tel://'.$venue->phone)
            ],['margin' => '20 0 20 0','vertical-align'=>'middle']);
            unset($row);

            $show[] = $this->getOnclickShowElement('map');
            $show[] = $this->getOnclickHideElement('map_row',['transition' => 'none']);
            $show[] = $this->getOnclickShowElement('map_row2',['transition' => 'none']);

            $hide[] = $this->getOnclickHideElement('map');
            $hide[] = $this->getOnclickShowElement('map_row',['transition' => 'none']);
            $hide[] = $this->getOnclickHideElement('map_row2',['transition' => 'none']);


            $row[] = $this->getComponentImage('mven_icon_address.png',[],['margin' => '0 20 0 20','width'=> '25']);
            $row[] = $this->getComponentText($venue->address .' ({#click_to_show_map#})',['style' => 'mivenue_address_2']);
            $this->layout->scroll[] = $this->getComponentRow($row,[
                'onclick' => $show,'id' => 'map_row'
            ],['margin' => '0 0 10 0','vertical-align'=>'middle']);


            unset($row);

            $row[] = $this->getComponentImage('mven_icon_address.png',[],['margin' => '0 20 0 20','width'=> '25']);
            $row[] = $this->getComponentText($venue->address .' ({#click_to_show_map#})',['style' => 'mivenue_address_2']);
            $this->layout->scroll[] = $this->getComponentRow($row,[
                'onclick' => $hide,'id' => 'map_row2','visibility' => 'hidden'
            ],['margin' => '0 0 10 0','vertical-align'=>'middle']);

            $position = $venue->lat .',' .$venue->lon;

            $marker = new \stdClass();
            $marker->position = $position;
            $markers[] = $marker;

            $this->layout->scroll[] = $this->getComponentMap([
                'position' => $position,
                'zoom' => '15',
                'id' => 'map',
                'visibility' => 'hidden',
                'markers' => $markers],['height' => '250','margin' => '0 20 0 20']);

            $this->layout->footer[] = $this->getComponentSpacer('10');

            $participants = $this->getData('participants', 'array');

            if($participants){
                $this->layout->scroll[] = $this->uiKitFormSectionHeader('{#whos_going#}');
                foreach($participants as $participant){
                    $profilepic = $participant['profilepic'] ? $participant['profilepic'] : 'football-icon.png';
                    unset($col);

                    $name = isset($participant['real_name']) ? $participant['real_name'] : '{#anonymous#}';
                    $city = isset($participant['city']) ? $participant['city'] : '';

                    $col[] = $this->getComponentImage($profilepic,['imgwidth' => '80'],['margin' => '0 10 0 20','crop' => 'round','width' => '40']);
                    $col[] = $this->getComponentText($name,[],['font-size' => '14','width' => '60%']);
                    $col[] = $this->getComponentText($city,[],['font-size' => '14']);
                    $this->layout->scroll[] = $this->getComponentRow($col,[],['margin' => '10 0 10 0']);
                    $this->layout->scroll[] = $this->getComponentDivider();
                }

            }

            if(isset($participants[$this->model->playid])){
                $this->layout->footer[] = $this->uiKitButtonFilled('{#cant_make_it#}',[
                    'onclick' => $this->getOnclickSubmit('showevent/going/'.$event->id,[
                    ])
                ]);
            } else {
                $this->layout->footer[] = $this->uiKitButtonFilled('{#im_going#}',[
                    'onclick' => $this->getOnclickSubmit('showevent/going/'.$event->id,[
                    ])
                ]);

            }
            $this->layout->footer[] = $this->getComponentSpacer(20);

        } else {
            $this->layout->scroll[] = $this->getComponentText('{#venue_not_found#}');
        }




    }



}