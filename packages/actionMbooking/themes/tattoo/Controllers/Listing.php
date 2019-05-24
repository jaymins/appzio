<?php

namespace packages\actionMbooking\themes\tattoo\Controllers;

use packages\actionMbooking\Controllers\Listing as Controller;
use packages\actionMbooking\themes\tattoo\Models\Model as ArticleModel;

class Listing extends Controller
{
    protected $creatorRole = 'user';
    protected $assigneeRole = 'artist';

    /* @var ArticleModel */
    public $model;

    public function __construct($obj){
        parent::__construct($obj);
        $this->model->updateTimezone();
    }

    public function actionDefault()
    {
        $this->model->setBackgroundColor();
//
//        var_dump($this->model->getSavedVariable('timezone_id'));
//        var_dump($this->model->getSavedVariable('offset_in_seconds'));

        $bookings = $this->getBookingsByRole();

        return ['Listing', compact('bookings')];
    }

    /**
     * Get list of bookings depending on the user's role.
     *
     * @return array|mixed
     */
    public function getBookingsByRole()
    {
        $role = $this->model->getSavedVariable('role');

        $status = $this->getStatusFromActiveTab();

        $bookings = array();
        $bookings =  $this->model->getBookingsByStatusAndTime($status, $role);
//        print_r($bookings);exit;
//        if ($role == $this->creatorRole) {
//            $bookings = $this->model->getBookingsByStatus($status);
//        } else if ($role == $this->assigneeRole) {
//            $bookings = $this->model->getAssignedBookingsByStatus($status);
//        }

        return $bookings;
    }

    /**
     * Bookings are fetched by status depending on the active tab.
     * This method checks the currently active tab and returns the string
     * which is used in the database for the current status.
     *
     * @return string
     */
    protected function getStatusFromActiveTab()
    {
        $confirmedBookingsTab = 2;
        $pendingBookingsTab = 1;
        $todayBookingTab = 3;
        $passedBookingTap = 4;

        $currentTab = $this->current_tab;

        $status = '';

        switch ($currentTab) {
            case $currentTab == $pendingBookingsTab:
                $status = 'pending';
                break;
            case $currentTab == $todayBookingTab:
                $status = 'todays';
                break;
            case $currentTab == $passedBookingTap:
                $status = 'passed';
                break;
            default:
                $status = 'confirmed';
                break;
        }

        return $status;
    }

}

