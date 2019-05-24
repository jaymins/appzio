<?php

namespace packages\actionMnexudus\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Listbookings extends BootstrapView
{

    /* @var Components */
    public $components;
    public $theme;
    private $bookings;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->components->getBigNexudusHeader('{#my_bookings#}');

        $this->bookings = $this->getData('bookings', 'array');

        if ($this->bookings) {
            foreach ($this->bookings as $booking) {
                $this->layout->scroll[] = $this->ApiGetBookingRow($booking);
                $this->layout->scroll[] = $this->getComponentDivider();
            }
        } else {
            $this->layout->scroll[] = $this->getComponentText('No bookings yet', ['style' => 'nexudus_uikit_formheader']);
        }


        return $this->layout;
    }

    public function ApiGetBookingRow($booking){
        $first_row[] = $this->getComponentImage('icon-nexudus-calendar.png', [], ['width' => '24', 'margin' => '0 5 0 15']);
        $first_row[] = $this->getComponentText($booking['resourceName'], ['style' => 'nexudus_uikit_row_text']);

        $firs_column[] = $this->getComponentRow($first_row, [], ['vertical-align' => 'middle', 'width' => '100%']);
        unset($col);

        $second_row[] = $this->getComponentVerticalSpacer('44');
        $second_row[] = $this->getComponentText($booking['date'], ['style' => 'nexudus_uikit_row_text']);
        $second_row[] = $this->getComponentText($booking['time'], ['style' => 'nexudus_uikit_row_text']);
        $second_row[] = $this->getComponentText($booking['length'].'min', ['style' => 'nexudus_uikit_row_text']);

        $firs_column[] = $this->getComponentRow($second_row, [], ['vertical-align' => 'middle', 'width' => '100%']);

        $final_row[] = $this->getComponentColumn($firs_column);

        if ($booking['full_date'] > time()) {
            $final_row[] = $this->getComponentText('{#upcoming#}', [], [
                'background-color' => '#7175FA', 'color' => '#ffffff', 'padding' => '8 10 8 10',
                'floating' => '1', 'float' => 'right'
            ]);
        } else {
            $final_row[] = $this->getComponentText('{#ended#}', [], [
                'background-color' => '#CAA3A3','color' => '#ffffff', 'padding' => '8 10 8 10',
                'floating' => '1', 'float' => 'right'
            ]);
        }

        return $this->getComponentRow($final_row,[
            'onclick' => $this->getOnclickOpenAction('viewbooking',false,[
                'id' => $booking['id'],
                'sync_open' => 1
            ])
        ]);
    }

    public function getDivs()
    {
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }


}
