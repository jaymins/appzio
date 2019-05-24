<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMbooking\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMbooking\Models\BookingModel;
use packages\actionMbooking\themes\tattoo\Components\Components;

class View extends BootstrapView
{

    /* @var Components */
    public $components;
    public $theme;
    public $layout;

    public $model;
    public $bookingDivs;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->model->rewriteActionConfigField('background_color', '#ffffff');
        $booking = $this->getData('booking', 'object');
        $tattoo = $this->getData('tattoo', 'object');

        if ( is_object($booking) AND isset($booking->play_id) ) {
            $userId = $this->model->playid == $booking->play_id ?
                $booking->assignee_play_id : $booking->play_id;

            $user = \AeplayVariable::getArrayOfPlayvariables($userId);

            $this->renderHeader($user['firstname']);
            $images = $tattoo->getImages();
            $this->renderImageSlider($images);
            $this->layout->scroll[] = $this->getArtistRow($user, $booking->item);
            $this->renderTags($tattoo->tags);

            if (!empty($tattoo->category)) {
                $this->renderCategory($tattoo->category);
            } else {
                $this->renderCategories($tattoo->categories);
            }

            $this->layout->scroll[] = $this->getBookingDate($booking);
            $this->layout->scroll[] = $this->getBookingTime($booking);
            $this->layout->scroll[] = $this->getBookingLocation($tattoo);
            $this->layout->scroll[] = $this->getButtons($booking, $user);

            return $this->layout;
        }

        return $this->layout;
    }

    public function getButtons(BookingModel $booking, $user)
    {
        $buttons = array();
        if($booking->status == 'pending'){
            $bookingDateObj = new \DateTime("now", new \DateTimeZone('Europe/London'));
            $bookingDateObj->setTimestamp($booking->date);
            $bookingDateObj->setTimezone(new \DateTimeZone($this->model->getSavedVariable('timezone_id')));

            $bookingChangeTimeDiv = 'change-booking-time-' . $booking->id;
            $this->bookingDivs[$bookingChangeTimeDiv] = $this->components->bookingTimeDiv($bookingDateObj->getTimestamp(), $booking->id);
            $buttons[] = $this->getPendingBook($booking->id);
        }

        if($booking->status == 'confirmed'){
            if ($this->model->getSavedVariable('role') == 'user') {
                $buttons[] = $this->getConfirmedButtons($user);
            }
        }

        $now  = new \DateTime("now", new \DateTimeZone('Europe/London'));
        if (!($booking->date < $now->getTimestamp() && $booking->status == "confirmed")) {

            $buttons[] = $this->getComponentRow(array(
                $this->getComponentText('{#cancel#}', array(
                    'onclick' => array_merge(
                        $this->getOnclickRoute('Controller/cancel/' . $booking->id, false),
                        array(
                            $this->getOnclickOpenAction('bookingslist', false, array('sync_open' => 1))
                        )
                    )
                ), array(
                    "background-color" => "#C70039",
                    "color" => "#ffffff",
                    "border-radius" => "20",
                    "width" => "80%",
                    "padding" => "10 30 10 30",
                    "text-align" => "center"
                ))
            ), array(), array(
                "text-align" => "center",
                'margin' => '10 0 10 0'
            ));
        }

        return $this->getComponentColumn($buttons);
    }

    public function getDivs()
    {
        return $this->bookingDivs;
    }

    public function getConfirmedButtons($user)
    {
        return $this->getComponentRow(array(
            $this->getComponentText('Call to ' . $user['firstname'], array(
                'onclick' => $this->getOnclickOpenUrl('tel://' . $user['phone'])
            ), array(
                "background-color" => "#ffffff",
                "color" => "#656b6f",
                "border-radius" => "20",
                "border-color" => "#656b6f",
                "width" => "80%",
                "padding" => "10 30 10 30",
                "text-align" => "center"
            ))
        ), array(), array(
            "text-align" => "center",
            'margin' => '10 0 10 0'
        ));
    }

    public function getPendingBook($bookID)
    {
        $layout = new \stdClass();
        $layout->top = 25;
        $layout->right = 10;
        $layout->left = 10;

        if ($this->model->getSavedVariable('role') == 'user') {
            return $this->getComponentRow(array(
                $this->getComponentText('{#suggest_new_time#}', array(
                    'onclick' => $this->getOnclickShowDiv('change-booking-time-' . $bookID, array(
                            'tap_to_close' => 1,
                            'transition' => 'fade',
                            'background' => 'blur',
                            'layout' => $layout
                        )
                    )
                ), array(
                    "background-color" => "#fbe121",
                    "color" => "#333333",
                    "border-radius" => "20",
                    "width" => "80%",
                    "padding" => "10 30 10 30",
                    "text-align" => "center"
                ))
            ), array(), array(
                "text-align" => "center",
                'margin' => '10 0 10 0',
                'width' => 'auto'
            ));
        }
        $accept = $this->getOnclickRoute('Controller/accept/' . $bookID, false);
        $accept[] = $this->getOnclickSubmit('');

        return $this->getComponentRow(
            array(
                $this->getComponentText('Accept', array(
                    'onclick' => $accept
                ), array(
                    "background-color" => "#ffffff",
                    "color" => "#656b6f",
                    "border-radius" => "20",
                    "border-color" => "#656b6f",
                    "width" => "80%",
                    "padding" => "10 30 10 30",
                    "text-align" => "center"
                ))
            ),
            array(),
            array(
                'text-align' => 'center',
                'margin' => '10 0 10 0'
            )
        );
    }

    public function getBookingDate(BookingModel $bookingModel)
    {
        return $this->getComponentRow(array(
            $this->getComponentImage('calendar_black.png',array(), array(
                'width' => '20',
            )),
            $this->getComponentText(date('m/d/Y', $bookingModel->date), array(), array(
                'margin' => '0 0 0 10',
                'font-weight' => 'bold'
            ))
        ), array(), array(
            'margin' => '40 15 0 15'
        ));
    }

    public function getBookingTime(BookingModel $bookingModel)
    {
        $bookingDateObj = new \DateTime("now", new \DateTimeZone('Europe/London'));
        $bookingDateObj->setTimestamp($bookingModel->date);
        $bookingDateObj->setTimezone(new \DateTimeZone($this->model->getSavedVariable('timezone_id')));

        return $this->getComponentRow(array(
            $this->getComponentImage('clock_black.png',array(), array(
                'width' => '20',
            )),
            $this->getComponentText($bookingDateObj->format('H:i T'), array(), array(
                'margin' => '0 0 0 10',
                'font-weight' => 'bold'
            ))
        ), array(), array(
            'margin' => '10 15 10 15'
        ));
    }

    public function getBookingLocation($item)
    {
        $address = $this->model->coordinatesToAddress($item->lat, $item->lon);
        
        $fullAddress = isset($address['country']) ? $address['country'] : ' ';
        if(strlen($fullAddress))
            $fullAddress .= ', ';

        $fullAddress .= isset($address['city']) ? $address['city'] .', ' : ' ';
        $fullAddress .= isset($address['zip']) ? $address['zip'] .', ' : ' ';
        $fullAddress .= isset($address['street']) ? $address['street'] : ' ';

        if ( empty($address) ) {
            $fullAddress = 'N/A';
        }

        return $this->getComponentRow(array(
            $this->getComponentImage('maps-and-flags.png',array(), array(
                'width' => '20',
            )),
            $this->getComponentText($fullAddress, array(), array(
                'margin' => '0 0 0 10',
                'font-weight' => 'bold'
            ))
        ), array(), array(
            'margin' => '0 15 10 15'
        ));
    }

    public function renderCategory($category)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->components->getBookTattooItemCategory($category->name)
        ), array(), array(
            'margin' => '5 15 5 15'
        ));
    }

    public function renderCategories($categories)
    {
        $items = [];

        foreach ($categories as $category) {
            $items[] = $this->components->getBookTattooItemCategory($category->name);
        }

        $this->layout->scroll[] = $this->getComponentWrapRow($items, [], [
            'margin' => '5 15 5 15'
        ]);

        return true;
    }

    public function renderTags($tags)
    {
        $items = array();

        $maxLength = 25;
        $currentLength = 0;

        $maxPerRowCount = 3;
        $currentCount = 0;
        $isTitle = true;

        foreach ($tags as $tag) {
            if ($currentLength >= $maxLength || $currentCount > $maxPerRowCount) {
                $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
                    'padding' => '0 10 0 10'
                ));
                $items = array();
                $currentLength = 0;
                $currentCount = 0;
            }
            if($isTitle){
                $items[] = $this->components->getBookTattooItemTag('Tags:');
                $isTitle = false;
            }

            $items[] = $this->components->getBookTattooItemTag($tag->name);
            $currentLength += strlen($tag->name);
            $currentCount++;
        }

        $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
            'padding' => '0 10 0 10'
        ));
    }

    public function getArtistRow($item, $tattoo)
    {

        if ( !isset($item->play_id) OR empty($item->play_id) ) {
            return $this->components->uiKitDefaultHeader('{#error#}');
        }

        $profilpic = isset($item['profilepic']) ? $item['profilepic'] : 'icon_camera-grey.png';
        $firstname = isset($item['firstname']) ? $item['firstname'] : '{#anonymous#}';
        $lastname = isset($item['lastname']) ? $item['lastname'] : '{#anonymous#}';

        if($tattoo['price'] > 0){
            $price = $tattoo['price'] .' per hour';
        }
        else{
            $price = '{#ask_for_price#}';
        }

        $profileViewAction = $this->getOnclickOpenAction('profile', false,
            array(
                'id' => $item->play_id,
                'sync_open' => 1,
                'back_button' => 1,
            ),
            'Profile/default/' . $item->play_id
        );

        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentImage($profilpic, array(
                    'onclick' => $profileViewAction,
                    'priority' => 9,
                ), array(
                    'width' => '55',
                    'crop' => 'round',
                    'margin' => '0 10 0 0'
                )),
                $this->getComponentColumn(array(
                    $this->getComponentRow(array(
                        $this->getComponentText($firstname . ' ' . $lastname, array(), array(
                            'color' => '#000000',
                            'font-size' => '18',
                            'font-weight' => 'bold',
                        )),
                    ), array(), array(
                    )),
                    $this->getComponentRow(array(
                        $this->getComponentImage('american-dollar-symbol.png', array(), array(
                            'width' => '14',
                            'vertical-align' => 'middle'
                        )),
                        $this->getComponentText($price, array(), array(
                            'color' => '#000000',
                            'font-size' => '18',
                        )),
                    ), array(), array())
                ))
            ), array(), array(
                'padding' => '20 15 5 15',
                'vertical-align' => 'middle'
            )),
        ));
    }

    public function renderImageSlider($images)
    {
        if (empty($images) || is_null($images)) {
            return false;
        }

        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false, 'imgwidth' => 900, 'imgheight' => 720, 'imgcrop' => 'yes'));
        $height = round($this->screen_width / 1.25, 0);

        $image_styles['imgwidth'] = '900';
        $image_styles['imgheight'] = '720';
        $image_styles['width'] = $this->screen_width;
        $image_styles['height'] = $height;
        $image_styles['imgcrop'] = 'yes';
        $image_styles['not_to_assetlist'] = true;
        $image_styles['priority'] = '9';

        $navi_styles['margin'] = '-60 0 0 0';
        $navi_styles['text-align'] = 'center';

        $content = array();
        $current = 1;

        foreach ($images as $image) {
            $content[] = $this->getComponentColumn(array(
                $this->getComponentImage($image, array(
                    'priority' => 9,
                    'tap_to_open' => 1,
                    'image_fallback' => $fallback_image_path
                ), $image_styles),
                $scroll[] = $this->getComponentSwipeNavi(count((array)$images), $current, 'white', array(), $navi_styles)
            ));

            $current++;
        }

        $this->layout->scroll[] = $this->getComponentSwipe($content, array(), array(
            'background-color' => '#1d1d1d'
        ));
    }


    public function renderHeader($name)
    {
        $onclick = new \stdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->model->getActionidByPermaname('bookingslist');

        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentRow(array(
                $this->getComponentImage('back-arrow.png', array(
//                    'onclick' => $this->getOnclickGoHome()
                'onclick' => $onclick
                ), array(
                    'vertical-align' => 'middle',
                    'width' => '30',
                    'floating' => true,
                    'float' => 'left'
                ))
            ), array(), array(
//                'width' => $this->screen_width / 4,
            'width' => '10%'
            )),
            $this->getComponentRow(array(
                $this->getComponentText('{#book_with#} ' . $name , array(), array(
                    'vertical-align' => 'middle',
                    'color' => '#ffffff'
                ))
            ), array(), array(
//                'width' => $this->screen_width / 3.1,
                'width' => '80%',
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(),array(), array(
                'width' => '10%'
            ))
        ), array(), array(
            'vertical-align' => 'middle',
            'text-align' => 'center',
            'padding' => '10 0 10 0',
            'background-color' => '#29292c',
            'width' => '100%'
        ));
    }

}
