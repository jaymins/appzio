<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMbooking\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMbooking\Models\Model as ArticleModel;
use packages\actionMbooking\Models\BookingModel;

class Listing extends BootstrapView
{
    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMbooking\Components\Components
     */
    public $components;
    public $theme;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * If a user has this role he will see only the bookings he has created
     * and will not be able to confirm or respond to bookings.
     * Must be defined in extending controllers depending on the roles in
     * the application.
     *
     * @var
     */
    protected $creatorRole;

    /**
     * If a user has this role he will see the bookings assigned to him and
     * he will be able to confirm or decline them.
     * Must be defined in extending controllers depending on the roles in
     * the application.
     *
     * @var
     */
    protected $assigneeRole;

    public $bookingDivs;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->bookingDivs = new \stdClass();

        $this->model->rewriteActionField('subject', 'Bookings');

        $this->layout->header[] = $this->setHeader(1);

        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_confirmed_bookings_yet#}', array(
                'style' => 'booking_information_text'
            ));
        }

        foreach ($bookings as $booking) {
            $this->layout->scroll[] = $this->components->getConfirmedBooking($booking);
        }

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $this->layout->header[] = $this->setHeader(2);

        $this->model->rewriteActionField('subject', 'Bookings');

        $bookings = $this->getData('bookings', 'array');

        if (empty($bookings)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_pending_bookings_at_the_moment#}', array(
                'style' => 'booking_information_text'
            ));
        }

        foreach ($bookings as $booking) {
            $this->layout->scroll[] = $this->components->getPendingBooking($booking);
            $this->addDivsForBooking($booking);
        }

        return $this->layout;
    }

    protected function setHeader($activeTab)
    {
        return $this->getComponentRow(array(
            $this->getComponentText(     '{#confirmed#}', array(
                'onclick' => $this->getOnclickTab(1),
                'style' => $activeTab == 1 ? 'booking_tab_active' : 'booking_tab'
            )),
            $this->getComponentText('{#pending#}', array(
                'onclick' => $this->getOnclickTab(2),
                'style' => $activeTab == 2 ? 'booking_tab_active' : 'booking_tab'
            ))
        ), array(
            'style' => 'booking_tabs'
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
