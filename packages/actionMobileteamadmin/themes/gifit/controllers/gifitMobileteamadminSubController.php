<?php

/*
	This is a theme specific subcontroller. 
	

 notice the class naming here, needs to adhere to this standard and extend the main controller*/

class gifitMobileteamadminSubController extends MobileteamadminController {

	public function smallHeader($title,$subtext=false){
		$this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
		$this->data->scroll[] = $this->getText($title,array('style' => 'gifit-titletext-header'));

		if($subtext){
			$this->data->scroll[] = $this->getText($subtext,array('style' => 'gifit-titletext-header-subtext'));
		}
		$this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
	}




	public function teamHeader(){
		$this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));

		if(isset($this->data_team_member->role_type) AND $this->data_team_member->role_type == 'admin'){
			$this->data->scroll[] = $this->getText($this->data_team->title,array('style' => 'gifit-titletext-header','onclick' => $this->getOnclick('tab3',true)));
		} else {
			$this->data->scroll[] = $this->getText($this->data_team->title,array('style' => 'gifit-titletext-header'));
		}

		$this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
	}

	public function teamHeaderEdit(){
		$this->data->scroll[] = $this->getText('',array('height' => '20','background-color' => '#85d4ee'));
		$name = isset($this->data_team->title) ? $this->data_team->title : '{#new_team#}';
		$this->data->scroll[] = $this->getFieldtext($name,array('style' => 'gifit-titletext-header','activation' => 'initially','variable' => 'new-name'));
		$this->data->scroll[] = $this->getImage('cloud.png',array('width' => $this->screen_width));
	}


}