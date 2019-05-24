<?php

namespace packages\actionMnexudus\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Viewbooking extends Makebooking
{

    /* @var Components */
    public $components;
    public $theme;
    public $booking_data;
    public $current_booth;
    public $booking_id;

    private $no_navi;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $deleted = $this->getData('deleted', 'mixed');
        $this->booking_id = $this->getData('booking_id', 'mixed');

        if($deleted){
            $this->layout->header[] = $this->components->getBigNexudusHeader('{#booking_deleted#}', [
                'onclick' => $this->getOnclickOpenAction('home')
            ]);

            $this->layout->scroll[] = $this->getComponentSpacer(50);
            $this->layout->scroll[] = $this->getComponentText('{#your_booking_has_been_cancelled#}',['style' => 'nexudus_uikit_loginheader']);
            $this->layout->scroll[] = $this->getComponentText('{#we_hope_to_see_you_soon#}!',['style' => 'nexudus_uikit_loginheader']);
            $this->setHomeButton();
            return $this->layout;
        }

        $this->booking_data = $this->getData('booking', 'array');
        $this->setHeader();
        $this->setDescription();


        $this->setBoothPic();

        $this->setCounter();
        $this->setButtons();

        if (isset($this->model->validation_errors['delete_booking'])) {
            $this->layout->header[] = $this->getComponentText($this->model->validation_errors['delete_booking'], [
                'style' => 'nexudus_error_header'
            ], [

            ]);
        }

        return $this->layout;
    }


    public function tab2()
    {
        $this->layout = new \stdClass();
        $deleted = $this->getData('deleted', 'mixed');
        $this->booking_id = $this->getData('booking_id', 'mixed');
        $this->booking_data = $this->getData('booking', 'array');


        $starttime = isset($this->booking_data['booking']['FromTime']) ? $this->booking_data['booking']['FromTime'] : '';
        $starttime = strtotime($starttime);


        if($starttime < time()+86400){
            $this->setHeader();
            $this->layout->scroll[] = $this->getComponentText('{#no_cancellation_title#}',['style' => 'nexudus_big_header']);
            $this->layout->scroll[] = $this->getComponentText('{#no_cancellation_explainer#}',['style' => 'nexudus_uikit_loginheader']);
            $this->setButtonsTab2NoDelete();
            return $this->layout;
        }


        if($deleted){
            $this->layout->header[] = $this->components->getBigNexudusHeader('{#booking_deleted#}', [
                'onclick' => $this->getOnclickOpenAction('home')
            ]);

            $this->layout->scroll[] = $this->getComponentSpacer(50);
            $this->layout->scroll[] = $this->getComponentText('{#your_booking_has_been_cancelled#}',['style' => 'nexudus_uikit_loginheader']);
            $this->layout->scroll[] = $this->getComponentText('{#we_hope_to_see_you_soon#}!',['style' => 'nexudus_uikit_loginheader']);
            $this->setHomeButton();
            return $this->layout;
        }

        $this->setHeader();

        $this->layout->scroll[] = $this->getComponentText('{#are_you_sure_you_want_to_cancel_your_booking#}?',['style' => 'nexudus_big_header']);


        if (isset($this->model->validation_errors['delete_booking'])) {
            $this->layout->header[] = $this->getComponentText($this->model->validation_errors['delete_booking'], [
                'style' => 'nexudus_error_header'
            ], [

            ]);
        }

        $this->setButtonsTab2();
        return $this->layout;
    }

    public function setBoothPic(){

        if(isset($this->booking_data['space']['Id'])){
            $id = $this->booking_data['space']['Id'];
            $pic = 'https://myworkbooth.spaces.nexudus.com/en/publicresources/getimage/'.$id.'?h=400&w=900';
        }

        if(!$pic){
            $id = isset($this->booking_data['space']['Id']) ? $this->booking_data['space']['Id'] : false;
            $cache = \Appcaching::getGlobalCache('nex_boothpics_'.$id);

            if($cache){
                $pic = $cache;
            } else {
                $pic = 'booth'.rand(1,4).'.jpg';
                \Appcaching::setGlobalCache('nex_boothpics_'.$id, $pic);
            }
        }

        $pic_height = round($this->screen_height / 4,0);
        $this->layout->scroll[] = $this->getComponentImage($pic,[],['height' => $pic_height,'crop' => 'yes','width' => $this->screen_width]);

    }

    public function setDescription(){
        if(isset($this->booking_data['space']['Description'])){
            $description = trim(strip_tags($this->booking_data['space']['Description']));
            $bubble[] = $this->getComponentText($description,['style' => 'nexudus_regular_text']);
            $content = $this->getComponentColumn($bubble,[],['margin' => '0 0 15 0']);
            $this->layout->scroll[] = $this->components->getNexudusBlueBubble($content);
        }
    }

    public function setCounter(){

        $radar[] = $this->getComponentSpacer('40');
        $radar[] = $this->getComponentImage('radar6.gif',[],['width' => '70']);
        $this->layout->scroll[] = $this->getComponentColumn($radar,[
            'id' => 'radar',
            'visibility' => 'hidden'
        ],['text-align' => 'center','margin' => '0 0 0 0']);

        $starttime = isset($this->booking_data['booking']['FromTime']) ? $this->booking_data['booking']['FromTime'] : '';
        $starttime = strtotime($starttime);
        $counter[] = $this->getComponentSpacer(20);
        $counter[] = $this->getComponentText('{#your_booking_starts_after#}',['style' => 'nexudus_uikit_loginheader']);
        $counter[] = $this->getComponentSpacer(20);

        if($starttime > time()){
            $counter[] = $this->components->getNexudusTimer($starttime,['id' => $this->booking_id.rand(3893,393939)]);
            $this->layout->scroll[] = $this->getComponentColumn($counter,[
                'id' => 'counter'
            ]);
        }

    }

    public function setButtons(){

        $error = $this->getData('error', 'mixed');
        if($error){
            $this->layout->footer[] = $this->getComponentSpacer(20);
            $this->layout->footer[] = $this->getComponentText($error,['style' => 'nexudus_uikit_loginheader']);
            $this->layout->footer[] = $this->getComponentSpacer(20);
        }


        $doorid = isset($this->booking_data['doorid']) ? $this->booking_data['doorid'] : '123';
        $doorid = '';

/*        $this->layout->footer[] = $this->uiKitButtonHollow('{#scan#}',[
            'onclick' => [$this->getOnclickDoorflowScan()
            ]
        ],['background-color' => '#656565','color' => '#ffffff']);

        $this->layout->footer[] = $this->getComponentSpacer(20);*/

        $id = isset($this->booking_data['booking']['Id']) ? $this->booking_data['booking']['Id'] : false;

        $this->layout->footer[] = $this->getComponentSpacer(20);

        $starttime = isset($this->booking_data['booking']['FromTime']) ? $this->booking_data['booking']['FromTime'] : '';
        $starttime = strtotime($starttime);

        if($starttime < time()) {
            $onclicks[] = $this->getOnclickHideElement('counter');
        }

        $onclicks[] = $this->getOnclickShowElement('radar');
        $onclicks[] = $this->getOnclickDoorflowUnlock($doorid);
        $onclicks[] = $this->getOnclickHideElement('radar',['delay' => 8]);


        $this->layout->footer[] = $this->uiKitButtonFilled('{#unlock_door#}',[
            'onclick' => $onclicks
        ],['background-color' => $this->color_top_bar_color,'color' => $this->color_top_bar_text_color]);
        $this->layout->footer[] = $this->getComponentSpacer(20);


        $this->layout->footer[] = $this->getComponentText('{#cancel_booking#}',[
            'onclick' => [$this->getOnclickTab(2),
                //$this->getOnclickOpenAction('home')
            ]
        ],['color' => '#B2B4B3','text-align' => 'center','font-size' => '14']);

        $this->layout->footer[] = $this->getComponentSpacer(20);

    }

    public function setButtonsTab2(){
        $delete = $this->getOnclickSubmit('viewbooking/cancel/'.$this->booking_id);

        $this->layout->footer[] = $this->uiKitButtonHollow('{#yes#}',[
            'onclick' => [$delete,
                //$this->getOnclickOpenAction('home')
            ]
        ],['background-color' => '#656565','color' => '#ffffff']);
        $this->layout->footer[] = $this->getComponentSpacer(20);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#no#}',[
            'onclick' => $this->getOnclickTab(1)
        ]);
        $this->layout->footer[] = $this->getComponentSpacer(20);
    }

    public function setButtonsTab2NoDelete(){
        $this->layout->footer[] = $this->getComponentSpacer(20);
        $this->layout->footer[] = $this->uiKitButtonFilled('{#back#}',[
            'onclick' => $this->getOnclickTab(1)
        ]);
        $this->layout->footer[] = $this->getComponentSpacer(20);
    }


    public function setHomeButton(){
        $this->layout->footer[] = $this->uiKitButtonHollow('{#home#}',[
            'onclick' => $this->getOnclickOpenAction('home')
        ],['background-color' => '#656565','color' => '#ffffff']);
        $this->layout->footer[] = $this->getComponentSpacer(20);
    }


    public function setBottom()
    {

        $length = isset($this->booking_data['booking']['length']) ? $this->booking_data['booking']['length'] : '';
        $price = $length / 1.5;
        $width = ($this->screen_width / 2) - 15;
        if(!$this->no_navi){
            $this->layout->footer[] = $this->getComponentSwipeAreaNavigation('#2930DD', '#ffffff');
        } else {
            $this->layout->footer[] = $this->getComponentSpacer(15);
        }

        $col[] = $this->getComponentText('{#total_cost#} ' . $price . '£', [], [
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'left',
            'font-weight' => 'bold',
            'font-size' => '16',
            'width' => $width,
            'margin' => '0 0 0 0'
        ]);

        if ($this->current_booth) {
            $booth = $this->current_booth;
        } elseif (isset($this->booking_data['booking']['booth'])) {
            $booth = $this->booking_data['booking']['booth'];
        } else {
            $booth = 0;
        }

        $btn[] = $this->getComponentRow([
            $this->getComponentText('{#continue#}', [], ['color' => $this->color_top_bar_text_color, 'font-size' => '16']),
            $this->getComponentImage('icon-nexudus-forward.png', [], ['width' => '25','margin' => '0 0 0 10'])
        ], ['onclick' => $this->getOnclickSubmit('Makebooking/step3/' . $booth)],
            ['text-align' => 'center',
                'background-color' => $this->color_top_bar_color,
                'padding' => '5 20 5 20',
                'border-radius' => '15'
            ]
        );

        $col[] = $this->getComponentRow($btn, [], ['text-align' => 'right', 'width' => $width]);

        $this->layout->footer[] = $this->getComponentRow($col, [], [
            'margin' => '0 15 15 15',
            'width' => '100%',
            'vertical-align' => 'middle']);


    }


    public function getDivs()
    {
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }

    public function setCalendar()
    {

        $booths = $this->getData('booths', 'array');
        $count = 0;
        $show_booking = true;
        $col = array();

        if (isset($booths['spaces'])) {
            foreach ($booths['spaces'] as $space) {
                if ($count < 2) {
                    if (!isset($this->booking_data['booking']['booth'])) {
                        $this->current_booth = $space['Id'];
                        $col[] = $this->getBooth($space, $show_booking);
                    } elseif ($this->booking_data['booking']['booth'] == $space['Id']) {
                        $col[] = $this->getBooth($space, true);
                    } else {
                        $col[] = $this->getBooth($space, false);
                    }
                    $show_booking = false;
                    $count++;
                } else {
                    $count = 0;
                    if (isset($this->booking_data['booking']['booth']) AND $this->booking_data['booking']['booth'] == $space['Id']) {
                        $col[] = $this->getBooth($space, true);
                    } else {
                        $col[] = $this->getBooth($space);
                    }
                    $swipe[] = $this->getComponentRow($col);
                    unset($col);
                }

            }
        }


        if (isset($swipe)) {
            if(count($swipe) < 4) {
                $this->no_navi = true;
                $this->layout->scroll[] = $this->getComponentColumn($swipe,['id' => 'bookingcal']);
            } else {
                $this->layout->scroll[] = $this->getComponentSwipe($swipe, ['preserve_position' => 1, 'id' => 'bookingcal']);
            }
        }


    }

    public function getBooth($space, $show_booking = false)
    {

        $booths = $this->getData('booths', 'array');
        $bookings = $booths['bookings'];

        $hour = isset($this->booking_data['booking']['hour']) ? $this->booking_data['booking']['hour'] : '';
        $minute = isset($this->booking_data['booking']['minute']) ? $this->booking_data['booking']['minute'] : '';
        $length = isset($this->booking_data['booking']['length']) ? $this->booking_data['booking']['length'] : '';

        if ($minute == 0) {
            $minute = '00';
        }

        $element_width = round($this->screen_width / 3, 0) - 30;

        $date = date('Y-m-d', $this->booking_data['booking']['date']);

        $begin = new \DateTime($date . ' 09:00');
        $end = new \DateTime($date . ' 19:00');

        $interval = new \DateInterval('PT15M');
        $daterange = new \DatePeriod($begin, $interval, $end);

        foreach ($daterange as $date) {
            $start = date_timestamp_get($date);
            $first = $date->format("G:i");
            $temp = $date;
            $temp->add(new \DateInterval('PT15M'));
            $second = $temp->format("G:i");
            $times[$first]['label'] = $first . ' - ' . $second;
            $times[$first]['start'] = $start;
        }

        $place = isset($space['Name']) ? $space['Name'] : '';

        $col[] = $this->getComponentText($place, [], [
            'font-size' => '11', 'color' => $this->color_text_color,
            'width' => $element_width, 'white-space' => 'nowrap'
        ]);
        $output[] = $this->getComponentRow($col, [], ['width' => $element_width, 'margin' => '5 15 5 15']);
        unset($col);

        $output[] = $this->getComponentDivider();
        $output[] = $this->getComponentSpacer(15);
        $counter = false;

        foreach ($times as $key => $time) {
            $placeid = $space['Id'];
            $real_time = $time['start'];

            if (isset($skip)) {
                $skip = $skip - 900;
                $output[] = $this->getComponentText($time['label'], [], ['parent_style' => 'nexus_timeblock_busy', 'width' => $element_width]);
                $counter = $counter - 15;

                if ($skip < 0) {
                    unset($skip);
                }
            } elseif (isset($bookings[$placeid][$real_time])) {
                if ($bookings[$placeid][$real_time] > 900) {
                    $skip = $bookings[$placeid][$real_time] / 900;
                }
                $output[] = $this->getComponentText($time['label'], [], ['parent_style' => 'nexus_timeblock_busy', 'width' => $element_width]);
                $counter = $counter - 15;
            } elseif ($counter > 0 AND $show_booking) {
                $output[] = $this->getComponentText($time['label'], [], ['parent_style' => 'nexus_timeblock_selected', 'width' => $element_width]);
                $counter = $counter - 15;
            } elseif ($key == $hour . ':' . $minute AND $show_booking) {
                if ($length > 15) {
                    $counter = $length - 15;
                }
                $output[] = $this->getComponentText($time['label'], [], ['parent_style' => 'nexus_timeblock_selected', 'width' => $element_width]);
            } else {
                $output[] = $this->getComponentText($time['label'], [
                    'onclick' => $this->getOnclickSubmit('makebooking/timechange/' . $key . '--' . $space['Id']),
                ], ['width' => $element_width, 'parent_style' => 'nexus_timeblock_free']);
            }
        }

        return $this->getComponentColumn($output);
    }

    public function setHeader()
    {

        //print_r($this->booking_data);die();

        $text = isset($this->booking_data['location']['Name']) ? str_replace('MyWorkBooth - ', '', $this->booking_data['location']['Name']) : '';

        $this->layout->header[] = $this->components->getBigNexudusHeader($text, [
            'onclick' => $this->getOnclickOpenAction('listbookings')
        ]);


        $place = isset($this->booking_data['booking']['ResourceName']) ? $this->booking_data['booking']['ResourceName'] : '';

        $col[] = $this->getComponentImage('icon-nexudus-calendar.png', [], ['width' => '24', 'margin' => '0 15 0 0']);

        $col[] = $this->getComponentText($place, [
            'style' => 'nexudus_uikit_formheader_small',
            //'onclick' => $this->showElement('place')
        ]);

        $col[] = $this->getTopSelectors();
        $this->setEditing($text);

        $this->layout->header[] = $this->getComponentRow($col, [], ['margin' => '5 15 5 15', 'vertical-align' => 'middle']);
        $this->layout->header[] = $this->getComponentDivider();
    }

    private function setEditing($text){
        $editrow[] = $this->getComponentText('{#booking_for#}: ' .$text,['style' => 'nexudus_regular_text']);
/*        $editrow[] = $this->getComponentImage('icon-nexudus-edit.png',[],['width' => '20','margin' => '5 0 0 0']);

        $editclick[] = $this->getOnclickSubmit('viewbooking/preparebooking/'.$this->booking_id,['sync_open' => 1]);
        $editclick[] = $this->getOnclickOpenAction('makebooking',false,[
            'id' => 'makebooking/step2/editing-'.$this->booking_id,'sync_open' => 1,'back_button' => 1
        ]);*/

        $this->layout->header[] = $this->getComponentRow($editrow,[
            //'onclick' => $editclick
        ],['text-align' => 'center']);

        $this->layout->header[] = $this->getComponentDivider();
    }


    public function showElement($name)
    {
        return [
            $this->getOnclickHideElement('*_element', ['transition' => 'none']),
            $this->getOnclickShowElement($name . '_element'),
            $this->getOnclickHideElement('*_selector_on', ['transition' => 'none']),
            $this->getOnclickShowElement('*_selector_off', ['transition' => 'none']),
            $this->getOnclickHideElement($name . '_selector_off', ['transition' => 'none']),
            $this->getOnclickShowElement($name . '_selector_on', ['transition' => 'none']),
        ];
    }

    public function hideElements()
    {
        return [
            $this->getOnclickHideElement('*_element'),
            $this->getOnclickHideElement('*_selector_on', ['transition' => 'none']),
            $this->getOnclickShowElement('*_selector_off', ['transition' => 'none', 'viewport' => 'top']),
        ];
    }

    public function getTopSelectors()
    {
        $starttime = isset($this->booking_data['booking']['FromTime']) ? $this->booking_data['booking']['FromTime'] : '';
        $starttime = strtotime($starttime);

        $endtime = isset($this->booking_data['booking']['ToTime']) ? $this->booking_data['booking']['ToTime'] : '';
        $endtime = strtotime($endtime);

        $length = ($endtime - $starttime)/60 .'min';

        $date = date('d.m.y',$starttime);
        $hour = date('G',$starttime);
        $minute = date('i',$starttime);

/*        $date = isset($this->booking_data['date']) ? date('d.m.y', $this->booking_data['date']) : '';
        $hour = isset($this->booking_data['hour']) ? $this->booking_data['hour'] : '';
        $minute = isset($this->booking_data['minute']) ? $this->booking_data['minute'] : '';
        $length = isset($this->booking_data['length']) ? $this->booking_data['length'] . 'm' : '';*/

        if ($minute == 0) {
            $minute = '00';
        }

        $time = $hour . ':' . $minute;

        $elements = ['date' => $date, 'time' => $time, 'length' => $length];

        foreach ($elements as $key => $val) {
            $row[] = $this->getComponentText($val, [
                'style' => 'nexudus_uikit_formheader_small',
                //'onclick' => $this->showElement($key)
            ]);

            $row[] = $this->getComponentText($val, [
                'style' => 'nexudus_uikit_formheader_selected_small',
                'visibility' => 'hidden',
                //'onclick' => $this->hideElements()
            ]);
        }


        return $this->getComponentRow($row, [], ['floating' => 1, 'float' => 'right']);


    }


    public function getBtns($selector_name)
    {

        $command = 'makebooking/step2/update_' . $selector_name;

        $row[] = $this->getComponentText('{#cancel#}', [
            'style' => 'nexudus_expanding_selector_button',
            'onclick' => $this->hideElements()
        ]);

        $row[] = $this->getComponentText('{#update#}', [
            'onclick' => $this->getOnclickSubmit($command),
            'style' => 'nexudus_expanding_selector_button'
        ]);

        $btns[] = $this->getComponentRow($row, [], [
            'floating' => '1', 'float' => 'right'
        ]);

        return $this->getComponentRow($btns, [], ['padding' => '5 15 5 5', 'background-color' => '#2930DD']);
    }


}
