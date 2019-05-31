<?php

namespace packages\actionDitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class Publiclisting extends BootstrapController
{
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

        $query = $this->model->getSubmittedVariableByName('searchterm');
        $page = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] : 1;

        $experts = array();

        if ($this->current_tab == 1) {

            // Experts in current country
            $experts = $this->model->getExpertsByCountryAndUnit($query, $page);

        } else if ($this->current_tab == 2) {

            // Experts by Business unit
            $experts = $this->model->getExpertsByUnit($query, $page);

        }

        return ['Publiclisting', array(
            'items' => $experts,
            'page' => $page,
            'current_tab' => (int)$this->current_tab,
            'filtering' => empty($query) ? false : true
        )];
    }

    public function actionEmail()
    {
        $email = $this->model->getSubmittedVariableByName('recipient_email');

        $body = "Dear First Choice Expert,<br><br>";
        $body .= "A user of the !MPROVE app requires your support - please fine the request below.<br><br>";

        $body .= $this->model->getSubmittedVariableByName('message') . "<br><br>";

        $body .= "This mail has been sent through the !MPROVE app - please refer back directly to ";
        $body .= $this->model->getSavedVariable('firstname')
            . ' ' . $this->model->getSavedVariable('lastname')
            . " &lt;" . $this->model->getSavedVariable('email') . "&gt;" . '.' . "<br><br>";

        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: dgf-firstchoice@dhl.com";

        $recipients = array($email);

        $sendCopy = $this->model->getSubmittedVariableByName('send_copy');

        if ($sendCopy) {
            $recipients[] = $this->model->getSavedVariable('email');
        }

        \Aenotification::addUserEmail(
            $this->playid,
            '!MPROVE - Your Expert Support',
            $body,
            $this->model->actionobj->game_id,
            implode(',', $recipients), array(
                'email_from' => $this->model->getSavedVariable('email')
            )
        );

        $this->no_output = true;
    }

}