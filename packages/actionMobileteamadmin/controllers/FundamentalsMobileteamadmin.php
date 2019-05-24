<?php

/*
	This is a theme specific subcontroller.


 notice the class naming here, needs to adhere to this standard and extend the main controller*/

class fundamentalsMobileteamadmin extends MobileteamadminController {


    public function tab1(){
        $this->data = new stdClass();
        $this->initObjects();

        $this->smallHeader('{#fundamentals#}','{#subtext_for_fundamentals#}');

        $this->areas();
        return $this->data;
    }




    public function areas(){

        if(stristr($this->menuid,'confirmdelete-')){
            $id = str_replace('confirmdelete-','',$this->menuid);
            $this->deleteConfirm($id);
            return true;
        }

        if(stristr($this->menuid,'dodelete-')){
            $id = str_replace('dodelete-','',$this->menuid);
            $this->doDelete($id);
        }

        if($this->menuid == 'save-area'){
            $obj = new MobilefeedbacktoolfundamentalsModel();
            $obj->game_id = $this->gid;
            $obj->team_id = $this->team_id;
            $obj->title = $this->getSubmittedVariableByName('newfundamental');
            $obj->insert();
        }

        $this->listAreas();


        $this->addArea();
    }

    public function listAreas($hilite=false){
        $areas = MobilefeedbacktoolfundamentalsModel::model()->findAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->team_id),array('order' => 'title'));

        if(!empty($areas)){
            $this->data->scroll[] = $this->getText('{#current_areas#}',array('style' => 'gifit-title-explanation'));
            $this->data->scroll[] = $this->getSpacer(15);

            $style_field = array('text-align' => 'center','font-size' => '14',);
            $style_row = array('margin' => '5 50 5 50','background-color' => '#ffffff',
                'border-radius' => '8','padding' => '12 10 12 10','opacity' => '0.7');

            foreach ($areas as $area){

                if($area->id == $hilite) {
                    $style_row['border-color'] = '#FC0100';
                    $style_row['border-width'] = '2';
                    $style_row['background-color'] = '#FCAAA3';
                } else {
                    unset($style_row['border-color']);
                    unset($style_row['border-width']);
                    $style_row['background-color'] = '#ffffff';
                }

                $delete = $this->getOnclick('id',false,'confirmdelete-'.$area->id);

                $col[] = $this->getText($area->title,array(
                    'style' => $style_field,'variable' => 'newfundamental','hint' => '{#name_your_area#}'));
                $col[] = $this->getImage('trash-delete-icon.png',array('width' => '20','floating' => '1','float' => 'right'
                ,'onclick' => $delete));
                $this->data->scroll[] = $this->getRow($col,array('style' => $style_row));
                unset($col);

            }
        }
    }

    public function deleteConfirm($id){
        $this->listAreas($id);
        $this->data->footer[] = $this->getText('{#are_you_sure_you_want_to_delete_this_area#}?',array('style' => 'gifit-title-explanation'));
        $col[] = $this->getText('{#cancel#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('tab1',false,'cancel')));
        $col[] = $this->getText('{#delete#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'dodelete-'.$id)));
        $this->data->footer[] = $this->getRow($col,array('text-align'=> 'center'));

    }

    public function doDelete($id){
        if($id){
            MobilefeedbacktoolfundamentalsModel::model()->deleteAllByAttributes(array('game_id' => $this->gid,'team_id' => $this->team_id,'id' => $id));
        }
    }


    public function addArea(){
        $this->data->scroll[] = $this->getSpacer(20);
        $this->data->scroll[] = $this->getText('{#add_a_new_area#}',array('style' => 'gifit-title-explanation'));

        $style = array('margin' => '15 50 5 50','background-color' => '#ffffff','text-align' => 'center','font-size' => '14',
            'border-radius' => '8');

        $this->data->scroll[] = $this->getFieldtext('',array(
            'style' => $style,'variable' => 'newfundamental','hint' => '{#name_your_area#}'));

        $col[] = $this->getText('{#save#}',array('style' => 'gifit-button-footer','onclick' => $this->getOnclick('id',false,'save-area')));
        $this->data->scroll[] = $this->getRow($col,array('text-align'=> 'center'));


    }


    public function smallHeader($title,$subtext=false){
        $this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getText($title,array('style' => 'gifit-titletext-header'));

        if($subtext){
            $this->data->scroll[] = $this->getText($subtext,array('style' => 'gifit-titletext-header-subtext'));
        }
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
    }



    /* aka help */
    public function tab2(){
        $this->data = new stdClass();
        $this->setHelp();
        $this->data->scroll[] = $this->getText('{#back#}',array('style' => 'gifit-button','onclick' => $this->getOnclick('tab1',false)));
        return $this->data;

    }

    /* help section */
    public function setHelp(){
        $this->data->scroll[] = $this->getColumn($this->intro1(),array('background-color' => '#85d4ee'));
        $this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
        return true;
    }

    public function intro1(){
        $col[] = $this->getSpacer(10);
        $col[] = $this->getText('{#tracking_fundamentals#}',array('style' => 'gifit-titletext'));
        $col[] = $this->getSpacer(20);
        $col[] = $this->getText('{#tracking_fundamentals_help#}',array('style' => 'gifit-title-explanation'));
        return $col;
    }


}