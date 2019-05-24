<?php

namespace packages\actionMswipematch\themes\matchswapp\Views;

use Bootstrap\Views\BootstrapView;

class Checkin extends BootstrapView
{

    /* @var \packages\actionMswipematch\themes\matchswapp\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }


    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $my_lat = $this->getData('my_lat', 'mixed');
        $my_lon = $this->getData('my_lon', 'mixed');

        $this->layout->overlay[] = $this->components->getCheckinFloatingButton('checkin');
        $this->layout->overlay[] = $this->components->getListSwipeFloatingButton('checkin');

        $this->setLocation();
        $this->setMap();
        $this->setPlace();


        //$col[] = $this->getComponentImage('icon_swipe_pin.png',[],['width' => '20','height' => '20','margin' => '0 0 0 5']);


        /*        $this->layout->scroll[] = $this->getComponentFormFieldText($this->model->getSubmittedVariableByName('venue_temp'),[
                    'variable' => $this->model->getVariableId('venue_temp')
                ]);*/


        $this->placesNearby();

        $this->layout->footer[] = $this->getComponentDivider();

        $this->layout->footer[] = $this->getComponentText('',[],['height' => '70','background-color' => '#ffffff','width' => '100%']);


        return $this->layout;
    }

    public function setPlace()
    {
        $checked_in = $this->getData('checked_in', 'bool');
        $address = $this->getData('address', 'string');
        $place = $this->getData('place', 'string');
        
        $name[] = $this->getComponentText($place, ['variable' => $this->model->getVariableId('venue_temp')], [
            'color' => '#545050', 'font-size' => '16',
        ]);

        $temp_address = explode(',',$address);

        if(isset($temp_address[0])){
            $address = $temp_address[0];
        }

        $name[] = $this->getComponentText($address, [], ['font-size' => '13', 'color' => '#545050']);
        if ($checked_in) {
            $name[] = $this->getComponentText('{#you_are_checked_in_here#}', [], [
                'font-size' => '13', 'color' => '#545050',
                'margin' => '0 0 0 0']);
        }

        $col[] = $this->getComponentColumn($name,[],['width' => '65%']);

        $region = new \stdClass();
        $region->lat = $this->model->getSavedVariable('lat');
        $region->lon = $this->model->getSavedVariable('lon');


        if($this->model->getConfigParam('max_radius')){
            $region->meters = $this->model->getConfigParam('max_radius');
        } else {
            $region->meters = 300;
        }

        $btns[] = $this->getComponentText('{#search_place#}', ['style' => 'igers_pick_btn',
            'onclick' => $this->getOnclickGooglePlaces('venue_temp', ['id' => 'googlesearch','sync_close' => 1, 'region' => $region])
        ]);

        $btns[] = $this->getComponentText('{#check_in#}', ['style' => 'igers_checkin_btn',
            'onclick' => $this->getOnclickSubmit('checkin/checkin/1')
        ]);

        $col[] = $this->getComponentColumn($btns, [], ['floating' => 1, 'float' => 'right']);


        $this->layout->header[] = $this->getComponentRow($col, [], ['vertical-align' => 'top',
            'with' => '100%',
            'text-align' => 'left',
            "shadow-color" => "#33000000",
            "shadow-radius" => "3",
            "shadow-offset" => "0 1",
            'margin' => '0 0 5 0',
            'background-color' => '#EFF2F1',
            'padding' => '15 15 15 15'
        ]);
    }

    public function setMap()
    {
        $lat = $this->getData('lat', 'mixed');
        $lon = $this->getData('lon', 'mixed');

        if(!$lat OR !$lon){
            return false;
        }

        $position = $lat . ',' . $lon;

        $marker = new \stdClass();
        $marker->position = $position;
        $markers[] = $marker;

        /*        if($my_lat != $lat OR $my_lon != $lon){
                    $marker = new \stdClass();
                    $marker->position = $my_lat.','.$my_lon;
                    $markers[] = $marker;
                }*/

        $this->layout->header[] = $this->getComponentMap([
            'position' => $position,
            'zoom' => '15',
            'id' => 'map',
            'markers' => $markers], ['height' => '220', 'margin' => '0 0 0 0', 'width' => $this->screen_width]);

    }

    public function setLocation()
    {
        $my_address = $this->getData('my_address', 'string');

        $location[] = $this->getOnclickLocation();
        $location[] = $this->getOnclickSubmit('checking/updatelocation/1',['delay' => '0.7']);

        $color = $this->getData('icon_color', 'string');

        $col[] = $this->getComponentImage($color.'_hamburger_icon.png', ['onclick' => $this->getOnclickOpenSidemenu()], ['width' => '24']);

        $width = $this->screen_width - 80;

        if ($my_address) {
            $shortaddress = explode(',', $my_address);
            $row[] = $this->getComponentText('{#my_current_address#}', [], [
                'text-align' => 'center',
                'font-size' => '13',
                'width' => $width,
                'font-weight' => 'bold',
                'color' => $this->color_top_bar_text_color]);
            $row[] = $this->getComponentText($shortaddress[0], [], [
                'text-align' => 'center', 'font-size' => '13',
                'width' => $width,
                'color' => $this->color_top_bar_text_color]);
            $col[] = $this->getComponentColumn($row);
        }


        $col[] = $this->getComponentImage($color.'_icon_compass.png', [
            'onclick' => $location
        ], ['width' => '24', 'margin' => '0 0 5 0', 'floating' => 1, 'float' => "right", 'vertical-align' => 'top']);

        $this->layout->header[] = $this->getComponentRow($col, [], ['padding' => '4 15 5 13', 'background-color' => $this->color_top_bar_color]);
        $this->layout->header[] = $this->getComponentText('',[],['height' => 2,'background-color' => '#e5e5e5','width' => '100%']);

    }

    public function placesNearby()
    {
        $places = $this->getData('places_nearby', 'array');

        /*        $this->layout->header[] = $this->getComponentText('{#places_close_by#}',[],[
                    'font-size' => 13,'font-weight' => 'bold','padding' => '15 15 15 15']);*/
        //$this->layout->header[] = $this->getComponentDivider();

        foreach ($places as $place) {
            $col[] = $this->getComponentImage($place['icon'], [], ['width' => '20']);
            $col[] = $this->getComponentText($place['name'], [], ['font-size' => '13', 'margin' => '0 15 0 15','width' => '65%']);
            $col[] = $this->getComponentText('{#check_in#}', ['style' => 'igers_checkin_btn2',
                'onclick'=>$this->getOnclickSubmit('Checkin/gooleplacecheckin/'.$place['place_id'])]);
            $this->layout->scroll[] = $this->getComponentRow($col, [], [
                'width' => '100%',
                'padding' => '10 15 10 15',
                'vertical-align' => 'middle']);
            $this->layout->scroll[] = $this->getComponentDivider();
            unset($col);
        }

    }


    /* if view has getDivs defined, it will include all the needed divs for the view */
    public function getDivs()
    {
        $divs = new \stdClass();

        return $divs;
    }


}