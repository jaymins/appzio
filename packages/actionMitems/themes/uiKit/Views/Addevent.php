<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Components\Components as Components;
use packages\actionMitems\Views\Create as BootstrapView;

class Addevent extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    private $event_data;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $this->event_data = $this->getData('event_data', 'mixed');

        $default_title_params = [
            'onclick' => $this->getOnclickShowDiv('pick_default_routine', $this->getDivParams()),
            'divider' => true,
            'variable' => 'default_routine',
            'right_icon' => 'back-icon-v2.png',
        ];

        $custom_title_params = [
            'onclick' => $this->getOnclickShowDiv('add_routine_name', $this->getDivParams()),
            'divider' => false,
            'variable' => 'routine_name',
            'right_icon' => 'back-icon-v2.png',
        ];

        if (isset($this->event_data->name) AND in_array($this->event_data->name, $this->getDefaultEventsList())) {
            $default_title_params['value'] = $this->event_data->name;
        } else if (isset($this->event_data->name) AND !in_array($this->event_data->name, $this->getDefaultEventsList())) {
            $custom_title_params['value'] = $this->event_data->name;
        }

        $this->layout->scroll[] = $this->components->uiKitListitem($this->getPredefinedEventTitle(), [], $default_title_params);
        $this->layout->scroll[] = $this->components->uiKitListitem($this->getPredefinedEventTitle('{#add_custom_routine#}', true), [], $custom_title_params, [
            'padding' => '0 0 10 0'
        ]);

        $this->layout->scroll[] = $this->getComponentDivider();
        $this->layout->scroll[] = $this->getEventRecipients();
        $this->layout->scroll[] = $this->getComponentDivider();

        $this->layout->scroll[] = $this->uiKitHintedTextField('{#description#}', 'routine_description', 'textarea', [
            'style' => 'article-uikit-textarea',
            'value' => (isset($this->event_data->description) ? $this->event_data->description : ''),
        ]);

        // Disbled support for changing the event type upon editing
        if (!$this->event_data) {
            $this->layout->scroll[] = $this->getRecurringSwitcher('disabled');
            $this->layout->scroll[] = $this->getRecurringSwitcher('enabled');
        }

        $this->layout->scroll[] = $this->getEventHour();

        $this->layout->scroll[] = $this->getRecurringSection();

        $this->layout->scroll[] = $this->getEventDate();

        if ($this->event_data) {
            $this->layout->scroll[] = $this->getComponentFormFieldText($this->event_data->id, [
                'variable' => 'current_event_id',
                'visibility' => 'hidden',
            ], [
                'opacity' => '0',
                'height' => '1'
            ]);
        }

        $this->showValidationErrors();

        $button_title = $this->getData('button_title', 'string');
        $button_submit = $this->getData('submit_value', 'string');

        $this->layout->footer[] = $this->uiKitWideButton($button_title, [
            'onclick' => $this->getOnclickSubmit($button_submit),
        ]);

        return $this->layout;
    }

    private function getEventDate()
    {

        $title = '{#event_date#}';
        $params = [
            'onclick' => $this->getOnclickShowDiv('pick_event_date', $this->getDivParams()),
            'variable' => 'event_date',
            'left_icon' => 'uikit-icon-routines.png',
            'time_format' => 'm / d / Y',
        ];

        if (isset($this->event_data->reminders[0]->date) AND $this->event_data->reminders[0]->is_full_day) {
            $timestamp = $this->event_data->reminders[0]->date;
            $title = date('m / d / Y', $timestamp);

            $params['title'] = $title;
            $params['value'] = $timestamp;
        }

        return $this->getComponentColumn([
            $this->components->uiKitListitem($title, [], $params),
            $this->getComponentDivider([
                'style' => 'article-uikit-divider-thin'
            ]),
        ], [
            'id' => 'section-event-date',
            'visibility' => $this->getVisibilityStatus('visible', 'full_day'),
        ]);
    }

    private function getEventHour()
    {
        return $this->getComponentColumn([
            $this->getComponentRow([
                $this->getComponentImage('uikit-icon-clock.png', [], [
                    'width' => '25',
                    'margin' => '0 10 0 0',
                ]),
                $this->getComponentText($this->getEventSelectedHour('H', 'event_start_time_hour', 'hh'), [
                    'variable' => 'event_start_time_hour',
                    'value' => $this->getEventSelectedHour('H', 'event_start_time_hour')
                ], [
                    'margin' => '0 0 0 0',
                ]),
                $this->getComponentText(':', [], [
                    'padding' => '0 5 0 5',
                ]),
                $this->getComponentText($this->getEventSelectedHour('i', 'event_start_time_minutes', 'mm'), [
                    'variable' => 'event_start_time_minutes',
                    'value' => $this->getEventSelectedHour('i', 'event_start_time_minutes')
                ], [
                    'margin' => '0 0 0 0',
                ]),
            ], [
                'onclick' => $this->getOnclickShowDiv('pick_hour', $this->getDivParams()),
            ], [
                'width' => 'auto',
                'padding' => '10 15 10 15',
                'vertical-align' => 'middle',
            ]),
            $this->getComponentDivider([
                'style' => 'article-uikit-divider-thin'
            ]),
        ], [
            'id' => 'section-event-time',
        ]);
    }

    private function getRecurringSwitcher($status)
    {

        $onclick = [
            $this->getOnclickShowElement('recurring-enabled', ['transition' => 'none']),
            $this->getOnclickShowElement('section-recurring-routine', ['transition' => 'fade']),
            $this->getOnclickShowElement('recurring-weekly', ['transition' => 'none']),
            $this->getOnclickHideElement('recurring-disabled', ['transition' => 'none']),
            $this->getOnclickHideElement('section-event-date', ['transition' => 'none']),
        ];

        if ($status == 'enabled') {
            $onclick = [
                $this->getOnclickHideElement('recurring-enabled', ['transition' => 'none']),
                $this->getOnclickHideElement('section-recurring-routine', ['transition' => 'fade']),
                $this->getOnclickHideElement('recurring-weekly', ['transition' => 'fade']),
                // $this->getOnclickHideElement('recurring-monthly', ['transition' => 'none']),
                $this->getOnclickShowElement('recurring-disabled', ['transition' => 'none']),
                $this->getOnclickShowElement('section-event-date', ['transition' => 'none']),
            ];
        }

        // TO DO: Description of routines is not saved

        $output = $this->getComponentColumn([
            $this->getComponentRow([
                $this->getComponentText('{#recurring_event#}', [], [
                    'font-size' => '16',
                ]),
                $this->getComponentImage('switch-' . $status . '.png', [
                    'onclick' => $onclick
                ], [
                    'width' => 60,
                    'floating' => 1,
                    'float' => 'right',
                ]),
            ], [], [
                'margin' => '10 15 10 15',
                'vertical-align' => 'middle'
            ]),
            $this->getComponentDivider([
                'parent_style' => 'article-uikit-divider-thin'
            ]),
        ], [
            'id' => 'recurring-' . $status,
            'visibility' => ($status == 'enabled' ? 'hidden' : 'visible'),
        ]);

        return $output;
    }

    private function getRecurringSection()
    {

        $this->getWeekCheckboxes();

        $date_params = [
            'onclick' => $this->getOnclickShowDiv('pick_start_date', $this->getDivParams()),
            'variable' => 'recurring_start_date',
            'left_icon' => 'uikit-icon-routines.png',
            'time_format' => 'm / d / Y',
        ];

        if (isset($this->event_data->reminders[0]->date) AND $this->event_data->reminders[0]->recurring) {
            $timestamp = $this->event_data->reminders[0]->date;
            $title = date('m / d / Y', $timestamp);

            $date_params['title'] = $title;
            $date_params['value'] = $timestamp;
        }

        return $this->getComponentColumn([
            $this->components->uiKitListitem('{#start_date#}', [], $date_params),
            $this->getComponentDivider([
                'style' => 'article-uikit-divider-thin'
            ]),
            // Weekly options
            $this->getComponentColumn(array_merge(
                [
                    $this->getComponentRow([
                        $this->getComponentText('{#occurs_weekly#}', [], [
                            'padding' => '10 5 10 5',
                        ])
                    ], [], [
                        'width' => 'auto',
                        'text-align' => 'center',
                    ]),
                    $this->getComponentRow([
                        $this->getComponentText('{#every#}', [], [
                            'margin' => '0 0 0 0'
                        ]),
                        $this->getComponentColumn([
                            $this->getComponentFormFieldText($this->getWeeklyRecurringInterval(), [
                                'variable' => 'weekly_recurring_interval',
                                'maxlength' => 2,
                                'input_type' => 'number',
                                'style' => 'uikit_input_with_border',
                            ]),
                        ], [], [
                            'width' => '12%',
                            'margin' => '0 10 0 10',
                        ]),
                        $this->getComponentText('{#weeks_on#}', [], [
                            'margin' => '0 0 0 0'
                        ]),
                    ], [], [
                        'text-align' => 'center',
                    ]),
                ],
                $this->getWeekCheckboxes()
            ), [
                'id' => 'recurring-weekly',
            ]),
        ], [
            'id' => 'section-recurring-routine',
            'visibility' => $this->getVisibilityStatus('hidden', 'recurring'),
        ]);
    }

    private function getWeekCheckboxes()
    {

        $checkboxes = [];
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $days[] = strtolower(jddayofweek($i, 1));
        }

        $chunks = array_chunk($days, 2);

        $active_days = [];
        if (
            isset($this->event_data->reminders[0]->pattern->day_of_week) AND
            $active_days_data = $this->event_data->reminders[0]->pattern->day_of_week
        ) {
            $acd = explode(',', $active_days_data);
            foreach ($acd as $active_day) {
                $active_days[] = strtolower(jddayofweek($active_day - 1, 1));
            }
        }

        foreach ($chunks as $row) {

            $row_data = [];

            foreach ($row as $day) {
                $row_data[] = $this->getComponentColumn([
                    $this->getComponentRow([
                        $this->getComponentFormFieldOnoff([
                            'variable' => 'recurs_' . $day,
                            'value' => (in_array($day, $active_days) ? '1' : '0'),
                        ], [
                            'margin' => '0 10 0 0'
                        ]),
                        $this->getComponentText('{#' . $day . '#}', [], [
                            'font-size' => '14',
                            'color' => '#7b7b7b',
                        ]),
                    ])
                ], [], [
                    'width' => '50%',
                    'vertical-align' => 'middle'
                ]);
            }

            $checkboxes[] = $this->getComponentRow($row_data, [], [
                'width' => 'auto',
                'vertical-align' => 'middle',
                'padding' => '10 10 10 10',
            ]);
        }

        return $checkboxes;
    }

    private function showValidationErrors()
    {

        if (empty($this->model->validation_errors)) {
            return false;
        }

        $errors = [];

        foreach ($this->model->validation_errors as $error) {
            $errors[] = $this->getComponentText($error, [], [
                'color' => '#d40511',
                'text-align' => 'center',
                'font-size' => '13',
                'padding' => '4 10 4 10',
            ]);
        }

        $this->layout->footer[] = $this->getComponentColumn($errors, [], [
            'margin' => '0 0 10 0'
        ]);

        return true;
    }

    public function getDivs()
    {

        $divs['add_routine_name'] = $this->components->getNamePicker([
            'title' => '{#add_custom_routine#}',
            'button_label' => '{#select_that_routine#}',
            'variable' => 'routine_name',
        ]);

        $divs['pick_default_routine'] = $this->components->getPicker([
            'title' => '{#select_a_routine#}',
            'button_label' => '{#select_a_routine#}',
            'variable' => 'default_routine',
            'data' => $this->getData('event_titles', 'string'),
            'default' => $this->getPredefinedEventTitle('Review Raised Problems'),
        ]);

        $divs['pick_event_date'] = $this->components->getCalendarPicker([
            'title' => '{#select_start_date#}',
            'variable' => 'event_date',
            'show_random' => false,
        ]);

        $divs['pick_start_date'] = $this->components->getCalendarPicker([
            'title' => '{#start_date_of_your_routine#}',
            'variable' => 'recurring_start_date',
        ]);

        /*$divs['pick_end_date'] = $this->components->getCalendarPicker(array(
            'title' => '{#end_date_of_your_routine#}',
            'variable' => 'recurring_end_date',
        ));*/

        $divs['pick_hour'] = $this->components->getHourPicker([
            'title' => '{#time_of_your_routine#}',
            'var_hour' => 'event_start_time_hour',
            'var_minutes' => 'event_start_time_minutes',
        ]);

        return $divs;
    }

    private function getDivParams()
    {

        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        return [
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        ];
    }

    private function getEventSelectedHour($format, $variable, $default = false)
    {

        if (isset($this->event_data->reminders[0]->date)) {
            return date($format, $this->event_data->reminders[0]->date);
        }

        return $this->model->getSubmittedVariableByName($variable, $default);
    }

    private function getWeeklyRecurringInterval()
    {

        if (
            isset($this->event_data->reminders[0]->pattern->separation_count) AND
            $this->event_data->reminders[0]->pattern->separation_count != 0
        ) {
            return $this->event_data->reminders[0]->pattern->separation_count;
        }

        return '1';
    }

    private function getPredefinedEventTitle($default = '{#select_a_routine#}', $search_custom = false)
    {

        if (!isset($this->event_data->name)) {
            return $default;
        }

        $titles = $this->getDefaultEventsList();

        if (in_array($this->event_data->name, $titles) AND !$search_custom) {
            return $this->event_data->name;
        } elseif (!in_array($this->event_data->name, $titles) AND $this->event_data->name AND $search_custom) {
            return $this->event_data->name;
        }

        return $default;
    }

    private function getDefaultEventsList()
    {
        return array_values(
            array_unique(
                explode(';', $this->getData('event_titles', 'string'))
            )
        );
    }

    private function getVisibilityStatus($default_status, $type)
    {

        if (!$this->event_data) {
            return $default_status;
        }

        if ($type == 'recurring' AND $this->event_data->reminders[0]->recurring) {
            return 'visible';
        } else if ($type == 'full_day' AND $this->event_data->reminders[0]->recurring) {
            return 'hidden';
        } else if ($type == 'full_day' AND $this->event_data->reminders[0]->is_full_day) {
            return 'visible';
        }

        return 'hidden';
    }

    private function getEventRecipients()
    {
        $value = $this->model->getSubmittedVariableByName('routine_emails');

        return $this->getComponentFormFieldText($value, [
            'hint' => '{#invite_additional_colleagues#}',
            'variable' => 'routine_emails',
            'id' => 'component_id',
            'suggestions_update_method' => 'getemails',
            'suggestions' => [],
            'suggestions_placeholder' => $this->getComponentText('$value', [], [
                'font-size' => 15,
                'color' => '#333333',
                'background-color' => '#ffffff',
                'padding' => '12 10 12 10',
            ]),
            'token_placeholder' => $this->getComponentRow([
                $this->getComponentText('$value', [], [
                    'color' => '#000000',
                    'padding' => '3 5 3 5',
                ])
            ], [], [
                'background-color' => '#f6f6f6',
                'padding' => '3 3 3 3',
                'margin' => '3 0 3 0',
                'border-radius' => '5',
            ]),
        ], [
            'margin' => '0 15 0 15',
            'width' => 'auto',
            'font-size' => '16'
        ]);
    }

}