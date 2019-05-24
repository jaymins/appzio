<?php

namespace packages\actionMnexudus\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Makebooking2 extends Makebooking
{

    /* @var Components */
    public $components;
    public $theme;
    public $booking_data;
    public $current_booth;
    private $no_navi;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->booking_data = $this->getData('booking_data', 'array');

        $this->setHeader();

        if (isset($this->model->validation_errors['booking'])) {
            $this->layout->header[] = $this->getComponentText($this->model->validation_errors['booking'], [
                'style' => 'nexudus_error_header'
            ], [

            ]);
        }

        /* these are the hidden selectors */
        $selectors = [
            'time' => $this->getTimeSelectors(),
            'date' => $this->getCalendarSelector(),
            'length' => $this->getLengthSelector(),
            'place' => $this->getPlaceSelector()
        ];

        foreach ($selectors as $key => $val) {
            $output[] = $val;
            $output[] = $this->getBtns($key);
            $this->layout->scroll[] = $this->getComponentColumn($output, [
                'id' => $key . '_element',
                'visibility' => 'hidden'
            ]);
            unset($output);
        }


        $this->setCalendar();
        $this->setBottom();
        return $this->layout;
    }


    public function setBottom()
    {

        $length = isset($this->booking_data['length']) ? $this->booking_data['length'] : '';
        $price = $length / 1.5;
        $width = ($this->screen_width / 2) - 15;
        if(!$this->no_navi){
            $this->layout->footer[] = $this->getComponentSwipeAreaNavigation('#2930DD', '#ffffff');
        } else {
            $this->layout->footer[] = $this->getComponentSpacer(15);
        }

        if(isset($this->booking_data['edit_id'])) {
            $cost = '{#booking_already_paid#}';
        } else {
            $cost = '{#total_cost#} ' . $price . '£';
        }

            $col[] = $this->getComponentText($cost, [], [
            'color' => $this->color_top_bar_text_color,
            'text-align' => 'left',
            'font-weight' => 'bold',
            'font-size' => '16',
            'width' => $width,
            'margin' => '0 0 0 0'
        ]);

        if ($this->current_booth) {
            $booth = $this->current_booth;
        } elseif (isset($this->booking_data['booth'])) {
            $booth = $this->booking_data['booth'];
        } else {
            $booth = 0;
        }

        if(isset($this->booking_data['edit_id'])){
            $onclick = $this->getOnclickSubmit('Makebooking/editbooking/' . $booth);
            $txt = '{#update_booking#}';
        }elseif($this->model->getSavedVariable('stripe_card')){
            $onclick = $this->getOnclickSubmit('Makebooking/step3/' . $booth);
            $txt = '{#pay_booking_and_continue#}';
        } else {
            $onclick = $this->getOnclickStripeChooseCard();
            $txt = '{#payment_method#}';
        }

        $btn[] = $this->getComponentRow([
            $this->getComponentText($txt, [], ['color' => $this->color_top_bar_text_color, 'font-size' => '16']),
            $this->getComponentImage('icon-nexudus-forward.png', [], ['width' => '25','margin' => '0 0 0 10'])
        ], [
            'onclick' => $onclick
        ],
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
                    if (!isset($this->booking_data['booth'])) {
                        $this->current_booth = $space['Id'];
                        $col[] = $this->getBooth($space, $show_booking);
                    } elseif ($this->booking_data['booth'] == $space['Id']) {
                        $col[] = $this->getBooth($space, true);
                    } else {
                        $col[] = $this->getBooth($space, false);
                    }
                    $show_booking = false;
                    $count++;
                } else {
                    $count = 0;
                    if (isset($this->booking_data['booth']) AND $this->booking_data['booth'] == $space['Id']) {
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

        $hour = isset($this->booking_data['hour']) ? $this->booking_data['hour'] : '';
        $minute = isset($this->booking_data['minute']) ? $this->booking_data['minute'] : '';
        $length = isset($this->booking_data['length']) ? $this->booking_data['length'] : '';

        if ($minute == 0) {
            $minute = '00';
        }

        $element_width = round($this->screen_width / 3, 0) - 30;

        $date = date('Y-m-d', $this->booking_data['date']);

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
                    'onclick' => $this->getOnclickSubmit('makebooking/timechange/' . $key . '--' . $space['Id'],['viewport' => 'current']),
                ], ['width' => $element_width, 'parent_style' => 'nexus_timeblock_free']);
            }
        }

        return $this->getComponentColumn($output);
    }

    public function setHeader()
    {
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#choose_booth_and_time#}', [
            'onclick' => $this->getOnclickSubmit('Makebooking/reset/1')
        ]);

        $place = isset($this->booking_data['location_name']) ? $this->booking_data['location_name'] : '';

        $col[] = $this->getComponentImage('icon-nexudus-calendar.png', [], ['width' => '24', 'margin' => '0 15 0 0']);

        $col[] = $this->getComponentText($place, [
            'style' => 'nexudus_uikit_formheader_selector_small',
            'id' => 'place_selector_off',
            'onclick' => $this->showElement('place')
        ]);

        $col[] = $this->getComponentText($place, [
            'style' => 'nexudus_uikit_formheader_selected_small',
            'id' => 'place_selector_on',
            'visibility' => 'hidden',
            'onclick' => $this->hideElements()
        ]);

        $col[] = $this->getTopSelectors();

        $this->layout->header[] = $this->getComponentRow($col, [], ['margin' => '5 15 5 15', 'vertical-align' => 'middle']);
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
        $date = isset($this->booking_data['date']) ? date('d.m.y', $this->booking_data['date']) : '';
        $hour = isset($this->booking_data['hour']) ? $this->booking_data['hour'] : '';
        $minute = isset($this->booking_data['minute']) ? $this->booking_data['minute'] : '';
        $length = isset($this->booking_data['length']) ? $this->booking_data['length'] . 'min' : '';

        if ($minute == 0) {
            $minute = '00';
        }

        $time = $hour . ':' . $minute;

        $elements = ['date' => $date, 'time' => $time, 'length' => $length];

        foreach ($elements as $key => $val) {
            $row[] = $this->getComponentText($val, [
                'style' => 'nexudus_uikit_formheader_selector_small',
                'id' => $key . '_selector_off',
                'onclick' => $this->showElement($key)
            ]);

            $row[] = $this->getComponentText($val, [
                'style' => 'nexudus_uikit_formheader_selected_small',
                'id' => $key . '_selector_on',
                'visibility' => 'hidden',
                'onclick' => $this->hideElements()
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
