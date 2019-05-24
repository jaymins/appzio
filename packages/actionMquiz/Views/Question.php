<?php

namespace packages\actionMquiz\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMquiz\Components\Components;
use packages\actionMquiz\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Question extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $question = $this->getData('question', 'mixed');

        if($this->hide_default_menubar){
            $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';
            $params['mode'] = 'gohome';
            if(isset($question->question)){
                $params['title'] = $question->question;
            } else {
                $params['title'] = $this->model->getConfigParam('subject');
            }

            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }


        if(isset($question->title)){
            $col[] = $this->getComponentText($question->question,[],['font-size' => '16','margin' => '20 0 15 0']);
            $col[] = $this->getComponentDivider();
            //$options = explode(';',$question->answer_options);

            $data = $question->option;

            foreach($data as $option){
                $options[] = $option->answer;
            }

            $var = $this->model->getVariableId($question->variable_name);
            $var_value = $this->model->getSavedVariable($question->variable_name);

            if($question->type == 'multiselect' AND $question->allow_multiple == 0){
                $col[] = $this->uiKitRadioButtonsCheckboxes($options, $var, $var_value);
            }elseif($question->type == 'multiselect' AND $question->allow_multiple == 1){
                $col[] = $this->uiKitRadioButtonsCheckboxes($options, $var, $var_value,'checkboxes');
            }

            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 0 15']);
        }

        $onclick[] = $this->getOnclickSubmit('save');
        $onclick[] = $this->getOnclickGoHome();

        $this->layout->footer[] = $this->uiKitButtonHollow('{#save#}', [
            'onclick' => $onclick
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(15);


        return $this->layout;
    }



}
