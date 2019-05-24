<?php

/*
	This is a theme specific subcontroller. 
	

 notice the class naming here, needs to adhere to this standard and extend the main controller*/

class exampleMobileintroSubController extends MobileintroController {


	public function getExampleString(){
		return $this->getText('Hello from the sub controller');
	}



}