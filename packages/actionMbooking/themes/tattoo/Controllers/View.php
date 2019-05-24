<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMbooking\themes\tattoo\Controllers;

use packages\actionMbooking\Models\BookingModel;
use packages\actionMbooking\themes\tattoo\Views\Main;
use packages\actionMbooking\themes\tattoo\Views\View as ArticleView;
use packages\actionMbooking\themes\tattoo\Models\Model as ArticleModel;

class View extends \packages\actionMbooking\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){

        parent::__construct($obj);
        $this->model->updateTimezone();
    }


    public function actionDefault()
    {
//        $this->model->flushActionRoutes();
        $booking = null;
        $tattoo = null;
        $bookingId = null;

        $bookingId = $this->getMenuId();
//        $bookingId = 21;
        if(!empty($bookingId) AND is_numeric($bookingId)){
            $this->model->sessionSet('book_view_id', $bookingId);
        }
        else{
            $bookingId = $this->model->sessionGet('book_view_id');
        }

        if(!empty($bookingId) AND is_numeric($bookingId)) {
            $booking = BookingModel::model()->findByPk($bookingId);
            $tattoo = $booking->item;
//            print_r($this->model->coordinatesToAddress($booking->lat, $booking->lon));exit;
        }

        return ['View', compact('booking', 'tattoo')];
    }

}
