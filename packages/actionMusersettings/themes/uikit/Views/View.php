<?php

namespace packages\actionMusersettings\themes\uikit\Views;

use packages\actionMusersettings\Views\View as BootstrapView;
use packages\actionMusersettings\themes\uikit\Components\Components;
use packages\actionMusersettings\themes\uikit\Models\Model as ArticleModel;

class View extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {

        $this->layout = new \stdClass();


        if($this->hide_default_menubar){
            $params['icon_color'] = 'white';
            $params['mode'] = 'gohome';
            $params['title'] = $this->model->getConfigParam('subject');

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }


        $this->layout->header[] = $this->getComponentText('', [], ['height' => 2, 'background-color' => '#e5e5e5', 'width' => '100%']);

        $insta = $this->getData('show_insta_loader', 'bool');

        if ($insta) {
            $col[] = $this->getComponentLoader();
            $col[] = $this->getComponentText('{#loading_images_from_instagram#}...', [], [
                'font-size' => '14', 'margin' => '0 0 0 10', 'color' => $this->color_top_bar_text_color
            ]);

            $this->layout->header[] = $this->getComponentRow($col, [], [
                'background-color' => $this->color_top_bar_color,
                'padding' => '10 10 10 10', 'text-align' => 'center'
            ]);
        }

        $this->displayFieldList();
        $this->getSettingsBlock('{#notifications_settings#}');

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText('{#receive_push_notifications#}', array(), ['margin' => '4 15 4 15', 'width' => '70%', 'font-size' => '14']),
            $this->getComponentFormFieldOnoff(array(
                'type' => 'toggle',
                'variable' => 'receive_notification',
                'value' => $this->model->getSavedVariable('receive_notification')
            ), ['margin' => '4 15 4 15'])
        ), ['width' => '90%']);

        $this->layout->scroll[] = $this->getDivider();

        if (!$this->model->getConfigParam('no_profiletoggle')) {
            $this->layout->scroll[] = $this->getComponentRow(array(
                $this->getComponentText('{#turn_off_my_profile_temporarily#}', array(), ['margin' => '4 15 4 15', 'width' => '70%', 'font-size' => '14']),
                $this->getComponentFormFieldOnoff(array(
                    'type' => 'toggle',
                    'variable' => 'hide_my_profile',
                    'value' => $this->model->getSavedVariable('hide_my_profile')
                ), ['margin' => '4 15 4 15'])
            ), ['width' => '90%']);

            $this->layout->scroll[] = $this->getDivider();
        }

        if (!$this->model->getConfigParam('no_likescounttoggle')) {

            $this->layout->scroll[] = $this->getComponentRow(array(
                $this->getComponentText('{#hide_number_of_likes_and_followers#}', array(), ['margin' => '4 15 4 15', 'width' => '70%', 'font-size' => '14']),
                $this->getComponentFormFieldOnoff(array(
                    'type' => 'toggle',
                    'variable' => 'hide_like_count',
                    'value' => $this->model->getSavedVariable('hide_like_count')
                ), ['margin' => '4 15 4 15'])
            ), ['width' => '90%']);


            $this->layout->scroll[] = $this->getDivider();
        }

        /*        $this->layout->scroll[] = $this->getComponentRow(array(
                    $this->getComponentText('{#receive_sms#}', array(
                        'style' => 'uikit-settings-field'
                    )),
                    $this->getComponentFormFieldOnoff(array(
                        'type' => 'toggle',
                        'variable' => 'receive_sms',
                        'value' => $this->model->getSavedVariable('receive_sms')
                    ))
                ));
                $this->layout->scroll[] = $this->getDivider();

                $this->layout->scroll[] = $this->getComponentRow(array(
                    $this->getComponentText('{#receive_email#}', array(
                        'style' => 'uikit-settings-field'
                    )),
                    $this->getComponentFormFieldOnoff(array(
                        'type' => 'toggle',
                        'variable' => 'receive_email',
                        'value' => $this->model->getSavedVariable('receive_email')
                    ))
                ));*/

        $this->displaySaveButton('footer');

        $fieldList = $this->getData('fieldlist', 'array');

        if (in_array('mreg_delete_user', $fieldList)) {
            $this->layout->footer[] = $this->getComponentText('{#delete_profile#}', [
                'uppercase' => 1,
                'onclick' => $this->getOnclickOpenAction('gdpr')], [
                'text-align' => 'center', 'width' => '100%', 'font-size' => '14', 'margin' => '0 0 15 0'
            ]);
        }

        return $this->layout;
    }

    public function getDivs()
    {

        $divs['pick_event_date'] = $this->components->uiKitBirthdayPickerDiv(array(
            'title' => '{#select_birthday#}',
            'variable' => 'event_date',
            'show_random' => false,
        ));

        return $divs;
    }


    public function addField_page1($field)
    {
        $result = [];
        switch ($field) {

            case 'mreg_profile_collect_photo':
                $insta = $this->getData('instagram_images', 'array');
                $this->model->loadVariableContent(true);


                $result[] = $this->uikitUserPhotoUploader($this->model->varcontent, $insta);

                if(!$this->model->getConfigParam('hide_profile_progress')){
                    $progress = $this->getData('profile_progress', 'mixed');

                    if($progress){

                        $percentage = $progress*100 .'%';

                        $col[] = $this->getComponentProgress($progress,[
                            'track_color' => '#B2B4B3',
                            'progress_color' => $this->color_top_bar_color,
                            'text_content' => $percentage
                        ],[
                            'width' => $this->screen_width/2,
                            'height' => '20',
                            'font-size' => '12',
                            'color' => '#ffffff',
                            'text-align' => 'center',
                            'border-radius' => '10',
                        ]);

                        $col[] = $this->getComponentText('{#your_profile_completness#}',[],[
                            'margin' => '8 0 0 0','font-size' => '14','color' => '#545050'
                        ]);

                        $result[] = $this->getComponentColumn($col,[],[
                            'text-align' => 'center',
                            'margin' => '0 20 20 20'
                        ]);


                        unset($col);

                    }
                }


                $insta = $this->getData('show_instagram_load_button', 'bool');
                $loader = $this->getData('show_insta_loader', 'bool');

                if ($insta AND !$loader) {
                    $onclick[] = $this->getOnclickSubmit('activateloader');
                    $onclick[] = $this->getOnclickSubmit('default/setinstaimages/1', ['hide_loader' => true]);
                    $onclick[] = $this->getOnclickSubmit('update', ['hide_loader' => true, 'delay' => 60]);

                    $col[] = $this->getComponentText('{#fetch_images_from_instagram#}', ['style' => 'uikit_list_general_small_hollow_button']);
                    $result[] = $this->getComponentRow($col, ['onclick' => $onclick], ['text-align' => 'center']);
                    $result[] = $this->getComponentSpacer(15);
                }

                break;

            case 'mreg_profile_collect_full_name':
                $content[] = $this->uiKitFormSectionHeader('{#personal_information#}');
                $content[] = $this->uiKitHintedTextField('{#first_name#}', 'firstname', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $content[] = $this->uiKitHintedTextField('{#last_name#}', 'lastname', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_nickname':
                $content[] = $this->uiKitFormSectionHeader('{#nickname#}');
                $content[] = $this->uiKitHintedTextField('{#nickname#}', 'nickname', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_facebook':
                $content[] = $this->uiKitFormSectionHeader('{#social#}');
                $content[] = $this->uiKitHintedTextField('{#facebook_profile#} ({#paste_url#})', 'facebook_profile', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_instagram':
                $content[] = $this->uiKitHintedTextField('{#instagram_profile#} ({#paste_url#})', 'instagram_profile', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_twitter':
                $content[] = $this->uiKitHintedTextField('{#twitter_profile#} ({#paste_url#})', 'twitter_profile', 'text', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'collect_age':
                $content[] = $this->uiKitHintedTextField('{#age#}', 'age', 'number', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_profile_collect_birthday':
                $content[] = $this->birthdayField();
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'mreg_collect_profile_comment':

                $params['uppercase'] = 1;
                $params['no_divider'] = 1;

                if (!$this->model->getSavedVariable('profile_comment')) {
                    $params['value'] = $this->model->getSavedVariable('instagram_bio');
                }

                $content[] = $this->uiKitHintedTextField('{#profile_text#}', 'profile_comment', 'textarea', $params);
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;

            case 'settings_fields_1':
                $result = $this->fieldSet(1);
                break;

            case 'settings_fields_2':
                $result = $this->fieldSet(2);
                break;

            case 'settings_fields_3':
                $result = $this->fieldSet(3);
                break;

            case 'settings_fields_4':
                $result = $this->fieldSet(4);
                break;

            case 'mreg_profile_collect_email':
                $content[] = $this->uiKitHintedTextField('{#email#}', 'email', 'email', ['uppercase' => 1, 'no_divider' => 1]);
                $content[] = $this->getComponentDivider();
                $result[] = $this->getComponentColumn($content, array(), array(
                    'width' => 'auto'
                ));
                break;
        }

        return $result;
    }

    public function fieldSet($num)
    {
        $fieldsets = $this->getData('fieldsets', 'array');

        if (!isset($fieldsets[$num]['fields']) OR !$fieldsets[$num]['title'] OR !$fieldsets[$num]['fields']) {
            return false;
        }

        $fields = $fieldsets[$num]['fields'];
        $title = $fieldsets[$num]['title'];
        $result[] = $this->uiKitFormSectionHeader($title);

        if ($fields) {
            foreach ($fields as $quiz) {
                $onclick = $this->getOnclickOpenAction('quizquestion', false, [
                    'sync_open' => 1, 'id' => $quiz->question->id, 'back_button' => 1
                ]);

                $desc = $this->model->getSavedVariable($quiz->question->variable_name) ? $this->model->getSavedVariable($quiz->question->variable_name) : $quiz->question->question;

                if (stristr($desc, '{')) {
                    $desc = json_decode($desc, true);
                    if (is_array($desc)) {
                        $desc = implode(', ', $desc);
                    }
                }

                $result[] = $this->uiKitFormSettingsField(
                    ['title' => $quiz->question->title,
                        'onclick' => $onclick,
                        'icon' => $quiz->question->picture ? $quiz->question->picture : 'uikit_form_settings.png',
                        'description' => $desc
                    ]
                );

                $result[] = $this->getDivider();
            }

            array_pop($result);
        }

        return $result;
    }

    public function displaySaveButton($location = 'scroll')
    {
        $saved = $this->getData('saved', 'int');

        if ($saved) {
            $text = strtoupper($this->model->localize('{#saved#}'));
        } else {
            $text = strtoupper($this->model->localize('{#save#}'));
        }

        $onclick[] = $this->getOnclickSubmit('Controller/save/');
        $onclick[] = $this->getOnclickListBranches(['loader_off' => true]);

        $this->layout->{$location}[] = $this->getComponentSpacer('20');
        $this->layout->{$location}[] = $this->uiKitButtonFilled($text, array('onclick' => $onclick));
        $this->layout->{$location}[] = $this->getComponentSpacer('20');
    }

    private function birthdayField()
    {

        $title = '{#event_date#}';
        $params = array(
            'onclick' => $this->getOnclickShowDiv('pick_event_date', $this->getDivParams()),
            //'variable' => 'event_date',
            //'style' => 'uikit-general-field-icon',
            //'left_icon' => 'icon-calendar-reg-2.png',
            //'time_format' => 'm / d / Y',
        );

        /*        if ( isset($this->event_data->reminders[0]->date) AND $this->event_data->reminders[0]->is_full_day ) {
                    $timestamp = $this->event_data->reminders[0]->date;
                    $title = date( 'm / d / Y', $timestamp );

                    $params['title'] = $title;
                    $params['value'] = $timestamp;
                }*/

        $title = strtoupper($this->model->localize('{#birthdate#}'));

        $day = $this->model->getSubmittedVariableByName('birth_day') ? $this->model->getSubmittedVariableByName('birth_day') : $this->model->getSavedVariable('birth_day');
        $month = $this->model->getSubmittedVariableByName('birth_month') ? $this->model->getSubmittedVariableByName('birth_month') : $this->model->getSavedVariable('birth_month');
        $year = $this->model->getSubmittedVariableByName('birth_year') ? $this->model->getSubmittedVariableByName('birth_year') : $this->model->getSavedVariable('birth_year');

        if($this->model->getConfigParam('us_time_format')){
            $row[] = $this->getComponentText($month, ['variable' => $this->model->getVariableId('birth_month')], ['margin' => '0 0 0 15', 'width' => '20']);
            $row[] = $this->getComponentText('/', [], ['margin' => '0 0 0 0']);
            $row[] = $this->getComponentText($day, ['variable' => $this->model->getVariableId('birth_day')], ['margin' => '0 0 0 0', 'width' => '20']);
            $row[] = $this->getComponentText('/', [], ['margin' => '0 0 0 0']);
            $row[] = $this->getComponentText($year, ['variable' => $this->model->getVariableId('birth_year')], ['margin' => '0 0 0 0', 'width' => '50']);
        } else {
            $row[] = $this->getComponentText($day, ['variable' => $this->model->getVariableId('birth_day')], ['margin' => '0 0 0 15', 'width' => '20']);
            $row[] = $this->getComponentText('.', [], ['margin' => '0 0 0 0']);
            $row[] = $this->getComponentText($month, ['variable' => $this->model->getVariableId('birth_month')], ['margin' => '0 0 0 0', 'width' => '20']);
            $row[] = $this->getComponentText('.', [], ['margin' => '0 0 0 0']);
            $row[] = $this->getComponentText($year, ['variable' => $this->model->getVariableId('birth_year')], ['margin' => '0 0 0 0', 'width' => '50']);
        }

        $layout = new \stdClass();
        $layout = new \stdClass();
        $layout->left = 0;
        $layout->right = 0;
        $layout->bottom = 0;

        /*        $params['overlay'][] = $this->getComponentText('hello',['layout' => $layout],[
                    'height' => '60', 'width' => $this->screen_width,
                    'background-color' => '#000000'
                    ]);*/

        return $this->getComponentColumn(array(
            $this->getComponentText($title, ['style' => 'uikit_steps_hint']),
            $this->getComponentRow($row, $params, ['margin' => '15 0 10 0']),
            $this->getComponentDivider(array(
                'style' => 'article-uikit-divider-thin'
            )),
        ), array(
            'id' => 'section-event-date',
            //'visibility' => $this->getVisibilityStatus('visible', 'full_day'),
        ));
    }

    public function displayFieldList()
    {
        /* get the data defined by the controller */
        $fieldList = $this->getData('fieldlist', 'array');

        $collection = [];
        foreach ($fieldList as $field) {

            $groupName = explode('_', $field);

            if (isset($groupName[0]) AND isset($groupName[1])) {
                //if ($groupName[0] == 'mreg' && $groupName[1] != 'collect') {
                if (!isset($collection[$groupName[1]])) {
                    $collection[$groupName[1]] = [];
                }
                $collection[$groupName[1]] = array_merge($collection[$groupName[1]], $this->addField_page1($field));
                //}
            }
        }

        if ($collection) {
            foreach ($collection as $title => $items) {

                $this->layout->scroll[] = $this->components->getSummaryBox($title, $items);
            }
        }
    }

    public function fieldPageInput($hint, $variable, $type = 'text', $value = null)
    {

        if ($this->model->getSubmittedVariableByName($variable)) {
            $value = $this->model->getSubmittedVariableByName($variable);
        } elseif ($this->model->getSavedVariable($variable)) {
            $value = $this->model->getSavedVariable($variable);
        } elseif ($value === null) {
            $value = '0.11294';
        }

        return $this->components->getComponentRow(array(
            $this->getComponentText($hint, array('style' => 'settings_icon_field')),
            $this->getComponentFormFieldText($value, array(
                'variable' => $variable,
                'style' => 'settings_fieldtext',
                'input_type' => $type
            ))
        ), array(), array(
            'width' => 'auto',
            'padding' => '5 15 5 15',
            'vertical-align' => 'middle',
        ));
    }

    private function getSettingsBlock($text)
    {
        $this->layout->scroll[] = $this->getDivider();
        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader($text, [], [
            'font-size' => '18',
            'padding' => '15 15 15 15',
            'margin' => '0 0 0 0',
        ]);
        $this->layout->scroll[] = $this->getDivider();
    }

    private function getLanguageSelector()
    {

        if (!isset($this->model->localizationComponent->mobilesettings->languages)) {
            return false;
        }

        $languages = explode(',', $this->model->localizationComponent->mobilesettings->languages);

        if (empty($languages)) {
            return false;
        }

        $buttons = [];


        foreach ($languages as $lang_code) {

            $styles = array(
                'margin' => '0 5 0 5',
                'font-size' => 18,
                'color' => '#80828b',
            );

            $onclick = new \StdClass();
            $onclick->action = 'submit-form-content';
            $onclick->id = 'lang_' . $lang_code;

            $params = array(
                'onclick' => $onclick,
            );

            if ($lang_code == $this->model->getSavedVariable('user_language', 'en')) {
                $styles = array_merge($styles, array(
                    'font-weight' => 'bold',
                    'color' => $this->color_top_bar_color,
                ));
            }

            $buttons[] = $this->getComponentText(strtoupper($lang_code), $params, $styles);

            unset($styles);
        }

        $this->layout->scroll[] = $this->getComponentRow($buttons, array(), array(
            'padding' => '20 15 20 15'
        ));

        return true;
    }

    private function getDivParams()
    {

        $layout = new \stdClass();
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        return array(
            'background' => 'blur',
            'tap_to_close' => 1,
            'transition' => 'from-bottom',
            'layout' => $layout
        );
    }


}