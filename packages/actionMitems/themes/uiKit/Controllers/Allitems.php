<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\uiKit\Views\View as ArticleView;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionMitems\Models\ItemRemindersModel as ItemRemindersModel;

class Allitems extends BootstrapController
{

	/* @var ArticleView */
	public $view;

    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point.
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();
	    
	    $reminders = $this->model->getSortedReminders();

        return ['Allitems', array(
            'reminders' => $reminders
        )];
    }

    function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }

	public function actionUpdate() {
		$this->no_output = 1;
		$id = $this->getMenuId();

		$reminders_model = new ItemRemindersModel();
		$reminders_model->markCompleted( $id );

		return true;
	}

}