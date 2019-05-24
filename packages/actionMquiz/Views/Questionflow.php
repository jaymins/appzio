<?php

namespace packages\actionMquiz\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMquiz\Components\Components;
use packages\actionMquiz\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Questionflow extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var \packages\actionMquiz\themes\swiss8\Components\Components */
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
        $list = $this->getData('list', 'array');
        $phase = $this->getData('phase', 'mixed');
        $totalcount = count($list) - 1;

        if($this->hide_default_menubar){
            $params['icon_color'] = $this->color_top_bar_text_color == '#FFFFFFFF' ? 'white' : 'black';
            $params['mode'] = 'gohome';
            $params['title'] = $this->model->getConfigParam('subject');


            if(!empty($menu)){
                $params['right_menu'] = $menu;
            }

            $this->layout->header[] = $this->components->uiKitFauxTopBar($params);
        }


        if($this->model->getConfigParam('flow_progress')){
            $progress = $phase == 0 ? '0 / '.$totalcount : round($phase / $totalcount,2);
            $this->layout->header[] = $this->getComponentProgress($progress,[
                'track_color' => '#D6D9DB',
                'progress_color' => '#3EB439'],['height' => '4']);
        }

        //$this->layout->header[] = $this->getComponentText($phase .' / ' .$totalcount,[],['font-size' => '16','margin' => '20 0 15 0']);

        if(isset($list[$phase])){
            $question = $list[$phase]->question;

            if(isset($question->title)){
                $col[] = $this->getComponentText($question->title,[],['font-size' => '22','margin' => '20 0 0 0']);
                $col[] = $this->getComponentText($question->question,[],['font-size' => '16','margin' => '10 0 15 0']);
                $col[] = $this->getComponentDivider();

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
        } else {
            $this->layout->scroll[] = $this->getComponentText('phase:'.$phase);
        }

        $this->setFooter($phase,$totalcount);

        return $this->layout;
    }

    public function setFooter($phase,$totalcount){

        $this->layout->footer[] = $this->getComponentDivider();

        if($phase != 0){
            $prev = $phase-1;
            $onclick = $this->getOnclickSubmit('phase-'.$prev);
            $col[] = $this->getComponentText('‹ {#previous#}', [
                'onclick' => $onclick
            ],['margin' => '0 0 0 0']);
        }

        $next = $phase+1;
        $onclick = $this->getOnclickSubmit('phase-'.$next);

        if($this->model->getConfigParam('flow_skip')){
            $col[] = $this->getComponentText('{#skip#}', [
                'onclick' => $onclick
            ],['margin' => '0 0 0 0','floating' => '1','float' => 'center']);
        }

        if($phase == $totalcount){

            $finish_onclick[] = $this->getOnclickSubmit('phase-0');
            $finish_onclick[] = $this->getOnclickSubmit('questionflow/updatepivot/1',['delay' => 2]);

            if($this->model->getConfigParam('flow_complete_action')) {
                $finish_onclick[] = $this->getOnclickCompleteAction();
            }

            if($this->model->getConfigParam('flow_home')) {
                $finish_onclick[] = $this->getOnclickOpenAction(false,$this->model->getConfigParam('flow_home'),['sync_open' => 1]);
            } else {
                $finish_onclick[] = $this->getOnclickGoHome(['sync_open' => 1]);
            }

            $col[] = $this->getComponentText('{#finish#} ›', [
                'onclick' => $finish_onclick
            ],['margin' => '0 0 0 0','floating' => '1','float' => 'right']);

        } else {
            $next = $phase+1;
            $onclick = $this->getOnclickSubmit('phase-'.$next);
            $col[] = $this->getComponentText('{#next#} ›', [
                'onclick' => $onclick
            ],['margin' => '0 0 0 0','floating' => '1','float' => 'right']);

        }

        $this->layout->footer[] = $this->getComponentRow($col,[],['margin' => '15 15 15 15']);

    }



}
