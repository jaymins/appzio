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

class Controller extends \packages\actionMbooking\Controllers\Controller {

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
        $itemId = null;
        $item = null;

        $itemId = $this->getMenuId();

        if(!empty($itemId) AND is_numeric($itemId)){
            $this->model->sessionSet('book_item_id', $itemId);
        }
        else{
            $itemId = $this->model->sessionGet('book_item_id');
        }

        if(!empty($itemId) AND is_numeric($itemId)) {
            $item = $this->model->getItem($itemId);
        }
        return ['Create', compact('item')];
    }

    public function actionSave()
    {
        $this->model->setBackgroundColor();

        $this->model->validateInput();

//        $artistData = $this->model->sessionGet('artistData');

//        $images = $artistData['images'];
//        $author = $artistData['author'];

        $data = array();

        if (!empty($this->model->validation_errors)) {
            return ['Create', $data];
        }

        $booking = $this->model->saveBooking();

        $this->model->sendNewBookingNotification($booking);

        return ['Create', ['saved' => true]];
    }

}
