<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\uiKit\Controllers;

use packages\actionDitems\Models\ItemRemindersModel;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionDitems\themes\uiKit\Views\View as ArticleView;

class Createvisit extends \packages\actionDitems\Controllers\Create
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
        if ($this->model->getSavedVariable('logged_in')) {
            $this->collectLocation(43200);
            $this->model->updateTimezone();
        }

        // dirty fix here
        $dateAdded = $this->model->getSubmittedVariableByName('date_added');
        if (!empty($dateAdded) && date('d m y', $dateAdded) != date('d m y')) {
            $this->model->saveVariable('temp_date', $dateAdded);
        }

        $this->model->setBackgroundColor();

        if ($this->getMenuId() == 'new') {
            $this->model->clearTemporaryData();
            $this->model->setDefaultTag();
        }

        // $this->model->clearImageVariables();

        // Not sure what is this?
        if ($this->model->getSavedVariable('last_stored_visit')) {
            $this->model->deleteVariable('last_stored_visit');
        }

        // Make sure that the tags persists across the views, even if they haven't been saved
        $this->setupSubmittedTags();

        if ($this->model->getSavedVariable('temp_preset')) {
            $this->viewData['presetData'] = json_decode(
                $this->model->getSavedVariable('temp_preset', []),
                true
            );
        }

        return ['Create', $this->viewData];
    }

    /**
     * Save the form's data upon changing tabs
     * Triggers on Tab 1
     */
    public function actionSaveData()
    {
        $this->no_output = 1;

        $variables = $this->model->getAllSubmittedVariablesByName();
        $this->model->saveVariable('temp_preset', $this->model->fillPresetData($variables));

        return true;
    }

    /**
     * Save the form's data upon changing tabs
     * Triggers on Tab 2
     */
    public function actionSaveCategories()
    {
        $this->no_output = 1;

        $categories = $this->model->getAllSubmittedVariablesByName();
        $this->model->sessionSet('categories', $categories);

        return true;
    }

    public function actionClearImage()
    {
        $this->no_output = true;

        $variable = $this->getMenuId();

        $this->model->deleteVariable($variable);
    }

    public function actionSaveVisit()
    {
        $itemId = null;

        if (strstr($this->getMenuId(), 'edit_')) {
            $itemId = str_replace('edit_', '', $this->getMenuId());
        }

        $this->model->validateInput();

        if ($this->current_tab == 1 AND !$this->model->getSubmittedVariableByName('name') AND !$itemId) {
            $this->model->validation_errors['name'] = '{#the_name_field_is_required#}';
        }

        if (!empty($this->model->validation_errors)) {
            // prefill data to be shown in the form
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return ['Create', [
                'presetData' => $presetData,
                'errors' => $this->model->validation_errors,
            ]];
        }

        // Override the submitvariables so they contain the right data
        $submittedVariables = $this->model->getAllSubmittedVariablesByName();

        if ($this->model->getSavedVariable('temp_preset')) {
            $data_tab_1 = json_decode($this->model->getSavedVariable('temp_preset'), true);
            $submittedVariables = array_merge($data_tab_1, $submittedVariables);

            foreach ($submittedVariables as $submit_key => $submit_value) {
                $this->model->submitvariables[$submit_key] = $submit_value;
            }
        }

        $this->model->editId = $itemId;

        $visit = $this->model->saveItem(
            $this->model->getSubmittedImages(),
            $this->model->getSubmittedTags(),
            'visit'
        );

        if ($visit == null) {
            $this->no_output = 1;
            return true;
        }

        $this->model->clearTemporaryData();

        $this->model->saveVariable('last_stored_visit', $visit->id);
        $this->model->sessionSet('item_id_' . $this->model->action_id, $visit->id);

        return ['Redirect', [
            'action' => 'viewvisit',
            'item_id' => $visit->id,
            'tab_to_open' => '3',
        ]];
    }

    public function actionCreateReminder()
    {
        $filename = '';
        $calendar_sync = $this->model->getSubmittedVariableByName('sync_to_calendar');
        $send_invite = $this->model->getSubmittedVariableByName('send_outlook_invite');

        if (!$calendar_sync) {
            $this->no_output = 1;
        }

        $date = gmdate('d M Y', $this->model->getSubmittedVariableByName('date'));
        $date = $date . ' ' .
            $this->model->getSubmittedVariableByName('hour') . ':' .
            $this->model->getSubmittedVariableByName('minutes');

        $starting_stamp = $this->getConvertedStamp($date);

        $reminder = new ItemRemindersModel();
        $reminder->name = $this->model->getSubmittedVariableByName('title');
        $reminder->message = $this->model->getSubmittedVariableByName('message');
        $reminder->date = $starting_stamp;
        $reminder->end_date = $starting_stamp + 86400;
        $reminder->item_id = $this->model->getSavedVariable('last_stored_visit');
        $reminder->is_full_day = 1;
        $reminder->type = 'reminder';
        $reminder->save();

        // Generate the calendar file
        if ($calendar_sync OR $send_invite) {

            if ($reminder->item->type == 'note') {
                $location = $reminder->name;
                $description = $this->model->getSubmittedVariableByName('message');
                $filename = $this->generateCalendarFile('Reminder - ' . $reminder->item->name, $location, 'Note Reminder', $starting_stamp, $description);
            } else {
                $location = $reminder->item->name . ' - ' . $reminder->name;
                $filename = $this->generateCalendarFile('PD Follow-Up', $location, 'PD Follow Up', $starting_stamp);
            }

        }

        if ($send_invite) {

            $path = $this->getCalendarsPath();

            if ($reminder->item->type == 'note') {
                $email_subject = '!MPROVE - Note Reminder - ' . $reminder->item->name . ' / ' . date('d M Y', $starting_stamp);
                $email_body = $this->getEmailDescription($reminder->item->name, $starting_stamp, 'reminder', 'on');
            } else {
                $email_subject = '!MPROVE - Performance Dialog Follow-Up - ' . $reminder->item->name . ' / ' . date('d M Y', $starting_stamp);
                $email_body = $this->getEmailDescription($reminder->item->name, $starting_stamp, 'Performance Dialog Follow-Up');
            }

            \Aenotification::addUserEmail(
                $this->playid,
                $email_subject,
                $email_body,
                $this->model->actionobj->game_id,
                $this->model->getSavedVariable('email'),
                array(
                    'file1' => $path . $filename,
                )
            );

        }

        if ($calendar_sync) {
            $path = $this->getCalendarsPath(false);
            $file = \Yii::app()->params['siteURLssl'] . '/' . $path . $filename;

            return ['Download', compact('file')];
        }

    }

    public function actionCreateNextVisitReminder()
    {
        $filename = '';
        $calendar_sync = $this->model->getSubmittedVariableByName('sync_to_calendar');
        $send_invite = $this->model->getSubmittedVariableByName('send_outlook_invite');

        if (!$calendar_sync) {
            $this->no_output = 1;
        }

        $date = date('d M Y', $this->model->getSubmittedVariableByName('date'));
        $date = $date . ' ' .
            $this->model->getSubmittedVariableByName('hour') . ':' .
            $this->model->getSubmittedVariableByName('minutes');

        $starting_stamp = $this->getConvertedStamp($date);

        $reminder = new ItemRemindersModel();
        $reminder->date = $starting_stamp;
        $reminder->item_id = $this->model->getSavedVariable('last_stored_visit');
        $reminder->type = 'next_visit';
        $reminder->save();

        // Generate the calendar file
        if ($calendar_sync OR $send_invite) {
            $location = $reminder->item->name;
            $filename = $this->generateCalendarFile('PD Visit', $location, 'PD Next Visit', $starting_stamp);
        }

        if ($send_invite) {

            $path = $this->getCalendarsPath();

            \Aenotification::addUserEmail(
                $this->playid,
                '!MPROVE - Performance Dialog Next Visit - ' . $reminder->item->name . ' / ' . date('d M Y', $starting_stamp),
                $this->getEmailDescription($reminder->item->name, $starting_stamp, 'next Performance Dialog visit'),
                $this->model->actionobj->game_id,
                $this->model->getSavedVariable('email'),
                array(
                    'file1' => $path . $filename,
                )
            );

        }

        if ($calendar_sync) {
            $path = $this->getCalendarsPath(false);
            $file = \Yii::app()->params['siteURLssl'] . '/' . $path . $filename;

            return ['Download', compact('file')];
        }
    }

    public function getEmailDescription($title, $date, $type, $prefix = 'with')
    {
        $name = $this->model->getSavedVariable('firstname');
        $date = date('d M Y', $date);

        $body = "Dear {$name},<br><br>";

        $suffix = ($prefix == 'on' ? '/' : 'on');

        $body .= "please find attached your calendar entry for your {$type} {$prefix} {$title} {$suffix} {$date}.<br><br>";

        $body .= "To import it into your Outlook calendar, please open the attached ICS file and click \"Save & Close\".<br><br>";
        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: dgf-firstchoice@dhl.com";

        return $body;
    }

    public function actionDeleteReminder()
    {
        $this->no_output = 1;

        $id = $this->getMenuId();

        ItemRemindersModel::model()->deleteByPk($id);
    }

    public function actionDeleteTag()
    {
        $this->model->removeTag();
        $this->no_output = 1;
        return true;
    }

    public function generateCalendarFile($title, $location, $file_title, $start_time, $description = false)
    {
        $email_to = $this->model->getSavedVariable('email');

        if ($offset = $this->model->getSavedVariable('offset_in_seconds')) {
            $start_time = $start_time - $offset;
        }

        $end_time = $start_time + 3600;

        $parameters = array(
            'starttime' => $start_time,
            'endtime' => $end_time,
            'organizer' => $this->model->getSavedVariable('real_name'),
            'organizer_email' => $email_to,
            'subject' => $title,
            'description' => ($description ? $description : $this->model->getSubmittedVariableByName('message')),
            'location' => $location,
        );

        $path = $this->getCalendarsPath();
        $template = $this->model->getCalendarTemplate($parameters);
        $filename = $file_title . ' - ' . time() . '.ics';

        file_put_contents($path . $filename, $template);

        return $filename;
    }

    public function getCalendarsPath($full_path = true)
    {
        $calpath = 'documents/games/' . $this->model->appid . '/calendars/';
        $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . $calpath;

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        if ($full_path) {
            return $path;
        } else {
            return $calpath;
        }
    }

    private function getConvertedStamp($date)
    {
        $current_time = strtotime($date);
        $gmt_time = gmdate('d.m.Y H:i', $current_time);
        return strtotime($gmt_time . ' UTC');
    }

    private function setupSubmittedTags()
    {
        $submitted_data = @json_decode($this->model->getSavedVariable('temp_preset'), true);

        if (empty($submitted_data)) {
            return false;
        }

        if (isset($submitted_data['item_tags']) AND $submitted_data['item_tags']) {
            $this->viewData['presetData']['item_tags'] = $submitted_data['item_tags'];
        }

        return true;
    }

}