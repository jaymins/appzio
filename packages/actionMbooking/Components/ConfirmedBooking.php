<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMbooking\Models\BookingModel;

trait ConfirmedBooking
{
    public function getConfirmedBooking(BookingModel $booking)
    {
        /** @var BootstrapComponent $this */
        $item = $booking->item;
        $images = $item->getImages();

        $userId = $this->model->playid == $booking->play_id ?
            $booking->assignee_play_id : $booking->play_id;

        $user = \AeplayVariable::getArrayOfPlayvariables($userId);

        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->bookingCardImage($images->itempic),
                $this->getComponentColumn(array(
                    $this->bookingCardInformation($booking->date, $item->price),
                    $this->getBookingCreatorInformation($user),
                    $this->getConfirmedBookingActions($booking->assignee_play_id, $user['phone'])
                ), array(
                    'style' => 'booking_card_body'
                ))
            )),
            $this->getConfirmedBookingButtons($booking->id)
        ), array(
            'style' => 'booking_card'
        ));
    }

    protected function getConfirmedBookingActions(int $assigneeId, $phone)
    {
        /** @var BootstrapComponent $this */

        if ($this->model->getSavedVariable('role') == 'artist') {
            return;
        }

        return $this->getComponentRow(array(
//            $this->getComponentImage('booking-location-icon.png', array(
//                'style' => 'booking_card_location_button',
//                'onclick' => $this->getOnclickOpenAction('bookinglocation', $this->model->getActionidByPermaname('bookinglocation'), array(
//                    'open_popup' => true,
//                    'id' => $assigneeId,
//                    'sync_open' => 1
//                ))
//            )),
            $this->getComponentImage('booking-phone-icon.png', array(
                'style' => 'booking_card_phone_button',
                'onclick' => $this->getOnclickOpenUrl('tel://' . $phone)
            ))
        ), array(
            'style' => 'booking_card_row_wrapper_bottom'
        ));
    }

    protected function getBookingCreatorInformation($user)
    {
        return $this->getComponentRow(array(
            $this->getComponentImage($user['profilepic'], array(), array(
                'width' => '20',
                'crop' => 'round'
            )),
            $this->getComponentText($user['firstname'] . ' ' . $user['lastname'], array(), array(
                'color' => '#dbdbdb',
                'font-size' => '14',
                'margin' => '0 0 0 5'
            ))
        ), array(), array(
            'margin' => '0 0 0 10'
        ));
    }

    /**
     * @param $bookingId
     * @return mixed
     */
    protected function getConfirmedBookingButtons($bookingId)
    {
        /** @var BootstrapComponent $this */

        $cancel = $this->getOnclickRoute('Controller/cancel/' . $bookingId, false);
        $cancel[] = $this->getOnclickSubmit('');

        return $this->getComponentRow(array(
            $this->getComponentText('{#cancel#}', array(
                'style' => 'booking_card_button_default_full_width',
                'onclick' => $cancel
            )),
        ), array(
            'style' => 'booking_card_body'
        ));
    }
}