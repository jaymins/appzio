<?php

namespace packages\actionMbooking\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMbooking\Models\BookingModel;

trait PendingBooking
{
    public function getPendingBooking(BookingModel $booking)
    {
        /** @var BootstrapComponent $this */
        $item = $booking->item;
        $images = $item->getImages();

        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->bookingCardImage($images->itempic),
                $this->getPendingBookingBody($booking, $item),
            )),
            $this->getPendingBookingButtons($booking->id)
        ), array(
            'style' => 'booking_card'
        ));
    }

    protected function getPendingBookingBody($booking, $item)
    {
        return $this->getComponentColumn(array(
            $this->bookingCardInformation($booking->date, $item->price),
            $this->getComponentDivider(),
            $this->getPendingBookingNote($booking->id)
        ), array(
            'style' => 'booking_card_body'
        ));
    }

    protected function getPendingBookingNote($bookingId)
    {
        $layout = new \stdClass();
        $layout->top = 100;
        $layout->right = 10;
        $layout->left = 10;

        return $this->getComponentRow(array(
            $this->getComponentText('{#proposed#}', array(
                'style' => 'booking_card_status'
            )),
            $this->getComponentText('{#view_note#}', array(
                'style' => 'booking_card_view_note_button',
                'onclick' => $this->getOnclickShowDiv('booking-note-' . $bookingId, array(
                    'tap_to_close' => 1,
                    'transition' => 'fade',
                    'background' => 'blur',
                    'layout' => $layout
                ))
            ))
        ), array(
            'style' => 'booking_card_row_wrapper_bottom'
        ));
    }

    /**
     * @param $bookingId
     * @return mixed
     */
    protected function getPendingBookingButtons($bookingId)
    {
        /** @var BootstrapComponent $this */

        $layout = new \stdClass();
        $layout->top = 25;
        $layout->right = 10;
        $layout->left = 10;

        if ($this->model->getSavedVariable('role') == 'user') {
            return $this->getComponentRow(array(
                $this->getComponentText('{#new_time#}', array(
                    'style' => 'booking_card_button_default_full_width',
                    'onclick' => $this->getOnclickShowDiv('change-booking-time-' . $bookingId, array(
                        'tap_to_close' => 1,
                        'transition' => 'fade',
                        'background' => 'blur',
                        'layout' => $layout
                    ))
                ))
            ), array(
                'booking_card_body'
            ));
        }

        $accept = $this->getOnclickRoute('Controller/accept/' . $bookingId, false);
        $accept[] = $this->getOnclickSubmit('');

        $decline = $this->getOnclickRoute('Controller/decline/' . $bookingId, false);
        $decline[] = $this->getOnclickSubmit('');

        return $this->getComponentRow(array(
            $this->getComponentText('{#accept#}', array(
                'style' => 'booking_card_button_primary',
                'onclick' => $accept
            )),
            $this->getComponentText('{#new_time#}', array(
                'style' => 'booking_card_button_default',
                'onclick' => $this->getOnclickShowDiv('change-booking-time-' . $bookingId, array(
                    'tap_to_close' => 1,
                    'transition' => 'fade',
                    'background' => 'blur',
                    'layout' => $layout
                ))
            )),
            $this->getComponentText('{#decline#}', array(
                'style' => 'booking_card_button_danger',
                'onclick' => $decline
            ))
        ), array(
            'style' => 'booking_card_body'
        ));
    }
}