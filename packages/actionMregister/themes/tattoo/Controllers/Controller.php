<?php

namespace packages\actionMregister\themes\tattoo\controllers;

use packages\actionMregister\themes\tattoo\Views\Main;
use packages\actionMregister\themes\tattoo\Views\View as ArticleView;
use packages\actionMregister\themes\tattoo\Models\Model as ArticleModel;

use moosend;
use moosend\Models;

class Controller extends \packages\actionMregister\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        if ($this->model->sessionGet('reg_phase') == 2) {
            return $this->actionPagetwo();
        }

        $this->model->rewriteActionConfigField('background_color', '#edac34');
        $data['mode'] = 'show';
        $data['fieldlist'] = $this->model->getFieldlist();

        if (strstr($this->getMenuId(), 'set_role')) {
            $role = str_replace('set_role_', '', $this->getMenuId());
            $this->model->saveVariable('role', $role);

            $this->model->sessionSet('reg_phase', 2);
            return $this->actionPagetwo();
        }



        return ['PickRole', $data];
    }

    public function actionPagetwo()
    {
    	
        $this->model->rewriteActionConfigField('background_color', '#edac34');
        /* if user has already completed the first phase, move to phase 2 */

	    if ( $this->model->getSavedVariable('fb_token') ) {
		    $facebookData = \ThirdpartyServices::getCompleteFBInfo($this->model->getSavedVariable('fb_token'));
		    $this->setExtraProfileData($facebookData);
	    }

        $data['fieldlist'] = $this->model->getFieldlist();
        $data['current_country'] = $this->model->getCountry();
        $data['mode'] = 'show';

        /* if user has clicked the signuop, we will first validate
        and then save the data. validation errors are also available to views and components. */
        if ($this->getMenuId() == 'signup') {

            $this->model->validatePage1();

            // TODO: refactor validation checks
            $this->model->validateMyEmail();

            if (!$this->model->getSavedVariable('profilepic')) {
                $this->model->validation_errors['profilepic'] = '{#please_add_a_profile_picture#}';
            }

            if (!$this->model->getSubmittedVariableByName('terms')) {
                $this->model->validation_errors['terms'] = '{#please_accept_the_terms_and_conditions#}';
            }
            
            if (
                !$this->model->getSavedVariable('address') AND
                (
	                $this->model->getSavedVariable('role') == 'artist' &&
	                !$this->model->getSubmittedVariableByName('address')
                )
            ) {
                $this->model->validation_errors['address'] = '{#please_add_an_address#}';
            }

            if ($this->model->getSavedVariable('role') == 'artist') {
                if (!$this->model->getSubmittedVariableByName('price')) {
                    $this->model->validation_errors['price'] = '{#please_add_hourly_price_rate#}';
                } else {
                    if ($this->model->getSubmittedVariableByName('price') < 0) {
                        $this->model->validation_errors['price'] = '{#hourly_price_rate_needs_to_be_positiv#}';
                    }
                    if (!is_numeric($this->model->getSubmittedVariableByName('price'))) {
                        $this->model->validation_errors['price'] = '{#hourly_price_rate_needs_to_be_whole_number#}';
                    }

                    if (is_numeric($this->model->getSubmittedVariableByName('price'))
                        && floor($this->model->getSubmittedVariableByName('price'))
                        != $this->model->getSubmittedVariableByName('price')) {
                        $this->model->validation_errors['price'] = '{#hourly_price_rate_needs_to_be_whole_number#}';
                    }
                }
            }

            if (empty($this->model->validation_errors)) {

            	// Handle the submitted address
	            $address_data = $this->model->getSubmittedVariableByName('address');
	            $this->model->saveAddressData( $address_data );

                $this->model->savePage1();

                $this->model->saveVariable('price', $this->model->getSubmittedVariableByName('price'));

                $maillist = '636ba48c-7367-46bc-b4a2-7e36f83fdde8';
                if ($this->model->getSavedVariable('role') == 'artist') {
                    $maillist = '7a413cd0-20c0-43d3-b3d5-d4ed2d0f7f3d';
                }

                $moosend = new moosend\MoosendApi('17cff209889e45a4831f0c3f87f505ee');
                $params = new moosend\Models\SubscriberParams();

                $params->email = $this->model->getSavedVariable('email');
                $params->name = $this->model->getSavedVariable('real_name');

                $moosend->subscribers->addSubscriber($maillist, $params);

                /* if validation succeeds, we save data to variables and move user to page 2*/
                $this->model->closeLogin();
                $data['mode'] = 'close';
            }
        }

        return ['View', $data];
    }

}