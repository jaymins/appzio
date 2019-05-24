<?php

namespace packages\actionMquiz\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMquiz\Components\Components;
use packages\actionMquiz\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Questionlist extends BootstrapView
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
        $quizzes = $this->getData('list', 'array');

        if($this->hide_default_menubar){
            $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';
            $params['mode'] = 'gohome';
            $params['title'] = $this->model->getConfigParam('subject');


            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }

        if($quizzes){
            foreach ($quizzes as $quiz){

                $onclick = $this->getOnclickOpenAction('quizquestion',false,[
                    'sync_open' => 1,'id' => $quiz->question->id,'back_button' => 1
                ]);

                $this->layout->scroll[] = $this->uiKitFormSettingsField(
                    ['title' => $quiz->question->title,
                        'onclick' => $onclick,
                        'icon' => $quiz->question->picture ? $quiz->question->picture : 'uikit_form_settings.png',
                        'description' => $quiz->question->question
                    ]
                );
            }
        }
        return $this->layout;
    }

}
