<?php

namespace packages\actionMswipematch\Views;

use Bootstrap\Views\BootstrapView;

class Filtering extends BootstrapView {

    /* @var \packages\actionMswipematch\themes\igers\Components\Components */
    public $components;
    public $theme;

    public function __construct($obj) {
        parent::__construct($obj);
    }


    /* view will always need to have a function called tab1 */
    public function tab1(){
        $this->layout = new \stdClass();

        if($this->hide_default_menubar){
            $params['title'] = $this->model->getConfigParam('subject');
            $params['mode'] = 'gohome';
            $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }



        if($this->model->getConfigParam('age_filtering')){
            $this->setAgeFilters();
        }

        if($this->model->getConfigParam('distance_filtering')){
            $this->setDistanceFilters();
        }

        $data = $this->fieldSet(1);

        if($data){ $this->layout->scroll = array_merge($this->layout->scroll,$data); }

        if($this->model->getConfigParam('allow_reset')){
            $reset_onclick[] = $this->getOnclickSubmit('reset');
            $reset_onclick[] = $this->getOnclickListBranches();
            $reset_onclick[] = $this->getOnclickGoHome();
            $this->layout->footer[] = $this->getComponentSpacer('20');
            $this->layout->footer[] = $this->uiKitButtonHollow('{#reset_un_matches#}',[
                'onclick' => $reset_onclick
            ]);
        }

        $onclick[] = $this->getOnclickSubmit('save');
        $onclick[] = $this->getOnclickGoHome();
        $this->layout->footer[] = $this->getComponentSpacer('20');
        $this->layout->footer[] = $this->uiKitButtonFilled('{#save#}',[
            'onclick' => $onclick
        ]);
        $this->layout->footer[] = $this->getComponentSpacer('20');

        return $this->layout;
    }

    public function setAgeFilters(){
        $params['min'] = $this->model->getConfigParam('min_age');
        $params['max'] = $this->model->getConfigParam('max_age');
        $params['title'] = '{#filter_by_age#}';
        $params['value'] = $this->model->getSavedVariable('filter_age_start') ? $this->model->getSavedVariable('filter_age_start') : $params['min'];
        $params['value2'] = $this->model->getSavedVariable('filter_age_end') ? $this->model->getSavedVariable('filter_age_end') :$params['max'];
        $params['variable'] = 'filter_age_start';
        $params['variable2'] = 'filter_age_end';
        $params['step'] = 1;
        $params['unit'] = 'Y';
        $this->layout->scroll[] = $this->uiKitTwoHandSlider($params);
    }

    public function setDistanceFilters(){
        $params['min'] = $this->model->getConfigParam('min_distance');
        $params['max'] = $this->model->getConfigParam('max_distance');
        $params['title'] = '{#filter_by_distance#}';
        $params['value'] = $this->model->getSavedVariable('filter_age_start') ? $this->model->getSavedVariable('filter_age_start') : $params['min'];
        $params['value2'] = $this->model->getSavedVariable('filter_age_end') ? $this->model->getSavedVariable('filter_age_end') :$params['max'];
        $params['step'] = 50;
        if($this->model->getConfigParam('use_miles')){
            $params['unit'] = 'mi';
        } else {
            $params['unit'] = 'km';
        }
        $this->layout->scroll[] = $this->uiKitSlider($params);
    }



    public function fieldSet($num){
        $fieldsets = $this->getData('fieldsets', 'array');

        if(!isset($fieldsets[$num]['fields']) OR !$fieldsets[$num]['title'] OR !$fieldsets[$num]['fields']){
            return false;
        }

        $fields = $fieldsets[$num]['fields'];
        $title = $fieldsets[$num]['title'];
        $result[] = $this->uiKitFormSectionHeader($title);

        if($fields){
            foreach ($fields as $quiz){
                $onclick = $this->getOnclickOpenAction('quizquestion',false,[
                    'sync_open' => 1,'id' => $quiz->question->id,'back_button' => 1
                ]);

                $desc = $this->model->getSavedVariable($quiz->question->variable_name) ? $this->model->getSavedVariable($quiz->question->variable_name) : $quiz->question->question;

                if(stristr($desc, '{')){
                    $desc = json_decode($desc,true);
                    if(is_array($desc)){
                        $desc = implode(', ', $desc);
                    } else {
                        $desc = '';
                    }
                }

                $result[] = $this->uiKitFormSettingsField(
                    ['title' => $quiz->question->title,
                        'onclick' => $onclick,
                        'icon' => $quiz->question->picture ? $quiz->question->picture : 'uikit_form_settings.png',
                        'description' => $desc
                    ]
                );

                $result[] = $this->getComponentDivider();
            }

            array_pop($result);
        }

        return $result;
    }



}