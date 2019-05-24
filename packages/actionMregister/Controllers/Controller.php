<?php

namespace packages\actionMregister\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMregister\Views\View as ArticleView;
use packages\actionMregister\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){

        /* if user has already completed the first phase, move to phase 2 */
        if($this->model->sessionGet('reg_phase') == 2){
            return $this->actionPagetwo();
        }

        $this->model->setFirstNameLastName();

        $data['fieldlist'] = $this->model->getFieldlist();
        $data['current_country'] = $this->model->getCountry();
        $data['mode'] = 'show';

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if($this->getMenuId() == 'signup'){
            $this->model->validatePage1();

            if(empty($this->model->validation_errors)){
                /* if validation succeeds, we save data to variables and move user to page 2*/
                $this->model->savePage1();
                $this->model->sessionSet('reg_phase', 2);
                return ['Pagetwo',$data];
            }
        }

        return ['View',$data];
    }


    public function actionPagetwo(){

        $data['mode'] = 'show';
        $data['fieldlist'] = $this->model->getFieldlist();

        /* no validation here */
        if($this->getMenuId() == 'done'){
            $this->model->closeLogin();
            $data['mode'] = 'close';
            return ['Complete',$data];
        }

        return ['Pagetwo',$data];

    }

	public function setExtraProfileData( $fb_data ) {

		if (empty($fb_data)) {
			return false;
		}

		if (isset($fb_data->name)) {
			$pieces = explode(' ', $fb_data->name);
			$this->model->saveVariable('firstname', $pieces[0]);

			if ( isset($pieces[1]) ) {
				$this->model->saveVariable('lastname', $pieces[1]);
			}

			$this->model->saveVariable('name', $fb_data->name);
			$this->model->saveVariable('real_name', $fb_data->name);
		}

		if (isset($fb_data->email)) {
			$this->model->saveVariable('email', $fb_data->email);
		}

		if (isset($fb_data->birthday)) {
			$birth_stamp = strtotime($fb_data->birthday);
			$age = $this->computeAge($birth_stamp, time());
			$this->model->saveVariable('age', $age);
		}

		if (isset($fb_data->gender)) {
			$gender = $fb_data->gender;
			$gender = ($gender == 'woman' ? 'female' : 'male');
			$this->model->saveVariable('gender', $gender);
		}

		return true;
	}

	public function computeAge($starttime, $endtime) {
		$age = date('Y', $endtime) - date('Y', $starttime);

		//if birthday didn't occur that last year, then decrement
		if (date('z', $endtime) < date('z', $starttime)) $age--;

		return $age;
	}

}