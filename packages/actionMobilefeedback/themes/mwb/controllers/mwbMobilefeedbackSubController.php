<?php

class mwbMobilefeedbackSubController extends MobilefeedbackController
{

    private $errors = [];

    public function tab1()
    {

        $this->data = new StdClass();

        $rq_fiedls = array(
            'first_name' => '{#first_name_is_required#}',
            'last_name' => '{#last_name_is_required#}',
            'preferred_email' => '{#email_is_required#}',
            'feedback-notes' => '{#please_enter_text_for_your_feedback#}',
        );

        if (isset($this->menuid) AND $this->menuid == 'submit-review') {

            foreach ($rq_fiedls as $field => $error_msg) {
                if (empty($this->submitvariables[$field])) {
                    $this->errors[$field] = $error_msg;
                }
            }

            if (empty($this->errors)) {
                $this->sendAdminNotifications();

                $this->data->scroll[] = $this->getRow([
                    $this->getColumn([
                        $this->getImage('nexudus-logo-2.png', [
                            'width' => '50',
                            'height' => 'auto'
                        ])
                    ], [
                        'width' => '100%',
                        'text-align' => 'center',
                        'vertical-align' => 'middle'
                    ]),
                ], [
                    'padding' => '20 0 10 0'
                ]);

                $this->data->scroll[] = $this->getText('{#thank_you_for_your_feedback#}!', [
                    'text-align' => 'center',
                    'font-size' => '20',
                    'color' => '#ffffff',
                    'padding' => '15 15 15 15',
                ]);

                $this->data->scroll[] = $this->getText('{#we_will_get_back_to_you_as_soon_as_possible#}', array(
                    'text-align' => 'center',
                    'color' => '#ffffff',
                    'margin' => '0 0 10 0'
                ));

                $this->data->footer[] = $this->getRow(array(
                    $this->getText('{#send_another_feedback#}', array(
                        'onclick' => $this->getOnclick('id', false, 'clear-review'),
                        'style' => 'mwb-feedback-button'
                    ))
                ), array('style' => 'mwb-feedback-button-container'));

                $this->data->footer[] = $this->getRow(array(
                    $this->getText('{#home#}', array(
                        'onclick' => $this->getOnclick('permaname', false, 'home'),
                        'style' => 'mwb-feedback-button'
                    ))
                ), array('style' => 'mwb-feedback-button-container'));

                return $this->data;
            }

        }

        $this->data->header[] = $this->setTopMenu();

        $this->data->scroll[] = $this->getText('{#please_send_us_feedback_or_question_using_the_form_below#}', array('style' => 'mwb-feedback-title'));

        $first_name = $this->getDefaultName('first');
        if ($this->getSubmittedVariableByName('first_name'))
            $first_name = $this->getSubmittedVariableByName('first_name');

        $this->data->scroll[] = $this->getFieldtext($first_name, array(
            'style' => 'mwb-feedback-field' . $this->getSuffix('first_name'),
            'hint' => '{#first_name#}',
            'variable' => 'first_name'
        ));

        $last_name = $this->getDefaultName('last');

        if ($this->getSubmittedVariableByName('last_name'))
            $last_name = $this->getSubmittedVariableByName('last_name');

        $preferred_email = '';
        if ($this->getSubmittedVariableByName('preferred_email'))
            $preferred_email = $this->getSubmittedVariableByName('preferred_email');

        $this->data->scroll[] = $this->getFieldtext($last_name, array(
            'style' => 'mwb-feedback-field' . $this->getSuffix('last_name'),
            'hint' => '{#last_name#}',
            'variable' => 'last_name'
        ));

        $this->data->scroll[] = $this->getFieldtext($preferred_email, array(
            'style' => 'mwb-feedback-field' . $this->getSuffix('preferred_email'),
            'hint' => '{#preferred_email#}',
            'variable' => 'preferred_email',
            'input_type' => 'email'
        ));

        $current_note = $this->getSubmittedVariableByName('feedback-notes');
        if (empty($current_note) OR $current_note == 'false')
            $current_note = '';

        $this->data->scroll[] = $this->getFieldtextarea($current_note, array(
            'style' => 'mwb-feedback-submit-review' . $this->getSuffix('feedback-notes'),
            'hint' => '{#type_your_message_here#}...',
            'variable' => 'feedback-notes'
        ));

        if ($this->errors) {

            foreach ($this->errors as $error) {
                $this->data->scroll[] = $this->getText($error, array('style' => 'mwb-feedback-error'));
            }

        }

        $this->data->footer[] = $this->getRow(array(
            $this->getText('{#send_feedback#}', array(
                'onclick' => $this->getOnclick('id', false, 'submit-review'),
                'style' => 'mwb-feedback-button'
            ))
        ), array('style' => 'mwb-feedback-button-container'));

        return $this->data;
    }

    protected function setTopMenu()
    {

        $width = ($this->screen_width / 2) - 70;

        $row[] = $this->getImage('icon-nexus-back.png',[
            'onclick' => $this->getOnclick('go-home')
        ],['width' => '30']);
        $row[] = $this->getText('',['width' => $width]);
        $row[] = $this->getImage('nexudus-logo-2.png',['width' => '45','floating' => '1','float' => 'right']);

        $col[] = $this->getRow($row,['vertical-align' => 'middle','margin' => '10 15 5 15']);

        $col[] = $this->getText('{#contact_us#}',[
            'color' => '#ffffff',
            'text-align' => 'center',
            'font-size' => '24',
            'margin' => '10 0 15 0'
        ]);

        return $this->getColumn($col,['background-color' => $this->color_topbar,'margin' => '0 0 0 0']);

    }

    protected function sendAdminNotifications()
    {

        $send_to = $this->configobj->feedback_email;
        $emails = explode(',', $send_to);

        $name = $this->getSubmittedVariableByName('first_name') . ' ' . $this->getSubmittedVariableByName('last_name');

        $send_to_email = $this->getSubmittedVariableByName(
            'preferred_email',
            $this->getSavedVariable('email'
            ));

        if (empty($emails)) {
            return false;
        }

        $notes_var = 'feedback-notes';

        $body = "Hi,<br><br>";
        $body .= "{#new_feedback_was_sent_by#} ";

        $body .= $name . ' ' .
            "&lt;" . $send_to_email . "&gt;" . '.' . "<br><br>";

        $body .= (isset($this->submitvariables[$notes_var]) ? $this->submitvariables[$notes_var] : $this->configobj->feedback_body) . "<br><br>";
        $body .= "Kind regards<br>";

        Aenotification::addUserEmail($this->playid, '{#new_feedback_received#}', $body, $this->gid, $emails[0], array());

        return true;
    }

    private function getSuffix($var)
    {

        if (isset($this->errors[$var])) {
            return '-error';
        }

        return '';
    }

    private function getDefaultName($type)
    {

        $current_name = $this->getSavedVariable('name');

        if (empty($current_name))
            return '';

        $pieces = explode(' ', $current_name);

        if (!isset($pieces[1]))
            return '';

        if ($type == 'first') {
            return $pieces[0];
        } else if ($type == 'last') {
            return $pieces[1];
        }

        return '';
    }

}