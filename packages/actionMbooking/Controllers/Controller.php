<?php

namespace packages\actionMbooking\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMbooking\Models\BookingModel;
use packages\actionMbooking\Views\View as ArticleView;
use packages\actionMbooking\Models\Model as ArticleModel;

class Controller extends BootstrapController
{

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();

        $data = array();
        return ['Create', $data];
    }

    /**
     * Validate and save booking in storage.
     * Returns the create view with a flag used to close it.
     *
     * @return array
     */
    public function actionSave()
    {
        $this->model->setBackgroundColor();

        $this->model->validateInput();
        $data = array();

        if (!empty($this->model->validation_errors)) {
            return ['Create', $data];
        }

        $booking = $this->model->saveBooking();

        $this->model->sendNewBookingNotification($booking);

        return ['Create', ['saved' => true]];
    }

    /**
     * Action for accepting a booking.
     * Marks the given booking as confirmed and returns no output.
     */
    public function actionAccept()
    {
        $this->model->setBackgroundColor();

        $bookingId = $this->getMenuId();

        $this->model->updateBookingStatus($bookingId, 'confirmed');

        $this->no_output = true;
    }

    /**
     * Action for canceling a booking.
     * Marks the given booking as pending and returns no output.
     */
    public function actionCancel()
    {
        $this->model->setBackgroundColor();

        $bookingId = $this->getMenuId();

        $this->model->updateBookingStatus($bookingId, 'declined');

        $this->model->sendCancelledBookingNotification($bookingId);

        $this->no_output = true;
    }

    /**
     * Action for accepting a booking.
     * Marks the given booking as declined and returns no output.
     */
    public function actionDecline()
    {
        $this->model->setBackgroundColor();

        $bookingId = $this->getMenuId();

        $this->model->updateBookingStatus($bookingId, 'declined');

        $this->no_output = true;
    }

    /**
     * Action for changing a booking's time.
     * Updates the given booking's date timestamp with the new ones
     * submitted by the assignee.
     */
    public function actionChange()
    {
        $this->model->setBackgroundColor();

        $bookingId = $this->getMenuId();

        $this->model->updateBookingTime($bookingId);

        $this->no_output = true;
    }

}
