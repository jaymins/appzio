<?php

/*

    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done

*/

Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');


class MobilelocationController extends ArticleController {

    public $data;
    public $toplist;
    public $special;
    public $buttonstyle = array(
        'text-color' => '#eff6ef',
        //'background_color' => '#50ca50',
        'font-style' => 'bold',
        'font-size' => '18',
        'padding' => '8 8 8 8',
        'radius' => '10'
    );

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '12'
    );

    public function tab1(){
        $data = new StdClass();

        $textstyle['font-size'] = '14';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '10 0 10 0';


        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        $imgstyle['crop'] = 'round';
        $imgstyle['margin'] = '20 90 10 90';
        $imgstyle['text-align'] = 'center';
        $imgstyle['variable'] = $this->getVariableId( 'profilepic' );

        if(isset($this->varcontent['profilepic'])){
            $profilepic = $this->varcontent['profilepic'];
        } else {
            $profilepic = 'anonymous2.png';
        }

        $output[] = $this->getImage($profilepic,$imgstyle);


        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        if(isset($this->varcontent['city'])){
            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );
            $output[] = $this->getText(ThirdpartyServices::translateString( $this->varcontent['city'], 'en', $tr_lang ) ,$textstyle);
        } else {
            $output[] = $this->getText('Unable to locate properly :(',$textstyle);
        }

        $textstyle['font-size'] = '14';

        if(isset($this->varcontent['country'])){
            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );
            $output[] = $this->getText(ThirdpartyServices::translateString( $this->varcontent['country'], 'en', $tr_lang ),$textstyle);
        }

        $textstyle['margin'] = '0 0 15 0';

        if(isset($this->varcontent['lat']) AND isset($this->varcontent['lon'])){
            $output[] = $this->getText($this->varcontent['lat'] .', ' .$this->varcontent['lon'],$textstyle);
        }


        $textstyle['color'] = '#42bb43';

        if($this->menuid == 'update-location') {
            if(isset($this->varcontent['lat']) AND $this->varcontent['lat']){
                MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
            }

            $onload = new StdClass();
            $onload->action = 'submit-form-content';
            $onload->id = 'location-refreshed';

            $this->data->onload[] = $onload;
        }

        if ( $this->menuid == 'location-refreshed' ) {
            $output[] = $this->getText('{#location_updated#}',$textstyle);
        }

        $buttonparams['onclick'] = new StdClass();
        $buttonparams['onclick']->action = 'ask-location';
        $buttonparams['onclick']->sync_open = 1;
        $buttonparams['onclick']->id = 'update-location';
        $buttonparams['style'] = 'general_button_style_red';

        $this->loadVariableContent(true);
        $this->getText($this->getSavedVariable('city'));

        $output[] = $this->getText('{#update_my_location#}',$buttonparams);


        $data->scroll = $output;
        //$data->footer[] = $this->getTextbutton('Save',array('id' => 5555));
        return $data;

    }

    public function addField($name,$title,$hint){
        $param = $this->getVariable($name);
        $column1[] = $this->getText($title,$this->fontstyle+array('width'=>'70','text-align' => 'right'));
        $column1[] = $this->getFieldtext(
            $param,array('text_color' => '#7d7d7d', 'variable' => $this->vars[$name],'value' => $param,
                      'hint' => $hint,'margin' => '0 30 0 10','padding' => '3 5 3 5',
            'border-width' => '1','border-radius' => '8',"height"=>'35',
            'border-color' => '#42bb43','width' => '150',
                      'text-align' => 'left')+$this->fontstyle
                );
        $output = $this->getRow($column1,array('margin' => '0 10 5 30'));

        return $output;

    }

}