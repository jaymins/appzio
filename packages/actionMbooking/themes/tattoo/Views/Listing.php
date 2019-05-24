<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMbooking\themes\tattoo\Views;

use packages\actionMbooking\Models\BookingModel;
use packages\actionMbooking\Views\Listing as View;

class Listing extends View
{
    protected $creatorRole = 'user';
    protected $assigneeRole = 'artist';

    public function tab1()
    {

        $this->layout = new \stdClass();
        $this->bookingDivs = new \stdClass();

        $this->model->rewriteActionField('subject', 'My Bookings');
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->layout->header[] = $this->getHeaderTopBlock();
        $this->layout->header[] = $this->setHeader(1);

        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getEmptyNotification('{#You_have_no_pending_bookings#}');
        }

        $this->getBookingBlocks($bookings);

        return $this->layout;
    }

    public function getEmptyNotification($notification)
    {
        return $this->getComponentRow(array(
            $this->getComponentText($notification, array(), array(
                'color' => '#000000'
            ))
        ), array(), array(
            'width' => '100%',
            'text-align' => 'center',
            'margin' => '50 0 0 0'
        ));
    }

    public function getBookingBlocks($bookings)
    {

        $rows = array_chunk($bookings, 2);

        foreach ($rows as $row_items) {

            $data = [];

            foreach ($row_items as $row_item) {
                $data[] = $this->getBookingItemBlock($row_item);
            }

            $this->layout->scroll[] = $this->getComponentRow($data, array(), array(
                'width' => '100%',
                'text-align' => ( count($data) > 1 ? 'center' : 'left' ),
                'margin' => '15 5 0 5',
            ));
        }

        return true;
    }

    public function getBookingItemBlock(BookingModel $booking)
    {
        $item = $booking->item;
        $images = $item->getImages();

        $userId = $this->model->playid == $booking->play_id ?
            $booking->assignee_play_id : $booking->play_id;

        $user = \AeplayVariable::getArrayOfPlayvariables($userId);

        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentImage($images->itempic, array(
                    'onclick' => $this->getOnclickOpenAction('singlebook', false, array(
                        'id' => $booking->id,
                        'back_button' => 1,
                        'sync_open' => 1
                    )),
                    'priority' => 9,
                ), array(
                    'width' => '150',
                    'height' => '150',
                    'border-width' => '10',
                    'border-color' => '#f5f6f5',
                    'crop' => 'yes',
                ))
            ), array(), array(
                'width' => '100%',
                'text-align' => 'center',
            )),
            $this->getComponentColumn($this->getBookingAuthorInfo($user, $item, $booking->date), array(), array('width' =>'100%'))
        ), array(), array(
            'width' => '50%',
            'text-align' => 'center',
        ));
    }

    public function getBookingAuthorInfo($user, $item, $bookingDate)
    {
        $bookingDateObj = new \DateTime("now", new \DateTimeZone('Europe/London'));
        $bookingDateObj->setTimestamp($bookingDate);
        $bookingDateObj->setTimezone(new \DateTimeZone($this->model->getSavedVariable('timezone_id')));

        $data = [
            $this->getComponentRow(array(
                $this->getComponentImage($user['profilepic'], array(
                    'priority' => 9,
                ), array(
                    'width' => '20',
                    'crop' => 'round',
                    'margin' => '0 5 0 0',
                )),
                $this->getComponentText($user['firstname'] . ' ' . $user['lastname'], array(), array(
                    'color' => '#787777',
                    'margin' => '0 0 0 0',
                )),
            ), array(), array(
                'width' => 'auto',
                'margin' => '5 0 0 0',
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(
                $this->getComponentText($item->name, array(), array(
                    'color' => '#000000',
                    'font-size' => '18',
                    'text-align' => 'center',
                    'width' => '100%'
                )),
            ), array(), array(
                'width' => 'auto',
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(
                $this->getComponentText($bookingDateObj->format('d-m-Y'), array(), array(
                    'color' => '#787777',
                    'font-size' => '17',
                    'text-align' => 'center',
                    'width' => '100%'
                )),
            ), array(), array(
                'width' => 'auto',
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(
                $this->getComponentText($bookingDateObj->format('H:i T'), array(), array(
                    'color' => '#f9bb32',
                    'font-size' => '21',
                    'text-align' => 'center',
                    'width' => '100%'
                ))
            ), array(), array(
                'width' => 'auto',
                'text-align' => 'center',
            ))
        ];

        return $data;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->getHeaderTopBlock();

        $this->layout->header[] = $this->setHeader(2);

        $this->model->rewriteActionField('subject', 'My Bookings');
        $this->model->rewriteActionConfigField('background_color', '#ffffff');


        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getEmptyNotification('{#You_have_no_confirmed_bookings#}');

        }

        $this->getBookingBlocks($bookings);

        return $this->layout;
    }

    public function tab3()
    {
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->getHeaderTopBlock();

        $this->layout->header[] = $this->setHeader(3);

        $this->model->rewriteActionField('subject', 'My Bookings');

        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getEmptyNotification('{#You_have_no_confirmed_bookings#}');
        }

        $this->getBookingBlocks($bookings);

        return $this->layout;
    }

    public function tab4()
    {
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->getHeaderTopBlock();

        $this->layout->header[] = $this->setHeader(4);

        $this->model->rewriteActionField('subject', 'My Bookings');

        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getEmptyNotification('{#You_have_no_passed_bookings#}');
        }

        $this->getBookingBlocks($bookings);

        return $this->layout;
    }

    protected function setHeader($activeTab)
    {
        $styles = array(
            "font-size" => "10",
            "padding" => "10 0 10 0",
            "color" => '#9c9b9b',
            "border-color" => '#dedede',
            "width" => "25%",
            "text-align" => 'center'
        );
        return $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#pending#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $activeTab === 1 ?? false,
            ),
            array(
                'text' => strtoupper('{#all_confirmed#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $activeTab === 2 ?? false
            ),
            array(
                'text' => strtoupper('{#todays#}'),
                'onclick' => $this->getOnclickTab(3),
                'active' => $activeTab === 3 ?? false

            ),
            array(
                'text' => strtoupper('{#passed#}'),
                'onclick' => $this->getOnclickTab(4),
                'active' => $activeTab === 4 ?? false,
            )
        ), array(), $styles);
    }

    public function getHeaderTopBlock()
    {
        return $this->getComponentRow(array(

            $this->getComponentImage('pexels-photo.jpeg', array(), array(
                'width' => '100%',
                'height' => '200',
                'crop' => 'yes',
            )),
            $this->getComponentText('MY BOOKINGS', array(), array(
                'color' => '#ffffff',
                'floating' => 1,
                'text-align' => 'center',
                'font-size' => '24',
                'vertical-align' => 'middle'
            )),
        ), array(
        ), array(
            'width' => '100%',
            'height' => '200',
        ));
    }

    protected function addDivsForBooking(BookingModel $booking)
    {
        $bookingDivName = 'booking-note-' . $booking->id;
        $this->bookingDivs->$bookingDivName = $this->components->bookingNoteDiv($booking->notes);
        $bookingChangeTimeDiv = 'change-booking-time-' . $booking->id;
        $this->bookingDivs->$bookingChangeTimeDiv = $this->components->bookingTimeDiv($booking->date, $booking->id);
    }

    public function getDivs()
    {
        return $this->bookingDivs;
    }

}