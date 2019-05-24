<?php

class oliveMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function olive(){

        if(isset($this->menuid) AND $this->menuid == 'save-data') {
            $this->saveVariables();
        } elseif(stristr($this->menuid,'distance_')){
            $this->saveVariables();
        }

        if(!isset($this->varcontent['distance'])){
            AeplayVariable::updateWithName($this->playid, 'distance' ,10000, $this->gid);
            $distance = 10000;
        } else {
            $distance = $this->varcontent['distance'];
        }

        if(stristr($this->menuid,'distance_')){
            $dist = str_replace('distance_','',$this->menuid);
            AeplayVariable::updateWithName($this->playid, 'distance', $dist, $this->gid);
            $distance = $dist;
        }

        $this->data->scroll[] = $this->getText('{#filtering#}',array('style' => 'form-field-section-title'));
        $this->data->scroll[] = $this->formkitSlider('{#distance#} ({#km#})', 'distance', $distance, 50, 10000, 10);

        $this->data->scroll[] = $this->formkitCheckbox('notify', '{#enable_notifications#}', array(
            'type' => 'toggle',
        ));

        if(isset($this->menuid) AND $this->menuid == 'reset-matches') {
            $font = $this->fontstyle;
            $font['text-align'] = 'center';
            $this->initMobileMatching();
            $this->mobilematchingobj->resetMatches();
        }

        $onclick1['action'] = 'submit-form-content';
        $onclick1['id'] = 'reset-matches';
        $onclick2['action'] = 'go-home';

        $options['style'] = 'olive-reset-button';
        $options['onclick'] = array($onclick1,$onclick2);

        //$this->data->scroll[] = $this->getText('{#reset_my_matches#}', $options);

        if(isset($this->menuid) AND $this->menuid == 'save-data'){
            $this->data->footer[] = $this->getTextbutton('{#saved#}',array('style' => 'olive-submit-button', 'id' => 'save-data'));
        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}',array('style' => 'olive-submit-button', 'id' => 'save-data'));
        }

    }

}