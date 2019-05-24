<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMfitness\Models\Model as ArticleModel;
use packages\actionMfitness\Models\ProgramSelectionModel;

class Settings extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfitness\Components\Components
     */
    public $components;
    public $theme;

    /* @var ArticleModel */
    public $model;

    private $divs = [];

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout = new \stdClass();

        $programs = $this->getData('programs', 'array');
        $confirm_program_edit = $this->getData('confirm_program_edit', 'mixed');
        $this->layout->header[] = $this->components->themeSmallHeader('{#program_settings#}');

        if (empty($programs)) {
            $this->layout->scroll[] = $this->getComponentText('{#you_dont_have_any_programs_yet#}.', [], [
                'color' => '#ffffff',
                'text-align' => 'center',
                'padding' => '25 20 15 20',
            ]);
            
            
            $this->layout->scroll[] = $this->components->themeButton(
                '{#add_a_program#}',
                $this->getOnclickOpenAction('categories'),
                'theme-icon-forward.png'
            );
            
            return $this->layout;
        }


        if ($confirm_program_edit) {
            $this->divs['confirm_program_edit'] = $this->divConfirmProgramEdit();
            $this->layout->onload[] = $this->getOnclickShowDiv(
                'confirm_program_edit',
                $this->components->themeDivOpenParams(),
                $this->components->themeDivOpenLayout()
            );
        }

        foreach ($programs as $program) {
            $this->getProgramSettings($program);
        }

        $this->renderValidationErrors();

        $this->layout->scroll[] = $this->components->themeButton(
            '{#save#}',
            $this->getOnclickSubmit('settings/storeSettings'),
            'theme-icon-forward.png',
            'black'
        );

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        return $this->layout;
    }

    private function getProgramSettings(ProgramSelectionModel $program)
    {

        $program_type = $program->program->category->name;

        if ($program_type == 'Nutrition') {
            $fields = $this->getData('food_field_types', 'array');
        } else {
            $fields = $this->getData('training_field_types', 'array');
        }

        $this->layout->scroll[] = $this->getComponentText('{#' . $program_type . '#}', ['style' => 'theme_big_header_left']);
        $this->layout->scroll[] = $this->getComponentText($program->program->name, [], [
            'font-size' => '16',
            'padding' => '0 20 0 20',
            'color' => '#dddddd',
        ]);

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        // Render a food selection field
        $this->getCurrentProgramFoodType($program);

        // Render a week selection field if certain conditions are met
        $this->getCurrentProgramWeek($program);

        foreach ($fields as $field => $settings) {
            if (!isset($program->{$field}) OR empty($program->{$field})) {
                continue;
            }

            $method_name = $settings['div_template'];
            $div_name = 'sw_program_' . $program->id . '|' . $field;

            switch ($settings['type']) {
                case 'int':
                    $this->divs[$div_name] = call_user_func_array([$this, $method_name], [$div_name, $field]);
                    $this->layout->scroll[] = $this->getProgramField($settings['label'], $field, $div_name, $program->{$field});
                    break;

                case 'json_single':
                    $db_data = json_decode($program->{$field}, true);
                    $this->divs[$div_name] = call_user_func_array([$this, $method_name], [$div_name, $field, $db_data]);
                    $this->layout->scroll[] = $this->getProgramField($settings['label'], $field, $div_name, $program->{$field});
                    break;

                case 'json_multiple':
                    $db_data = json_decode($program->{$field}, true);

                    foreach ($db_data as $i => $entry) {
                        $label = $settings['label'] . ' ' . ($i + 1);
                        $inner_div_name = $div_name . '_' . ($i + 1);

                        $this->divs[$inner_div_name] = call_user_func_array([$this, $method_name], [$inner_div_name, $field, $entry]);
                        $this->layout->scroll[] = $this->getProgramField($label, $field, $inner_div_name, $entry, 'swiss8-icon-clock.png');
                    }

                    break;
            }

        }

        // TODO: The field is not currently connected to the main logic as some client clarifications are required.
        if ($program_type == 'challenge') {
            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText('{#ongoing#}', [], [
                    'padding' => '0 0 0 5',
                    'font-size' => '16',
                    'color' => '#ffffff',
                ]),
                $this->getComponentFormFieldOnoff([
                    'type' => 'toggle',
                    'variable' => 'challenge_to_ongoing',
                    'value' => $this->model->getSavedVariable('challenge_to_ongoing', '0'),
                ], [
                    'floating' => 1,
                    'float' => 'right',
                    'color' => '#b71a2a',
                ])
            ], [], [
                'margin' => '10 0 0 0',
                'padding' => '0 20 0 20',
                'vertical-align' => 'middle',
            ]);
        }

        $onclick[] = $this->getOnclickSubmit('settings/stop/' . $program->id, ['async' => 1]);

        $onclick[] = $this->getOnclickShowDiv('delete_confirmation',
            $this->components->themeDivOpenParams(),
            $this->components->themeDivOpenLayout()
        );

        $this->layout->scroll[] = $this->components->themeButton('{#stop_this_program#}', $onclick,
            'theme-icon-big-red-close.png'
        );

        return true;
    }

    private function getCurrentProgramWeek(ProgramSelectionModel $program)
    {
        $category = $program->program->category->name;
        $program_type = $program->program->program_sub_type;

        if (
            ($category == 'Training' OR $category == 'Nutrition') AND
            $program->program_type != 'challenge' AND
            $program_type == 'weekly_based'
        ) {
            $div_name = 'sw_program_' . $program->id . '|current_program_week';
            $week = $this->model->getWeeksOffset($program->program_start_date, time());

            if ($tmp_value = $this->model->sessionGet($div_name)) {
                $week = $tmp_value;
            }

            $this->divs[$div_name] = $this->divWeekPicker($div_name);
            $this->layout->scroll[] = $this->getProgramField('{#current_program_week#}', '', $div_name, $week);
        }

        return true;
    }

    private function getCurrentProgramFoodType(ProgramSelectionModel $program)
    {
        $category = $program->program->category->name;

        if ($category != 'Nutrition') {
            return false;
        }

        $div_name = 'sw_program_' . $program->id . '|current_program_food_type';
        $food_name = (isset($program->program->subcategory->name) ? $program->program->subcategory->name : 'N/A');

        $this->divs[$div_name] = $this->divFoodPicker($div_name);
        $this->layout->scroll[] = $this->getProgramField('{#food_type#}', '', $div_name, $food_name);

        return true;
    }

    private function getProgramField(string $label, string $field, string $div_name, $value, $icon = false)
    {
        $out[] = $this->getComponentText('', ['style' => 'theme_field_shader']);
        $out[] = $this->getComponentText('', ['style' => 'theme_field_shader2']);
        $out[] = $this->getComponentRow([
            $this->getComponentText($label, [
                'style' => 'theme_text_in_form_field'
            ]),
            $this->getComponentText($this->getProgramFieldValue($value, $div_name), [
                // 'variable' => $this->TFFvariable
            ], [
                'color' => '#ffffff',
                'font-size' => '12',
                'floating' => 1,
                'float' => 'right',
                'margin' => '0 35 0 0'
            ]),
            $this->getProgramFieldRightIcon($icon)
        ], [
            'style' => 'theme_field',
            'onclick' => $this->getOnclickShowDiv(
                $div_name,
                $this->components->themeDivOpenParams(),
                $this->components->themeDivOpenLayout()
            )
        ]);

        return $this->getComponentColumn($out, [], [
            'vertical-align' => 'middle',
        ]);
    }

    private function getProgramFieldRightIcon($icon)
    {
        $src = 'theme-icon-down.png';

        if ($icon) {
            $src = $icon;
        }

        return $this->getComponentImage($src, [
            'priority' => '1'
        ], [
            'floating' => 1,
            'float' => 'right',
            'width' => '20'
        ]);
    }

    private function getProgramFieldValue($value, $session_value)
    {
        if ($tmp_value = $this->model->sessionGet($session_value)) {
            $value = $tmp_value;
        }

        // Check if the value is a valid JSON
        if (is_string($value) && is_array(json_decode($value, true)) && (json_last_error() == JSON_ERROR_NONE)) {
            $decoded_value = json_decode($value, true);
            $value = '';
            foreach ($decoded_value as $item) {
                $value .= substr($item, 0, 3) . ', ';
            }

            $value = rtrim($value, ', ');
        }

        if (strlen($value) > 20) {
            $value = '...' . substr($value, -20);
        }

        return $value;
    }

    private function divNumPicker($div_name, $div_title)
    {
        return $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#' . $div_title . '#}',
            'div_name' => $div_name,
            'content' => $this->components->themeLongList([
                'set_variable' => false,
                'controller' => 'settings/updateProgramReps/',
                'variable' => $div_name,
                'list' => explode(';', '1;2;3;4;5;6;7')
            ], [
                'background-color' => '#2e3237',
            ])
        ]);
    }

    private function divWeekPicker($div_name)
    {
        return $this->components->themeFullScreenDiv([
            'title' => '{#choose_a_week#}',
            'div_name' => $div_name,
            'content' => $this->components->themeLongList([
                'set_variable' => false,
                'controller' => 'settings/updateProgramWeek/',
                'variable' => $div_name,
                'list' => explode(';', 'Week 1;Week 2;Week 3;Week 4;Week 5;Week 6;Week 7;Week 8')
            ], [
                'background-color' => '#2e3237',
            ])
        ]);
    }

    private function divFoodPicker($div_name)
    {
        return $this->components->themeFullScreenDiv([
            'title' => '{#choose_a_week#}',
            'div_name' => $div_name,
            'content' => $this->components->themeLongList([
                'set_variable' => false,
                'controller' => 'settings/updateProgramWeek/',
                'variable' => $div_name,
                'list' => explode(';', 'Vegetarian;Low Carb;Fodmap;Paleo;Atkins')
            ], [
                'background-color' => '#2e3237',
            ])
        ]);
    }

    private function divDayPicker($div_name, $div_title, $values)
    {
        if ($tmp_value = $this->model->sessionGet($div_name)) {
            $values = json_decode($tmp_value);
        }

        return $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#' . $div_title . '#}',
            'div_name' => $div_name,
            'content' => $this->components->themeMultiselectList([
                'variable' => $div_name,
                'values' => $values,
                'list' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'controller' => 'settings/updateProgramDays/',
                'controller_refresh' => true,
            ], [
                'background-color' => '#2e3237',
            ])
        ]);
    }

    private function divTimePicker($div_name, $div_title, $selected_hour)
    {
        if ($tmp_value = $this->model->sessionGet($div_name)) {
            $selected_hour = $tmp_value;
        }

        $pieces = explode(':', $selected_hour);

        return $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#' . $div_title . '#}',
            'div_name' => $div_name,
            'content' => $this->components->themeTimePicker([
                'variable' => $div_name,
                'hours' => $this->model->getHourSelectorData24h(),
                'minutes' => $this->model->getMinuteSelectorData(),
                'hour' => $pieces[0],
                'minute' => $pieces[1],
                'controller' => 'settings/updateTime/'
            ], [
                'background-color' => '#2e3237',
            ])
        ]);
    }

    private function divConfirmProgramEdit()
    {
        return $this->components->themeNoticeDiv(
            '{#program_change#}',
            '{#one_or_more_programs_would_be_modified#}. {#please_confirm#}.',
            [
                $this->getOnclickHideDiv('confirm_program_edit'),
                $this->getOnclickSubmit('settings/storeSettings/confirm'),
            ]
        );
    }

    public function getDivs()
    {

        $this->divs['delete_confirmation'] = $this->components->themeConfirmationDiv(
            '{#please_confirm#}',
            '{#are_you_sure_you_want_to_quit_this_program#}?',
            'delete_confirmation', [
                $this->getOnclickHideDiv('delete_confirmation'),
                $this->getOnclickSubmit('settings/dodelete/')
            ]
        );

        return $this->divs;
    }

    private function renderValidationErrors()
    {
        if (empty($this->model->validation_errors)) {
            return false;
        }

        $messages = [];

        foreach ($this->model->validation_errors as $error) {
            $messages[] = $this->getComponentText($error, [], [
                'color' => '#ff0000',
                'font-size' => '15',
                'text-align' => 'center',
                'padding' => '5 0 5 0',
            ]);
        }

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentColumn($messages, [], [
                'width' => 'auto',
                'padding' => '5 20 5 20',
            ])
        ]);

        return true;

    }

}