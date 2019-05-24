<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class MobileavailabilityController extends ArticleController {

    public $data;
    public $theme;
    public $availability;

    public function tab1(){
        $this->data = new StdClass();
        $this->data->scroll[] = $this->getText($this->getConfigParam('msg'),array('style' => 'selector_general_txt'));

        switch($this->getConfigParam('granularity')){
            case 'by_morning_afternoon':
                $this->getCalendarMorningAfternoon();
                break;
        }

        if($this->getConfigParam('image_portrait')){
            $this->data->header[] = $this->getImage($this->getConfigParam('image_portrait'));
        }

        return $this->data;
    }

    public function getCalendarMorningAfternoon(){

        if($this->menuid == 'savetimes'){
            if(!empty($this->submitvariables)){

                foreach ($this->submitvariables as $key=>$time){
                    if($time){
                        $times[$key] = true;
                     }
                }

                if(isset($times)){
                    $this->saveVariable('availability',json_encode($times));
                    $this->loadVariableContent(true);
                }
            }
        }

        if($this->getSavedVariable('availability')){
            $av = json_decode($this->getSavedVariable('availability'),true);
            if(is_array($av) AND !empty($av)){
                $this->availability = $av;
            }
        }

        if($this->getConfigParam('complete_action') AND $this->menuid == 'savetimes'){
            $this->completeAction();
            return true;
        }

        $this->data->scroll[] = $this->getDayMorningAfternoon('monday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('tuesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('wednesday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('thursday');
        $this->data->scroll[] = $this->getDayMorningAfternoon('friday');

        if($this->getConfigParam('full_week')){
            $this->data->scroll[] = $this->getDayMorningAfternoon('saturday');
            $this->data->scroll[] = $this->getDayMorningAfternoon('sunday');
        }

        if($this->menuid == 'savetimes'){
            $this->data->footer[] = $this->getText('{#saved#}',array('style' => 'selector_general_txt'));
        }

        if($this->getConfigParam('complete_action')){
            $this->data->footer[] = $this->getTextbutton('{#save_and_continue#}',array('id' => 'savetimes'));
        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}',array('id' => 'savetimes'));
        }
    }

    public function getDayMorningAfternoon($daytitle){
        $selectstate = array('variable_value' => "1",'style' => 'selector_day_selected','allow_unselect' => 1,'animation' => 'fade');
        $selectstate_active = array('variable_value' => "1",'active' => 1,'style' => 'selector_day_selected','allow_unselect' => 1,'animation' => 'fade');
        $col[] = $this->getText('{#'.$daytitle.'#}',array('style' => 'selector_daytitle'));

        $varname = $daytitle.'_morning';
        if(isset($this->availability[$varname])){
            $col[] = $this->getText('{#morning#}',array('variable' => $varname,'active' => 1, 'selected_state' => $selectstate_active,'style' => 'selector_day'));
        } else {
            $col[] = $this->getText('{#morning#}',array('variable' => $varname,'selected_state' => $selectstate,'style' => 'selector_day'));
        }

        $varname = $daytitle.'_afternoon';
        if(isset($this->availability[$varname])){
            $col[] = $this->getText('{#afternoon#}',array('variable' => $varname,'active' => 1, 'selected_state' => $selectstate_active,'style' => 'selector_day'));
        } else {
            $col[] = $this->getText('{#afternoon#}',array('variable' => $varname,'selected_state' => $selectstate,'style' => 'selector_day'));
        }

        return $this->getRow($col,array('margin' => '4 10 4 10'));
    }

    public function completeAction(){

/*      $submit = new stdClass();
        $submit->action = 'submit-form-content';
        $submit->id = 'savetimes';

        $this->data->onload[] = $submit;*/

        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getLoader('{#saving#}');
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->onload[] = $this->getCompleteAction();
        return true;
    }

}