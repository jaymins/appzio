<?php

class cookifyMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function cookify(){

        $data = new StdClass();

        if(isset($this->menuid) AND $this->menuid == '5555') {
            $this->saveVariables();
        } elseif(stristr($this->menuid,'distance_')){
            $this->saveVariables();
        }

        if(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
            $txt = 'Change the photo';
        } else {
            $pic = 'small-filmstrip.png';
            $txt = 'Add a photo';
        }

        $output[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '400','imgcrop'=>'yes', 'style' => 'cookify_profilepic'));

        if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 55556)){
            $this->data->scroll[] = $this->getText('Uploading ...', array( 'style' => 'uploading_text'));
        }

        $output[] = $this->getTextbutton($txt, array(
            'variable' => $this->getVariableId('profilepic'),
            'action' => 'upload-image',
            'max_dimensions'=> '900',
            'sync_upload'=>false,
            'style' => 'general_button_style_cookify' ,
            'id' => $this->getVariableId('profilepic')));


        $output[] = $this->getSpacer('20');
        $output[] = $this->addField('name','Name','Your real name');
        $output[] = $this->addField('phone','Phone','Your Phone');
        $output[] = $this->addField('email','Email','Your Email');

        $output[] = $this->getCheckbox('notify', 'Notifications', false, $this->fontstyle);

        if (isset($this->menuid) AND $this->menuid == '5555') {
            $this->saveVariables();
            $data->footer[] = $this->getTextbutton('Information saved',array('id' => 5555));
        } else {
            $data->footer[] = $this->getTextbutton('Save',array('id' => 5555));
        }

        $data->scroll = $output;

        $this->data = $data;
        return true;
    }

}