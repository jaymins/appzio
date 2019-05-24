<?php

namespace packages\actionMbooking\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMbooking\Views\View as ArticleView;
use packages\actionMbooking\Models\Model as ArticleModel;

class Listing extends BootstrapController
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

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();

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

        if ($role == $this->creatorRole) {
            $bookings = $this->model->getBookingsByStatus($status);
        } else if ($role == $this->assigneeRole) {
            $bookings = $this->model->getAssignedBookingsByStatus($status);
        }

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
        $confirmedBookingsTab = 1;
        $pendingBookingsTab = 2;

        $currentTab = $this->current_tab;

        $status = '';

        switch ($currentTab) {
            case $currentTab == $confirmedBookingsTab:
                $status = 'confirmed';
                break;
            case $currentTab == $pendingBookingsTab:
                $status = 'pending';
                break;
        }

        return $status;
    }

}
