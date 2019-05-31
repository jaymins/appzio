<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\uiKit\Controllers;

use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionDitems\Views\View as ArticleView;
use SimpleEmailServiceMessage;

class Controller extends \packages\actionDitems\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $current_action = $this->model->getItemId();
        $searchterm = $this->model->getSubmittedVariableByName('searchterm');

        // TODO: This is here so the client could setup the initial users, who has access to the statistics
        if (!$this->model->getSavedVariable('can_view_statistics')) {
            $this->model->saveVariable('can_view_statistics', 'N/A');
        }

        if ($this->model->getSavedVariable('logged_in')) {
            $this->collectLocation(43200);
            $this->model->updateTimezone();
        }

        if ($current_action AND $current_action !== 'search') {
            return $this->actionShow();
        } else if ($current_action === 'search' AND !empty($searchterm)) {

            $items = $this->model->getAllItems(false, true, false, array(
                'note', 'visit', 'routine'
            ));

            return ['Search', compact('items')];
        }

        $performanceDialogs = count($this->model->getUserItemsByType('visit'));
        $notes = count($this->model->getUserItemsByType('note'));
        $routines = count($this->model->getRemindersByType());

        return ['Home', compact(
            'performanceDialogs',
            'notes',
            'routines'
        )];
    }

    public function actionDelete()
    {
        $this->no_output = 1;

        $itemId = $this->getMenuId();

        $this->model->deleteItem($itemId);
    }

    public function actionShow()
    {
        $itemId = $this->model->getItemId();
        $actionName = $this->model->actionobj->permaname;

        if ($actionName == 'viewvisit' || $actionName == 'addvisit') {
            $item = ItemModel::model()->with('tags', 'category_relations')->findByPk($itemId);
            $viewName = 'View';
        } else if ($actionName == 'viewnote' || $actionName == 'addnote') {
            $item = ItemModel::model()->findByPk($itemId);
            $viewName = 'Viewnote';
        }

        if (isset($item->id) AND isset($viewName)) {
            $this->model->sessionSet('item_id_' . $this->model->action_id, $item->id);
            return [$viewName, compact('item')];
        }

        return ['Home'];
    }

    /**
     * Used to send a visit email.
     */
    public function actionEmail()
    {
        $item = $this->model->getItem($this->model->getSavedVariable('last_stored_visit'));
        $body = $this->getVsisitEmailBody($item);
        $subject = '!MPROVE - Performance Dialog Observations - ' . $item->name . ' / ' . date('d M Y', $item->date_added);
        $this->sendItemEmail($body, $subject);
    }

    /**
     * Used to send a note email.
     */
    public function actionNoteEmail()
    {
        $item = $this->model->getItem($this->model->getSubmittedVariableByName('note_id'));

        $body = $this->getNoteEmailBody($item);

        $subject = '!MPROVE - Note - ' . $item->name . ' / ' . date('d M Y', $item->date_added);

        $this->sendItemEmail($body, $subject);
    }

    /**
     * Deals with sending item related emails.
     * It relies on being passed the body and the subject and does all other
     * scaffolding operations.
     *
     * @param string $body
     * @param string $subject
     */
    protected function sendItemEmail(string $body, string $subject)
    {
        $this->no_output = true;

        $this->model->sessionSet('emails', array());

        $recipients = $this->getEmailRecipients();

        // Don't try to send the email if no recipients
        if (empty($recipients)) {
            return false;
        }

        $sender = $this->model->getSavedVariable('email');

        $email = \Yii::app()->ses->email()
            ->addTo($recipients)
            ->setFrom($sender)
            ->setSubject($subject)
            ->setMessageFromString($body, $body);

        $email = $this->getEmailWithAttachments($email);

        $email->addReplyTo($sender)
            ->setReturnPath($sender)
            ->setSubjectCharset('ISO-8859-1')
            ->setMessageCharset('ISO-8859-1')
            ->send();
    }

    /**
     * Returns the visit email contents based on the item that
     * we are sending and it's saved categories.
     *
     * @param $item
     * @return string
     */
    protected function getVsisitEmailBody(ItemModel $item): string
    {
        $body = "Hello,<br><br>";
        $body .= "Please find below and/or attached observations and notes taken upon visiting the ";
        $body .= $item->name . " Performance Dialog on " . date('d M Y', $item->date_added) . '.' . "<br><br>";

        $body .= $this->model->getSubmittedVariableByName('message');
        $body .= '<br><br>';

        $categories = $this->model->getItemCategoryInformation($item->id);

        foreach ($categories as $category) {
            $body .= $category->name . ':';
            $body .= '<br>';
            $body .= $category->category_relations[0]->description;
            $body .= '<br><br>';
        }

        $body .= "This mail has been sent through the !MPROVE app - for any questions on the observations and notes, please refer back to ";
        $body .= $this->model->getSavedVariable('firstname')
            . ' ' . $this->model->getSavedVariable('lastname')
            . " &lt;" . $this->model->getSavedVariable('email') . "&gt;" . '.' . "<br><br>";

        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: dgf-firstchoice@dhl.com";

        return $body;
    }

    /**
     * Returns the note email body based on the name and
     * description of the passed item.
     *
     * @param $item
     * @return string
     */
    protected function getNoteEmailBody(ItemModel $item): string
    {
        $body = "Hello,<br><br>";
        $body .= "Please find below and/or attached observations and notes taken on " . date('d M Y', $item->date_added) . ".<br><br>";
        $body .= $this->model->getSubmittedVariableByName('message');
        $body .= "<br><br>";
        $body .= $item->description;
        $body .= "<br><br>";
        $body .= "This mail has been sent through the !MPROVE app - for any questions on the observations and notes, please refer back to ";
        $body .= $this->model->getSavedVariable('firstname')
            . ' ' . $this->model->getSavedVariable('lastname')
            . " &lt;" . $this->model->getSavedVariable('email') . "&gt;" . '.' . "<br><br>";

        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: dgf-firstchoice@dhl.com";

        return $body;
    }

    /**
     * Returns a list of mail recipients. They will contain the
     * email of the normal recipient and the sender's email if a
     * copy is desired.
     *
     * In case we want to add recipients for every email sent
     * we should add them here.
     *
     * This method is used both in visit and note email functionality.
     *
     * @return array
     */
    protected function getEmailRecipients(): array
    {
        $recipients = [];

        $emails = $this->model->getSubmittedVariableByName('recipient_email');

        if ($emails) {
            $emails = explode(',', $emails);

            foreach ($emails as $email) {

                if (!strpos($email, '@'))
                    continue;

                $recipients[] = trim($email);
            }
        }

        $sendCopy = $this->model->getSubmittedVariableByName('send_copy');

        if ($sendCopy) {
            $recipients[] = $this->model->getSavedVariable('email');
        }

        // Cleanup
        $recipients = array_filter($recipients);

        // Save the recipients for future use
        foreach ($recipients as $recipient) {
            $this->model->addToVariable('temp_emails', $recipient);
        }

        return $recipients;
    }

    /**
     * Adds attachments if any are passed to the email object.
     * Returns the full modified email message object with it's
     * attachments.
     *
     * @param $email
     * @return SimpleEmailServiceMessage
     */
    protected function getEmailWithAttachments(SimpleEmailServiceMessage $email): SimpleEmailServiceMessage
    {
        foreach ($this->model->getAllSubmittedVariablesByName() as $key => $value) {
            if (stristr($key, 'send_pic_')) {
                $fileName = str_replace('send_pic_', '', $key);
                $file = \Aeapifilelookup::model()->findByAttributes(array('cachefile' => $value));

                if (isset($file->original)) {
                    $emailWithAttachment = $email->addAttachmentFromFile($file->original, $this->getEmailAttachedImagePathOriginal($file->original));

                    if ($emailWithAttachment) {
                        $email = $emailWithAttachment;
                    }
                }
            }
        }

        return $email;
    }

    /**
     * Returns the full path to the images directory
     * for the current app.
     *
     * @param $imageName
     * @return string
     */
    protected function getEmailAttachedImagePath(string $imageName): string
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/documents/games/' . $this->model->appid . '/images/';
        return $path . $imageName;
    }

    protected function getEmailAttachedImagePathOriginal(string $imageName): string
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . '/documents/games/' . $this->model->appid . '/user_original_images/';
        return $path . $imageName;
    }

}