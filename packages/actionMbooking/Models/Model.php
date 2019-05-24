<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMbooking\Models;

use Bootstrap\Models\BootstrapModel;
use packages\actionMitems\Models\ItemModel;

class Model extends BootstrapModel
{

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;

    const STATUS_PENDING = 'pending';

    /**
     * Save a new booking entity in storage.
     *
     * @return BookingModel
     */
    public function saveBooking()
    {
        $itemId = $this->sessionGet('booking_item_id');
        $item = ItemModel::model()->findByPk($itemId);

        $booking = new BookingModel();
        $booking->play_id = $this->playid;
        $booking->item_id = $itemId;
        $booking->assignee_play_id = $item->play_id;
        $booking->date = $this->getBookingTimestamp();
        $booking->notes = $this->getSubmittedVariableByName('notes');
        $booking->status = self::STATUS_PENDING;
        $booking->price = $item->price;
        // TODO: add lon and lat from assignee variables
        $booking->save();

        return $booking;
    }

    /**
     * Get bookings created by the current user.
     * This will return all bookings for the given status with the booked item.
     *
     * @param $status
     * @return mixed
     */
    public function getBookingsByStatus($status)
    {
        return BookingModel::model()
            ->with('item')
            ->findAll(array(
                'order' => 't.id DESC',
                'condition' => 't.play_id = :playId AND t.status = :status',
                'params' => array(
                    ':playId' => $this->playid,
                    ':status' => $status
                )
            ));
    }

    /**
     * Get bookings created for items of the current user.
     * This will return all bookings assigned to the user with the booked item.
     *
     * @param $status
     * @return mixed
     */
    public function getAssignedBookingsByStatus($status)
    {
        return BookingModel::model()
            ->with('item')
            ->findAll(array(
                'order' => 't.id DESC',
                'condition' => 'assignee_play_id = :playId AND t.status = :status',
                'params' => array(
                    ':playId' => $this->playid,
                    ':status' => $status
                )
            ));
    }

    /**
     * Updates the given booking status.
     *
     * @param int $bookingId
     * @param string $status
     */
    public function updateBookingStatus(int $bookingId, string $status)
    {
        $booking = BookingModel::model()->findByPk($bookingId);
        $booking->status = $status;
        $booking->update();
    }

    public function updateBookingTime(int $bookingId)
    {
        $booking = BookingModel::model()->findByPk($bookingId);
        $booking->date = $this->getBookingTimestamp();
        $booking->update();
    }

    /**
     * Returns the booking timestamp created from the date
     * and the hour specified by the person creating it.
     *
     * @return false|int
     */
    protected function getBookingTimestamp()
    {
        $variables = $this->getAllSubmittedVariablesByName();

        $date = date('d M Y', $variables['date']);
        $time = $variables['hour'] . ':' . $variables['minutes'];
        $timestamp = strtotime($date . ' ' . $time);

        return $timestamp;
    }

    /**
     * Validate submitted input
     */
    public function validateInput()
    {
        $requiredVariables = array(
            'date',
            'hour',
            'notes'
        );

        foreach ($requiredVariables as $variable) {
            $value = $this->getSubmittedVariableByName($variable);
            if (empty($value)) {
                $this->validation_errors[$variable] = '{#this_field_is_required#}';
            }
        }
    }

    /**
     * Set the background color for the action
     *
     * @param $color
     */
    public function setBackgroundColor($color = '#343434')
    {
        $this->rewriteActionConfigField('background_color', $color);
    }


    /**
     * Send a notification to the artist that a new booking was placed.
     *
     * @param BookingModel $booking
     */
    public function sendNewBookingNotification(BookingModel $booking)
    {
        $subject = '{#new_booking#}';
        $message = 'Hey, new booking request just arrived. Confirm it asap.';
        $actionName = 'bookingslist';

        $this->sendBookingNotification($booking->assignee_play_id, $subject, $message);
    }

    /**
     * Send a notification that the given booking was cancelled.
     * Notification is sent to either the creator or the assignee
     * depending on who has cancelled the booking.
     *
     * @param int $bookingId
     */
    public function sendCancelledBookingNotification(int $bookingId)
    {
        $booking = BookingModel::model()->findByPk($bookingId);
        $notifiedUserId = $booking->play_id == $this->playid ?
            $booking->assignee_play_id : $booking->play_id;

        $subject = '{#booking_canceled#}';
        $message = 'Your booking with ' . $this->getSavedVariable('firstname') . ' ' . $this->getSavedVariable('lastname') . ' was cancelled.';
        $actionName = 'bookingslist';

        $this->sendBookingNotification($notifiedUserId, $subject, $message);
    }

    /**
     * Send a notification that the given booking's time was changed.
     * Notification is sent to either the creator or the assignee
     * depending on who changed the time.
     *
     * @param int $bookingId
     */
    public function sendChangedBookingNotification(int $bookingId)
    {
        $booking = BookingModel::model()->findByPk($bookingId);
        $notifiedUserId = $booking->play_id == $this->playid ?
            $booking->assignee_play_id : $booking->play_id;

        $subject = '{#time_changed#}';
        $message = 'New time was proposed for your booking. Confirm it asap.';
        $actionName = 'bookingslist';

        $this->sendBookingNotification($notifiedUserId, $subject, $message);
    }

    /**
     * Send booking notification.
     *
     * @param $userId
     * @param $subject
     * @param $message
     */
    public function sendBookingNotification($userId, $subject, $message)
    {
        $notifications = new \Aenotification();
        $notifications->id_channel = 1;
        $notifications->app_id = $this->appid;
        $notifications->play_id = $userId;
        $notifications->subject = $subject;
        $notifications->message = $message;
        $notifications->type = 'push';
        $notifications->action_id = $this->getActionidByPermaname('bookingslist');

        $menu1 = new \stdClass();
        $menu1->action = 'open-action';
        $menu1->action_config = $this->getActionidByPermaname('bookingslist');
        $menu1->sync_open = 1;

        $params = new \stdClass;
        $params->onopen = array($menu1);

        $notifications->parameters = json_encode($params);
        $notifications->insert();
    }
}
